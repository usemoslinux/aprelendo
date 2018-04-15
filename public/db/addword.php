<?php
if (isset($_POST['word'])) {
  require_once('dbinit.php'); // connect to database

  $word = mysqli_real_escape_string($con, $_POST['word']);
  $status = 2;
  $isphrase = $_POST['isphrase'];

  $result = mysqli_query($con, "REPLACE INTO words (word, wordStatus, isPhrase)
            VALUES ('$word', $status, $isphrase)") or die(mysqli_error($con));
}
?>
