<?php

// perform all initialization here, in a single private file

define('APP_ROOT', dirname(dirname(__FILE__)));
define('PRIVATE_PATH', APP_ROOT . '/private/');
define('PUBLIC_PATH', APP_ROOT . '/public/');

require_once(PRIVATE_PATH . 'passwords.php');
require_once(PRIVATE_PATH . 'db_connect.php');
require_once(PRIVATE_PATH . 'functions.php');

?>
