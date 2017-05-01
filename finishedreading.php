<?php

  require_once 'connect.php'; // connect to database

  $sts = join("','", $_POST['words']); // convert array to comma separated string
  //$sts = mysqli_real_escape_string($con, $sts); >>>> TODO: protect against SQL injection

  // delete words with wordStatus = 1
  mysqli_query($con, "DELETE FROM words WHERE word IN ('$sts') AND wordStatus=1") or die(mysqli_error($con));

  // -1 to wordStatus if it's not a new word added to the db
  mysqli_query($con, "UPDATE words SET wordStatus=wordStatus-1 WHERE word IN ('$sts') ") or die(mysqli_error($con));

 ?>
