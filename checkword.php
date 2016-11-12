<?php
if (isset($_POST['word'])) {
  include 'connect.php'; // connect to database

  $word = mysqli_real_escape_string($con, $_POST['word']);

  $result = mysqli_query($con, "SELECT word, wordTranslation, tags FROM words WHERE word = '$word' ") or die(mysqli_error($con));
  mysqli_set_charset($con, 'utf8');

  $row = mysqli_fetch_assoc($result);
  echo json_encode($row);
}
?>
