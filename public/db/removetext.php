<?php

if (isset($_POST['idText'])) {
  require_once('dbinit.php'); // connect to database

  $id = mysqli_real_escape_string($con, $_POST['idText']);

  // decide wether we are deleting an archived text or not
  $referer = basename($_SERVER['HTTP_REFERER']);
  if ($referer == 'archivedtexts.php') {
    $selectsql = "SELECT atextAudioURI FROM archivedtexts WHERE atextID='$id'";
    $deletesql = "DELETE FROM archivedtexts WHERE atextID='$id'";
    $audiouri = 'atextAudioURI';
  } else {
    $selectsql = "SELECT textAudioURI FROM texts WHERE textID='$id'";
    $deletesql = "DELETE FROM texts WHERE textID='$id'";
    $audiouri = 'textAudioURI';
  }

  // check if there is an audio file associated to this text and store its URI
  $result = mysqli_query($con, $selectsql) or die(mysqli_error($con));
  $row = mysqli_fetch_assoc($result);
  $filename = $row[$audiouri];

  // delete entry from db
  $deletedfromdb = mysqli_query($con, $deletesql) or die(mysqli_error($con));

  // if there is an audio file associated to this text, delete it
  if ($deletedfromdb && isset($filename)) {
    $filename = APP_ROOT . '/public' . $filename;
    unlink($filename);
  }
}
