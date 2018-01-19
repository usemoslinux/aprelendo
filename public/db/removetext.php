<?php

  if (isset($_POST['idText'])) {
    require_once('dbinit.php'); // connect to database

    $id = mysqli_real_escape_string($con, $_POST['idText']);

    // check if there is an audio file associated to this text and store its URI
    $result = mysqli_query($con, "SELECT textAudioURI FROM texts WHERE textID='$id'") or die(mysqli_error($con));
    $row = mysqli_fetch_assoc($result);
    $filename = $row['textAudioURI'];

    // delete entry from db
    $deletedfromdb = mysqli_query($con, "DELETE FROM texts WHERE textID='$id'") or die(mysqli_error($con));

    // if there is an audio file associated to this text, delete it
    if ($deletedfromdb && isset($filename)) {
      $filename = APP_ROOT . '/public' . $filename;
      unlink($filename);
    }
  }

?>
