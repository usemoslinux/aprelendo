<?php 
/**
 * Copyright (C) 2018 Pablo Castagnino
 * 
 * This file is part of aprelendo.
 * 
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/classes/texts.php'); // loads Texts class
require_once(PUBLIC_PATH . '/classes/sharedtexts.php'); // loads SharedTexts class
require_once(PUBLIC_PATH . '/classes/files.php'); // loads Files class
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

try {
    //catch file overload error...
    if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
        $post_max_size = ini_get('post_max_size'); //grab the size limits...
        throw new Exception("Please note that posts larger than $post_max_size will result in this error!" .
            "<br>This is a limitation of the hosting server." .
            "<br>If you have access to the php ini file you can fix this by changing the <code>post_max_size</code> setting." .
            "<br>If you can't, please ask your host to increase the size limits.");
    } else {
        switch ($_POST['mode']) {
            case 'simple':
            case 'video':
            if (isset($_POST['title']) && isset($_POST['text'])) {
                $title = $_POST['title'];
                $author = $_POST['author'];
                $source_url = $_POST['url'];
                $text = $_POST['text'];
                $type = $_POST['type'];
                $is_shared = $_POST['mode'] == 'video' || isset($_POST['shared-text']) ? true : false;
                $target_file_name = '';
                $errors = [];
                
                // initialize text table
                if ($is_shared) {
                    $texts_table = new SharedTexts($con, $user_id, $learning_lang_id);
                } else {
                    $texts_table = new Texts($con, $user_id, $learning_lang_id);
                }
                
                // check if required fields are set
                if (!isset($title) || empty($title)) {
                    $errors[] = "<li>Title is a required field. Please enter one and try again.</li>";
                }
                
                /*if (!isset($author) || empty($author)) {
                    $errors[] = "<li>Author is a required field. Please enter one and try again.</li>";
                }*/
                
                if (!isset($text) || empty($text)) {
                    $errors[] = "<li>Text is a required field. Please enter one and try again. In case you
                    are uploading a video, enter a valid YouTube URL and fetch the correct transcript. 
                    Only videos with subtitles in your target language are supported.</li>";
                }
                
                // check if text is longer than the max. number of chars allowed
                $xml_text = $texts_table->extractTextFromXML($text);
                
                if ($xml_text != false) {
                    if (strlen($xml_text) > 20000) {
                        $errors[] = "<li>Maximum supported text length is 20.000 characters.</li>";
                    }    
                } else {
                    if (strlen($text) > 20000) {
                        $errors[] = "<li>Maximum supported text length is 20.000 characters.</li>";
                    }
                }
                
                // Audio file validation
                if (empty($errors)) {
                    if (isset($_FILES['audio']) && !empty($_FILES['audio']) && $_FILES['audio']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $audio_file = new AudioFile($user->premium_until !== NULL);
                        $file_uploaded = $audio_file->put($_FILES['audio']);
                        $target_file_name = $audio_file->file_name;
                    }
                    // save text in db
                    if (!empty($_POST['id'])) {
                        $id = $_POST['id'];
                        $result = $texts_table->update($id, $title, $author, $text, $source_url, $target_file_name, $type);
                    } else {
                        $result = $texts_table->add($title, $author, $text, $source_url, $target_file_name, $type);
                    }
                    
                    if ($result) {
                        // if everything goes fine return HTTP code 204 (No content), as nothing is returned 
                        http_response_code(204);
                    } else { // in case of error, show message
                        throw new Exception ('Oops! There was an unexpected error when uploading this text.');
                    }
                } else {
                    $error_str = '<ul>' . implode("<br>", $errors) . '</ul>'; // show upload errors
                    throw new Exception ($error_str);    
                }
            }
            break; // end of simple text or video
            
            case 'rss':
            if (isset($_POST['title']) && isset($_POST['text'])) {
                $title = $_POST['title'];
                $author = $_POST['author'];
                $source_url = $_POST['url'];
                $text = $_POST['text'];
                
                $texts_table = new SharedTexts($con, $user_id, $learning_lang_id);
                
                // if successful, return insert_id in json format
                if ($texts_table->add($title, $author, $text, $source_url, '', '1')) {
                    $arr = array('insert_id' => $con->insert_id);
                    echo json_encode($arr);
                }
            }
            break; // end of rss
            
            case 'ebook':
            if(!isset($_POST['title']) || !isset($_POST['author']) || !isset($_FILES['url'])) {
                throw new Exception('Please, complete all the required fields: name, author & epub file.');
            } else {
                $title = $_POST['title'];
                $author = $_POST['author'];
                $type = 6; // 6 = ebook
                $audio_uri = '';
                $target_file_name = '';
                $text = null;

                if (isset($_FILES['url']) && $_FILES['url']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $ebook_file = new EbookFile($user->premium_until !== NULL);
                    $ebook_file->put($_FILES['url']);
                    $target_file_name = $ebook_file->file_name;
                }

                // save text in db
                $texts_table = new Texts($con, $user_id, $learning_lang_id);
                if (!empty($_POST['id'])) {
                    $id = $_POST['id'];
                    $result = $texts_table->update($id, $title, $author, $text, $target_file_name, $audio_uri, $type);
                } else {
                    $result = $texts_table->add($title, $author, $text, $target_file_name, $audio_uri, $type);
                }
                
                if ($result) {
                    // if everything goes fine return HTTP code 204 (No content), as nothing is returned 
                    http_response_code(204);
                } else { // in case of error, show message
                    throw new Exception ('Oops! There was an unexpected error when uploading this text.');
                }

            }
            
            default:
            break;
        }
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}


?>