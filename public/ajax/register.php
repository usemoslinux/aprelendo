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

require_once '../../Includes/dbinit.php'; // connect to database

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\User;
use Aprelendo\UserRegistrationManager;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    // check username, email & password are set and not empty
    if (!isset($_POST['username']) || empty($_POST['username']) ||
        !isset($_POST['newpassword']) || empty($_POST['newpassword']) ||
        !isset($_POST['email'])    || empty($_POST['email'])) {
        throw new UserException('Either username, email or password were not provided. Please try again.');
    }

    // check e-mail address is valid
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new UserException('Invalid e-mail address. Please try again.');
    }

    // check password is valid
    $regex = '/(?=.*[0-9a-zA-Z])(?=.*[~`!@#$%^&*()\-_+={};:\[\]\?\.\/,]).{8,}/';
    if (!preg_match($regex, $_POST['newpassword'])) {
        throw new UserException("Password must have at least 8 characters and contain letters, special characters"
            . " and digits. Please try again.");
    }

    // check password & password confirmation match
    if ($_POST['newpassword'] != $_POST['newpassword-confirmation']) {
        throw new UserException("Passwords don't match. Please try again.");
    }
    
    $user_data = [
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'password' => $_POST['newpassword'],
        'native_lang' => $_POST['native-lang'],
        'lang' => $_POST['learning-lang'],
        'time_zone' => $_POST['time-zone'],
        'send_email' => true
    ];
                    
    $user = new User($pdo);
    $user_reg = new UserRegistrationManager($user);
    $user_reg->register($user_data);
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
