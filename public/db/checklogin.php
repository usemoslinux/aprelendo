<?php 
require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . 'classes/users.php'); // load Users class

$user = new User($con);

if (!$user->isLoggedIn()) {
    header('Location:/login.php');
    exit;
}
