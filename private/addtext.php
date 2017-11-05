<?php
if (isset($_POST['submit'])) {
  require_once('../private/init.php'); // connect to database

  $text = mysqli_real_escape_string($con, $_POST['text']);
  $title = mysqli_real_escape_string($con, $_POST['title']);
  $result = mysqli_query($con, "INSERT INTO texts (textTitle, text) VALUES ('$title', '$text') ") or die(mysqli_error($con));
  header('Location: /');
}
?>
