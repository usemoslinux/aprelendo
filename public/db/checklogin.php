<?php 
  require_once('dbinit.php'); // connect to database
  require_once(PUBLIC_PATH . 'classes/users.php');

  $user = new User($con);
  
  if (!$user->isLoggedIn()) {
    header('Location:/login.php');
  }
