<?php
require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in
require_once(PUBLIC_PATH . '/classes/files.php'); // loads File, AudioFile & EbookFile classes

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;
$id = $con->real_escape_string($_POST['id']);

try {
    $result = $con->query("SELECT textSourceURI FROM texts WHERE textId='$id' AND textUserId='$user_id' AND textLgId = '$learning_lang_id'") or die(mysqli_error($con));
    
    if ($result) {
        $row = $result->fetch_assoc();
        $ebook_file = new EbookFile();
        $ebook_content = $ebook_file->get($row['textSourceURI']);
        if ($ebook_content != false) {
            return $ebook_content;
        } else {
            throw new Exception (404);
        }
    } else {
        throw new Exception (404);
    }
    
    
} catch (Exception $e) {
    http_response_code($e->getMessage());
}
