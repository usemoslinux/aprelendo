<?php 
require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/classes/texts.php'); // loads Texts class
require_once(PUBLIC_PATH . '/classes/sharedtexts.php'); // loads Texts class
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

try {
    if ($_POST['mode'] == 'rss') { // add rss entry
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
    } else if ($_POST['mode'] == 'simple' || $_POST['mode'] == 'video') { // add simple text, shared text or video
        if (isset($_POST['title']) && isset($_POST['text'])) {
            $title = $_POST['title'];
            $author = $_POST['author'];
            $source_url = $_POST['url'];
            $text = $_POST['text'];
            $type = $_POST['type'];
            $is_shared = isset($_POST['shared-text']) ? true : false;
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

            // if (!isset($author) || empty($author)) {
            //     $errors[] = "<li>Author is a required field. Please enter one and try again.</li>";
            // }

            if (!isset($text) || empty($text)) {
                $errors[] = "<li>Text is a required field. Please enter one and try again. <br>In case you
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
            if (isset($_FILES['audio']) && $_FILES['audio']['error'] !== UPLOAD_ERR_NO_FILE) {
                $target_dir = PRIVATE_PATH . 'uploads/'; //PUBLIC_PATH . 'uploads/'; //APP_ROOT . '/public/uploads/';
                $file_name = basename($_FILES['audio']['name']);
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                
                $target_file_name = uniqid() . '.' . $file_extension; // create unique filename for audio file
                $target_file_URI = $target_dir . $target_file_name;
                
                $file_size = $_FILES['audio']['size'] / 1024; // size in KBs
                $upload_max_filesize = ini_get('upload_max_filesize'); // max file size
                $allowed_extensions = array('mp3', 'ogg');
                                
                // Check if file exists
                if (file_exists($target_file_URI)) {
                    $errors[] = "<li>File already exists. Please try again later.</li>";
                }
                
                // Check file size
                if ($_FILES['audio']['error'] == UPLOAD_ERR_INI_SIZE) {
                    $errors[] = "<li>File size should be less than $upload_max_filesize<br>" .
                    "This is a limitation of the hosting server.<br>" .
                    "If you have access to the php ini file you can fix this by changing the <code>upload_max_filesize</code> setting.<br>" .
                    "If you can't, please ask your host to increase the size limits.</li>";
                }
                
                // Check file extension
                $allowed_ext = false;
                for ($i=0; $i < sizeof($allowed_extensions); $i++) {
                    if (strcasecmp($allowed_extensions[$i], $file_extension) == 0) {
                        $allowed_ext = true;
                    }
                }
                
                if (!$allowed_ext) {
                    $errors[] = '<li>Only the following file types are supported: ' . implode(', ', $allowed_extensions) . "</li>";
                }
                
                // upload file & save info to db
                if ($_FILES['audio']['error'] == UPLOAD_ERR_OK && empty($errors)) {
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir);
                    }
                    // try to move file to uploads folder. If this fails, show error message
                    if (!move_uploaded_file($_FILES['audio']['tmp_name'], $target_file_URI)) {
                        $errors[] = "<li>Sorry, there was an error uploading your file.</li>";
                    }
                }
            } elseif (isset($_FILES['audio']) && $_FILES['audio']['error'] == UPLOAD_ERR_INI_SIZE) {
                $errors[] = "<li>File size should be less than $upload_max_filesize.</li>";
            }
            
            if (empty($errors)) {
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
                $error_str = '<ul>' . implode("<br>", $errors) . '</ul>';
                throw new Exception ($error_str);
            }
            
            // //catch file overload error...
            // if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
                //   $post_max_size = ini_get('post_max_size'); //grab the size limits...
                //   echo  "<div class='alert alert-danger'>Please note that posts larger than $post_max_size will result in this error!" .
                //         "<br>This is a limitation of the hosting server." .
                //         "<br>If you have access to the php ini file you can fix this by changing the <code>post_max_size</code> setting." .
                //         "<br>If you can't, please ask your host to increase the size limits.</div>";
                // }
        }
    }  // end simple text
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}


    ?>