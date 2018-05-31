<?php
  require_once('dbinit.php'); // connect to database
  $actlangid = $_COOKIE['actlangid'];
  $result = mysqli_query($con, "SELECT * FROM languages WHERE LgID = '$actlangid'") or die(mysqli_error($con));
  $row = mysqli_fetch_assoc($result);
  echo json_encode($row);
