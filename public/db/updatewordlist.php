<?php
require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

// if text is archived using green button at the end, update learning status of words
if (isset($_POST['words'])) {
    $wordslearnt = $_POST['words'];
    
    foreach($wordslearnt as $k => $v) { // escape strings to protect against SQL injection
        $wordslearnt[$k] = $con->real_escape_string($v);
    }
    
    $cvswords = join("','", $wordslearnt); // convert array to comma separated string
    
    // delete words with wordStatus = 1
    $con->query("DELETE FROM words WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND word IN ('$cvswords') AND wordStatus=1") or die(mysqli_error($con));
    
    // -1 to wordStatus if it's not a new word added to the db
    $con->query("UPDATE words SET wordStatus=wordStatus-1 WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND word IN ('$cvswords') ") or die(mysqli_error($con));
}

?>