<?php
require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

// save preferences to database
$fontfamily = isset($_POST['fontfamily']) ? $_POST['fontfamily'] : "Helvetica";
$fontsize = isset($_POST['fontsize']) ? $_POST['fontsize'] : '12pt';
$lineheight = isset($_POST['lineheight']) ? $_POST['lineheight'] : '1.5';
$alignment = isset($_POST['alignment']) ? $_POST['alignment'] : 'left';
$mode = isset($_POST['mode']) ? $_POST['mode'] : 'light';
$assistedlearning = isset($_POST['assistedlearning']) ? $_POST['assistedlearning'] : true;

try {
    $result = $con->query("REPLACE INTO preferences (prefUserId, prefFontFamily,
    prefFontSize, prefLineHeight, prefAlignment, prefMode, prefAssistedLearning)
    VALUES ('$user_id', '$fontfamily', '$fontsize', '$lineheight', '$alignment', '$mode', '$assistedlearning')");
    
    if (!$result) {
        throw new Exception ('There was an unexpected error trying to save your preferences. Please, try again later.');
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>