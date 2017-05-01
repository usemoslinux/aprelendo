<?php
if (isset($_POST['word'])) {
  require_once 'connect.php'; // connect to database
  require_once 'functions.php';

  $word = mysqli_real_escape_string($con, $_POST['word']);
  $status = 2;

  $result = mysqli_query($con, "REPLACE INTO words (word, wordStatus) VALUES ('$word', '$status')") or die(mysqli_error($con));
}
?>
