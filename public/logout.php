<?php
  require_once('../includes/dbinit.php'); // connect to database

  use Aprelendo\Includes\Classes\User;

  $user = new User($con);
  
  $user->logout(false);
?>