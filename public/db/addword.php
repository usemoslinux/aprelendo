<?php

if (isset($_POST['word'])) {
    require_once('dbinit.php'); // connect to database
    require_once(PUBLIC_PATH . '/classes/words.php'); // loads Words class
    require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in
    
    $user_id = $user->id;
    $learning_lang_id = $user->learning_lang_id;
    
    $word = $_POST['word'];
    $status = 2;
    $isphrase = $_POST['isphrase'];
    
    try {
        $words_table = new Words($con, $user_id, $learning_lang_id);
        $result = $words_table->add($word, $status, $isphrase);

        if (!$result) {
            throw new Exception ('Oops! There was an unexpected error trying to add this word.');
        }
    } catch (Exception $e) {
        $error = array('error_msg' => $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode($error);
    }
    
}
?>