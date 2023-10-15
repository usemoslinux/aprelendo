<?php
/**
 * Copyright (C) 2019 Pablo Castagnino
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

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\Texts;
use Aprelendo\SharedTexts;
use Aprelendo\EbookFile;
use Aprelendo\LogFileUploads;
use Aprelendo\Gems;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    $text_added_successfully = false;
    switch ($_POST['mode']) {
        case 'simple':
        case 'video':
        if (isset($_POST['title']) && isset($_POST['text'])) {
            $title = $_POST['title'];
            $author = $_POST['author'];
            $source_uri = $_POST['url'];
            $audio_uri = isset($_POST['audio-url']) ? $_POST['audio-url'] : '';
            $text = $_POST['text'];
            $type = $_POST['type'];
            $level = isset($_POST['level']) ? $_POST['level'] : 2;  // default to 2 (intermediate) if not set
            $is_shared = $_POST['mode'] == 'video' || isset($_POST['shared-text']) ? true : false;
            $errors = [];
            
            // initialize text table
            if ($is_shared) {
                $texts_table = new SharedTexts($pdo, $user_id, $lang_id);
            } else {
                $texts_table = new Texts($pdo, $user_id, $lang_id);
            }
            
            // check if required fields are set
            if (empty($title)) {
                $errors[] = "<li>Title is a required field. Please enter one and try again.</li>";
            }
                        
            if (empty($text)) {
                $errors[] = "<li>Text is a required field. Please enter one and try again. In case you
                are uploading a video, enter a valid YouTube URL and fetch the correct transcript.
                Only videos with subtitles in your target language are supported.</li>";
            }

            // check if audio file exists or is accessible
            if (!empty($audio_uri)) {
                $headers = get_headers($audio_uri);
                if (stripos($headers[0], '200 OK') === false) {
                    $errors[] = "<li>The provided audio file cannot be accessed. Try another URL address.</li>";
                }
            }
            
            /*  For some reason new lines on the client side are counted by Jquery/JS as '\n',
                but on the server side the $_POST variable gets '\r\n' instead.
                To make them both compatible, we need to eliminate all instances of '\r' */
            if ($_POST['mode'] == 'simple') {
                $text = str_replace("\r", '', $text);
            }
            
            // save text in db
            if (empty($errors)) {
                if (!empty($_POST['id'])) {
                    $id = $_POST['id'];
                    $texts_table->update($id, [$title, $author, $text, $source_uri, $audio_uri, $type]);
                } else {
                    if ($texts_table->exists($source_uri)) {
                        $msg = 'The text you are trying to add already exists. ';
                        $msg .= $is_shared
                            ? 'Look for it in the <a class="alert-link" href="/sharedtexts">shared texts</a> section.'
                            : 'Look for it in your <a class="alert-link" href="/texts">private library</a>. '
                            . 'Remember that you may have <a class="alert-link" href="/texts?sa=1">archived</a> it.';

                        throw new UserException($msg);
                    }
                    $texts_table->add($title, $author, $text, $source_uri, $audio_uri, $type, $level);
                    $text_added_successfully = true;
                }
                
                // if everything goes fine return HTTP code 204 (No content), as nothing is returned
                http_response_code(204);
            } else {
                $error_str = '<ul>' . implode("<br>", $errors) . '</ul>'; // show upload errors
                throw new UserException($error_str);
            }
        }
        break; // end of simple text or video
        
        case 'rss':
        if (isset($_POST['title']) && isset($_POST['text'])) {
            $title = $_POST['title'];
            $author = $_POST['author'];
            $source_uri = $_POST['url'];
            $audio_uri = '';
            $type = 1; // assumes that all rss texts are "articles"
            $level = !empty($_POST['level']) ? $_POST['level'] : 2; // if not set, mark as "intermediate"
            $text = $_POST['text'];
            
            /*  For some reason new lines on the client side are counted by Jquery/JS as '\n',
            but on the server side the $_POST variable gets '\r\n' instead.
            To make them both compatible, we need to eliminate all instances of '\r' */
            $text = str_replace("\r", '', $text);

            $texts_table = new SharedTexts($pdo, $user_id, $lang_id);

            // if text is already in db, show error message
            if ($texts_table->exists($source_uri)) {
                $msg = 'The text you are trying to add already exists in our database. ';
                $msg .= 'Look for it in the <a class="alert-link" href="/sharedtexts">shared texts</a> section.';

                throw new UserException($msg);
            }
            
            // if successful, return insert_id in json format
            $insert_id = $texts_table->add($title, $author, $text, $source_uri, $audio_uri, $type, $level);
            if ($insert_id > 0) {
                $text_added_successfully = true;
                $arr = ['insert_id' => $insert_id];
                echo json_encode($arr);
            }
        }
        break; // end of rss
        
        case 'ebook':
        if (!isset($_POST['title']) || !isset($_POST['author']) || !isset($_FILES['url'])) {
            throw new UserException('Please, complete all the required fields: name, author & epub file.');
        } else {
            $title = $_POST['title'];
            $author = $_POST['author'];
            $type = 6; // 6 = ebook
            $level = !empty($_POST['level']) ? $_POST['level'] : 2; // if not set, mark as "intermediate"
            $audio_uri = $_POST['audio-uri'];
            $target_file_name = '';
            $text = '';

            // check if file exists
            if (!isset($_FILES['url']) || $_FILES['url']['error'] === UPLOAD_ERR_NO_FILE) {
                throw new UserException('File not found. Please select a file to upload.');
            }

            // check if user is allowed to upload file & does not exceed the daily upload limit
            $file_upload_log = new LogFileUploads($pdo, $user->id);
            $nr_of_uploads_today = $file_upload_log->countTodayRecords();

            if ($nr_of_uploads_today >= 1) {
                throw new UserException('Sorry, you have reached your file upload limit for today.');
            }

            // upload file & create unique file name
            $ebook_file = new EbookFile($_FILES['url']['name']);
            $ebook_file->put($_FILES['url']);
            $target_file_name = $ebook_file->name;

            // save text in db
            $texts_table = new Texts($pdo, $user_id, $lang_id);
            $insert_id = $texts_table->add($title, $author, $text, $target_file_name, $audio_uri, $type, $level);
            
            if ($insert_id > 0) {
                // if everything goes fine log upload
                $text_added_successfully = true;
                $file_upload_log->addRecord();
                $filename = ['filename' => $target_file_name];
                header('Content-Type: application/json');
                echo json_encode($filename);
            } else { // in case of error, show message
                throw new UserException('There was an error uploading this text.');
            }
        }
        default:
            break;
    }

    // if text was added with success, update user score (gems)
    if ($text_added_successfully) {
        $events = ['texts' => ['new' => 1]];
        $gems = new Gems($pdo, $user_id, $lang_id, $user->time_zone);
        $new_gems = $gems->updateScore($events);
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
