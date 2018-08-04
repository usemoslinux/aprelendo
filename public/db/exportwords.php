<?php
require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/classes/words.php'); // loads Words class
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

try {
    // set search criteria, if any
    $search_text = isset($_GET['s']) ? $_GET['s'] : '';
    $order_by = isset($_GET['o']) ? $_GET['o'] : '';

    // export to csv
    $words_table = new Words($con, $user_id, $learning_lang_id);
    $result = $words_table->createCSVFile($search_text, $order_by);

    if (!$result) {
        throw new Exception ('There was an unexpected error trying to export your word list');
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>
