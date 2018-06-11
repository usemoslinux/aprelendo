<?php
  require_once('dbinit.php'); // connect to database
  $user = 1; // TODO: add user support
  $result = mysqli_query($con, "SELECT prefActLangId FROM preferences WHERE prefUserId = '$user'") or die(mysqli_error($con));
  $actlangid = mysqli_fetch_array($result);
