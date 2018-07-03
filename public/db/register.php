<?php 
  require_once('dbinit.php'); // connect to database
  require_once('../classes/users.php');

  if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])) {
    $user = new User($con);
    $result = $user->register($_POST['username'], $_POST['email'], $_POST['password'], $_POST['native_lang'], $_POST['learning_lang']);
    if ($result) {
      $user->createRememberMeCookie($_POST['username'], $_POST['password']);
    } else {
      exit($user->showJSONError());
    }    
  } else {
    exit($user->showJSONError('Either username, email or password were not provided. Please try again.'));
  }

?>