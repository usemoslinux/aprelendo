<?php
  require_once('db/dbinit.php'); // connect to database
  require_once('classes/users.php');

  $user = new User($con);
  
  $user->logout();
?>