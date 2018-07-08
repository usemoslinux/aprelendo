<?php
  require_once('dbinit.php'); // connect to database
  require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in

  $user_id = $user->id;
  $learning_lang_id = $user->learning_lang_id;

  $result = $con->query("SELECT * FROM languages WHERE LgUserId='$user_id' AND LgID = '$learning_lang_id'") or die(mysqli_error($con));
  $row = $result->fetch_assoc();
  echo json_encode($row);
