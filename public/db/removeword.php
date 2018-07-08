<?php
require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/classes/words.php'); // loads Words class
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

try{
    if (isset($_POST['word'])) { // deletes word by 'name'; used by showtext.php
        $words_table = new Words($con, $user_id, $learning_lang_id);
        $result = $words_table->deleteByName($_POST['word']);
    } elseif (isset($_POST['wordIDs'])) { // deletes word by id; used by listwords.php
        $words_table = new Words($con, $user_id, $learning_lang_id);
        $result = $words_table->deleteByIds($_POST['wordIDs']);
    }

    if (!$result) {
        throw new Exception ('There was an unexpected error trying to remove this word');
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>
