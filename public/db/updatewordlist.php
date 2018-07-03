<?php
  require_once('dbinit.php'); // connect to database
  require_once('checklogin.php'); // check if user is logged in and set $user_id & $learning_lang_id

  $user_id = $user->id;
  $learning_lang_id = $user->learning_lang_id;

  // if text is archived using green button at the end, update learning status of words
  if (isset($_POST['words'])) {
    $wordslearnt = $_POST['words'];

    foreach($wordslearnt as $k => $v) { // escape strings to protect against SQL injection
      $wordslearnt[$k] = mysqli_real_escape_string($con, $v);
  }

  $cvswords = join("','", $wordslearnt); // convert array to comma separated string

  // delete words with wordStatus = 1
  mysqli_query($con, "DELETE FROM words WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND word IN ('$cvswords') AND wordStatus=1") or die(mysqli_error($con));

  // -1 to wordStatus if it's not a new word added to the db
  mysqli_query($con, "UPDATE words SET wordStatus=wordStatus-1 WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND word IN ('$cvswords') ") or die(mysqli_error($con));
}

?>