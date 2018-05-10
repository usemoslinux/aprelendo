<?php

  if (isset($_POST['word'])) {
    require_once('dbinit.php'); // connect to database

    $word = mysqli_real_escape_string($con, $_POST['word']);

    $result = mysqli_query($con, "DELETE FROM words WHERE word='$word'") or die(mysqli_error($con));
  } elseif (isset($_POST['wordIDs'])) {
    require_once('dbinit.php'); // connect to database

    $ids = json_decode($_POST['wordIDs']);
    foreach ($ids as $id) {
      $id = mysqli_real_escape_string($con, $id);
    }
    $wordIDs = implode(',', $ids);

    $id = mysqli_real_escape_string($con, $_POST['wordID']);
    $deletesql = "DELETE FROM words WHERE wordID IN ($wordIDs)";

    $result = mysqli_query($con, $deletesql) or die(mysqli_error($con));
  }

?>
