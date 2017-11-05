<?php

  if (isset($_POST['idText'])) {
    require_once('../private/init.php'); // connect to database

    $id = mysqli_real_escape_string($con, $_POST['idText']);

    $result = mysqli_query($con, "DELETE FROM texts WHERE textID='$id'") or die(mysqli_error($con));
  }

?>
