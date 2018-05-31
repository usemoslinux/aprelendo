<?php

if (isset($_POST['word'])) {
  require_once('dbinit.php'); // connect to database

  $word = mysqli_real_escape_string($con, $_POST['word']);
  $status = 2;
  //$isphrase = filter_var($_POST['isphrase'], FILTER_VALIDATE_BOOLEAN);
  $isphrase = $_POST['isphrase'];
  $lgid = $_COOKIE['actlangid'];

  $result = mysqli_query($con, "REPLACE INTO words (wordLgId, word, wordStatus, isPhrase)
            VALUES ($lgid, '$word', $status, $isphrase)") or die(mysqli_error($con));
}
?>
