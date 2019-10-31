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

require_once '../../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & checks if user is logged in

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\Includes\Classes\Texts;
use Aprelendo\Includes\Classes\SharedTexts;
use Aprelendo\Includes\Classes\EbookFile;
use Aprelendo\Includes\Classes\LogFileUploads;

$user_id = $user->getId();
$lang_id = $user->getLangId();

try {
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
                $texts_table = new SharedTexts($pdo, $user_id, $lang_id);
            } else {
                $texts_table = new Texts($pdo, $user_id, $lang_id);
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
            $xml_text = $texts_table->extractFromXML($text);
            
            if ($xml_text != false) {
                if (strlen($xml_text) > 20000) {
                    $errors[] = "<li>Maximum supported text length is 20.000 characters.</li>";
                }    
            } else {
                if (strlen($text) > 20000) {
                    $errors[] = "<li>Maximum supported text length is 20.000 characters.</li>";
                }
            }
            
            
            // save text in db
            if (empty($errors)) {
                // Audio file validation
                // if (isset($_FILES['audio']) && !empty($_FILES['audio']) && $_FILES['audio']['error'] !== UPLOAD_ERR_NO_FILE) {
                //     $audio_file = new AudioFile($user->isPremium());
                //     $file_uploaded = $audio_file->put($_FILES['audio']);
                //     $target_file_name = $audio_file->getName();
                // }
                
                if (!empty($_POST['id'])) {
                    $id = $_POST['id'];
                    $texts_table->update($id, $title, $author, $text, $source_url, $target_file_name, $type);
                } else {
                    if ($texts_table->exists($source_url)) {
                        $msg = 'The text you are trying to add already exists in our database. ';
                        $msg .= $is_shared ? 'Look for it in the <a href="sharedtexts.php">shared texts</a> section.' : 'Look for it in your <a href="texts.php">private library</a>.';

                        throw new \Exception($msg);
                    }
                    $texts_table->add($title, $author, $text, $source_url, $target_file_name, $type);
                }
                
                // if everything goes fine return HTTP code 204 (No content), as nothing is returned 
                http_response_code(204);
            } else {
                $error_str = '<ul>' . implode("<br>", $errors) . '</ul>'; // show upload errors
                throw new \Exception($error_str);    
            }
        }
        break; // end of simple text or video
        
        case 'rss':
        if (isset($_POST['title']) && isset($_POST['text'])) {
            $title = $_POST['title'];
            $author = $_POST['author'];
            $source_url = $_POST['url'];
            $text = $_POST['text'];

            $texts_table = new SharedTexts($pdo, $user_id, $lang_id);

            // if text is already in db, show error message
            if ($texts_table->exists($source_url)) {
                $msg = 'The text you are trying to add already exists in our database. ';
                $msg .= 'Look for it in the <a href="sharedtexts.php">shared texts</a> section.';

                throw new \Exception($msg);
            }
            
            // if successful, return insert_id in json format
            $insert_id = $texts_table->add($title, $author, $text, $source_url, '', '1');
            if ($insert_id > 0) {
                $arr = array('insert_id' => $insert_id);
                echo json_encode($arr);
            }
        }
        break; // end of rss
        
        case 'ebook':
        if(!isset($_POST['title']) || !isset($_POST['author']) || !isset($_FILES['url'])) {
            throw new \Exception('Please, complete all the required fields: name, author & epub file.');
        } else {
            $title = $_POST['title'];
            $author = $_POST['author'];
            $type = 6; // 6 = ebook
            $audio_uri = '';
            $target_file_name = '';
            $text = '';

            // check if file exists
            if (!isset($_FILES['url']) || $_FILES['url']['error'] === UPLOAD_ERR_NO_FILE) {
                throw new \Exception('No file found. Please select a file to upload.');
            }

            // check if user is allowed to upload file & does not exceed the daily upload limit
            $file_upload_log = new LogFileUploads($pdo, $user->getId());
            $nr_of_uploads_today = $file_upload_log->countTodayRecords();
            $premium_user = $user->isPremium();

            if ((!$premium_user) || ($premium_user && $nr_of_uploads_today >= 1)){
                throw new \Exception('Sorry, you have reached your file upload limit for today.');
            }

            // upload file & create unique file name
            $ebook_file = new EbookFile($_FILES['url']['name'], $user->isPremium());
            $ebook_file->put($_FILES['url']);
            $target_file_name = $ebook_file->getName();

            // save text in db
            $texts_table = new Texts($pdo, $user_id, $lang_id);
            $insert_id = $texts_table->add($title, $author, $text, $target_file_name, $audio_uri, $type);
            
            if ($insert_id > 0) {
                // if everything goes fine log upload & return HTTP code 204 (No content), as nothing is returned 
                $file_upload_log->addRecord();
                $filename = array('filename' => $target_file_name);
                header('Content-Type: application/json');
                echo json_encode($filename);
            } else { // in case of error, show message
                throw new \Exception('Oops! There was an unexpected error when uploading this text.');
            }
        }
        default:
            break;
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}


?>