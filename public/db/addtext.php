<?php
session_start();

if (isset($_POST['title']) && isset($_POST['content'])) {
  require_once('dbinit.php'); // connect to database

  $actlangid = $_SESSION['actlangid'];
  $title = mysqli_real_escape_string($con, $_POST['title']);
  $author = mysqli_real_escape_string($con, $_POST['author']);
  $link = mysqli_real_escape_string($con, $_POST['link']);
  $content = mysqli_real_escape_string($con, $_POST['content']);

  $result = mysqli_query($con, "INSERT INTO texts (textLgID, textTitle, textAuthor, text, textSourceURI)
            VALUES ('$actlangid', '$title', '$author', '$content', '$link')") or die(mysqli_error($con));

  // if successful, return insert_id in json format
  $arr = array('insert_id' => mysqli_insert_id($con));
  echo json_encode($arr);
}
?>