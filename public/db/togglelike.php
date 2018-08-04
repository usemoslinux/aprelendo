<?php
require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in
require_once(PUBLIC_PATH . '/classes/likes.php'); // loads Likes class

try {
    if ($_POST['id']) {
        $like = new Likes($con, $_POST['id'], $user->id, $user->learning_lang_id);
        $result = $like->toggle();
        if (!$result) {
            throw new Exception('Oops! There was a problem trying to give a like to that text.');
        }
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>
