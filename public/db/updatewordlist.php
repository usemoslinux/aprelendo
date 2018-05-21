<?php

require_once 'dbinit.php'; // connect to database

// if text is archived using green button at the end, update learning status of words
if (isset($_POST['words'])) {
  $wordslearnt = $_POST['words'];

  foreach($wordslearnt as $k => $v) { // escape strings to protect against SQL injection
    $wordslearnt[$k] = mysqli_real_escape_string($con, $v);
  }

  $cvswords = join("','", $wordslearnt); // convert array to comma separated string

  // delete words with wordStatus = 1
  mysqli_query($con, "DELETE FROM words WHERE word IN ('$cvswords') AND wordStatus=1") or die(mysqli_error($con));

  // -1 to wordStatus if it's not a new word added to the db
  mysqli_query($con, "UPDATE words SET wordStatus=wordStatus-1 WHERE word IN ('$cvswords') ") or die(mysqli_error($con));
}

?>