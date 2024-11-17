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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\SecureEncryption;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_id = $user->id;

    // save user profile information
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $new_password1 = isset($_POST['newpassword']) ? $_POST['newpassword'] : '';
    $new_password2 = isset($_POST['newpassword-confirmation']) ? $_POST['newpassword-confirmation'] : '';
    $src_lang = isset($_POST['src_lang']) ? $_POST['src_lang'] : '';
    $to_lang = isset($_POST['to_lang']) ? $_POST['to_lang'] : '';
    $hf_token = isset($_POST['hf_token']) ? $_POST['hf_token'] : '';

    $crypto = new SecureEncryption(ENCRYPTION_KEY);
    $hf_token = $_POST['hf_token'] === '' ? '' : $crypto->encrypt($_POST['hf_token']);

    $user_data = [
        'new_username' => $username,
        'new_email' => $email,
        'password' => $password,
        'new_password' => $new_password1,
        'new_native_lang' => $src_lang,
        'new_lang' => $to_lang,
        'hf_token' => $hf_token
    ];

    if (empty($new_password1) && empty($new_password2)) {
        if (empty($password)) {
            throw new UserException('Please enter your current password and try again.');
        } else {
            $user->updateProfile($user_data);
        }
    } else {
        if ($new_password1 === $new_password2) {
            if (mb_strlen($new_password1) >= 8) {
                $user->updateProfile($user_data);
            } else {
                throw new UserException('New password should be at least 8 characters long. Please, try again.');
            }
        } else {
            throw new UserException('Both new passwords should be identical. Please, try again.');
        }
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
