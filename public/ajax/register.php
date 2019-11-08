<?php 
/**
 * Copyright (C) 2019 Pablo Castagnino
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

require_once '../../includes/dbinit.php'; // connect to database

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\Includes\Classes\User;

try {
    // check username, email & password are set and not empty
    if (!isset($_POST['username']) || empty($_POST['username']) ||
        !isset($_POST['newpassword']) || empty($_POST['newpassword']) ||
        !isset($_POST['email'])    || empty($_POST['email'])) {
        throw new \Exception('Either username, email or password were not provided. Please try again.');
    }

    // check e-mail address is valid
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new \Exception('Invalid e-mail address. Please try again.');
    }

    // check password is valid
    $regex = '/(?=.*[0-9a-zA-Z])(?=.*[~`!@#$%^&*()\-_+={};:\[\]\?\.\/,]).{8,}/';
    if (!preg_match($regex, $_POST['newpassword'])) {
        throw new \Exception("Password must contain a letter, a special character and a digit. Password length must be minimum 8 characters. Please try again.");
    }

    // check password & password confirmation match
    if ($_POST['newpassword'] != $_POST['newpassword-confirmation']) {
        throw new \Exception("Passwords don't match. Please try again.");
    }
    
    $user = new User($pdo);
    $user->register($_POST['username'], $_POST['email'], $_POST['newpassword'], $_POST['native-lang'], $_POST['learning-lang'], true);
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>