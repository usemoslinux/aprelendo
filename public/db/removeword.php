<?php
  require_once('dbinit.php'); // connect to database

  if (isset($_POST['word'])) { // deletes word by 'name'; used by showtext.php
    $word = mysqli_real_escape_string($con, $_POST['word']);

    $result = mysqli_query($con, "DELETE FROM words WHERE word='$word'") or die(mysqli_error($con));
  } elseif (isset($_POST['wordIDs'])) { // deletes word by id; used by listwords.php
    $ids = json_decode($_POST['wordIDs']);
    foreach ($ids as $id) {
      $id = mysqli_real_escape_string($con, $id);
    }
    $wordIDs = implode(',', $ids);

    $deletesql = "DELETE FROM words WHERE wordID IN ($wordIDs)";

    $result = mysqli_query($con, $deletesql) or die(mysqli_error($con));
  }

?>
