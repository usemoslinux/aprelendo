<?php

  if (isset($_POST['word'])) {
    require_once('../private/init.php'); // connect to database

    $word = mysqli_real_escape_string($con, $_POST['word']);

    $result = mysqli_query($con, "DELETE FROM words WHERE word='$word'") or die(mysqli_error($con));
  }

?>
