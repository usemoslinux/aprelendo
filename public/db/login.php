<?php 
  require_once('dbinit.php'); // connect to database
  require_once('../classes/users.php');
  if (isset($_POST['username']) && isset($_POST['password'])) {
    $user = new User($con);
    if (!$user->createRememberMeCookie($_POST['username'], $_POST['password'])) {
      exit($user->showJSONError());
    }
  } else {
    exit($user->showJSONError('Either username, email or password were not provided. Please try again.'));
  }

?>