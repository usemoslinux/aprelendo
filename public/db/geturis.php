<?php
  require_once('dbinit.php'); // connect to database
  require_once('checklogin.php'); // check if user is logged in and set $user_id & $learning_lang_id

  $user_id = $user->id;
  $learning_lang_id = $user->learning_lang_id;

  $result = mysqli_query($con, "SELECT * FROM languages WHERE LgUserId='$user_id' AND LgID = '$learning_lang_id'") or die(mysqli_error($con));
  $row = mysqli_fetch_assoc($result);
  echo json_encode($row);
