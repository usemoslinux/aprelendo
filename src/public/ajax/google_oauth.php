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

use Aprelendo\User;
use Aprelendo\UserAuth;
use Aprelendo\UserRegistrationManager;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (!empty($_POST['id']) && !empty($_POST['email'])) {
        $google_id = $_POST['id']; //Google ID
        $google_email = $_POST['email']; //Email ID
        $google_name = $_POST['name']; //Name
        // $google_profile_pic = $_POST['pic']; //Profile Pic URL
        $time_zone = $_POST['time_zone']; // browser time zone

        $user = new User($pdo);

        // check if google email is already in db
        $user->loadRecordByEmail($google_email);
        $user_auth = new UserAuth($user);

        if (!empty($user->email)) {
            // user already exists
            $user_auth->login($user->name, '', $google_id);
        } else {
            // new user
            $user_data = [
                'username' => $google_name,
                'email' => $google_email,
                'password' => $google_id
            ];
            
            $user_reg = new UserRegistrationManager($user);
            $user_reg->register($user_data);
            $user->updateGoogleId($google_id, $google_email);
            $user_auth->login($google_name, $google_id);
        }
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
