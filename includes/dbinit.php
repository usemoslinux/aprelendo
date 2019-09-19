<?php
/**
 * Copyright (C) 2018 Pablo Castagnino
 * 
 * This file is part of aprelendo.
 * 
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

// perform all database initialization here, in a single file

defined('APP_ROOT')     ? null : define('APP_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
defined('PUBLIC_PATH')  ? null : define('PUBLIC_PATH', APP_ROOT . 'public' . DIRECTORY_SEPARATOR);

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/autoload.php';

use Aprelendo\Includes\Classes\Connect;

try {
    $db_connection = new Connect;
    $con = $db_connection->connect();
} catch (Exception $e) {
    header('Location:error.php');
    exit;
}

?>
