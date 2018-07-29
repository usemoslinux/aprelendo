<?php

// perform all database initialization here, in a single file

define('APP_ROOT', dirname(dirname(dirname(__FILE__))));
define('PRIVATE_PATH', APP_ROOT . '/private/');
define('PUBLIC_PATH', APP_ROOT . '/public/');

require_once(PRIVATE_PATH . 'passwords.php');
require_once(PUBLIC_PATH . '/classes/connect.php'); // connect to database

try {
    $db_connection = new Connect;
    $con = $db_connection->connect();
} catch (Exception $e) {
    header('location:error.php');
}

?>
