<?php

// perform all database initialization here, in a single file

define('APP_ROOT', dirname(dirname(dirname(__FILE__))));
define('PRIVATE_PATH', APP_ROOT . '/private/');

require_once(PRIVATE_PATH . 'passwords.php');
require_once('dbconnect.php');

?>
