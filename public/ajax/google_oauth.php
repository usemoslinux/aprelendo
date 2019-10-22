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

require_once '../../includes/dbinit.php'; // connect to database

use Aprelendo\Includes\Classes\User;

if(isset($_POST['Eea']) && !empty($_POST['Eea']) && !empty($_POST['U3']))
{
    try {
        $google_id = $_POST['Eea']; //Google ID
        $google_email = $_POST['U3']; //Email ID
        $google_name = $_POST['ig']; //Name
        $google_profile_pic = $_POST['Paa']; //Profile Pic URL

        $user = new User($con);

        // check if google email is already in db
        if($user->loadRecordByEmail($google_email)) {
            // user already exists
            $user->updateGoogleId($google_id, $google_email);
            $user->login($user->getName(), '', $google_id);
        } else {
            // new user
            if ($user->register($google_name, $google_email, $google_id)) {
                $user->login($google_name, $google_id);
            }
        }
    } catch (Exception $e) {
        $error = array('error_msg' => 'There was an unexpected error trying to log you in using your Google ID.');
        echo json_encode($error);
    } finally {
        $stmt = null;
    }
}

?>