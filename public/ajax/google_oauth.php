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
        $google_id = $con->escape_string($_POST['Eea']); //Google ID
        $google_email = $con->escape_string($_POST['U3']); //Email ID
        $google_name = $con->escape_string($_POST['ig']); //Name
        $google_profile_pic = $con->escape_string($_POST['Paa']); //Profile Pic URL

        $return_msg = "";

        // check if Google ID already exists
        $sql = "SELECT * FROM `users` WHERE `email`='$google_email'";
        $result = $con->query($sql);

        if ($result) {
            $user = new User($con);
            $row = $result->fetch_assoc();
            $user_name = $row['name'];

            if($result->num_rows > 0) {
                // user already exists
                $sql = "UPDATE `users` SET `google_id`='$google_id' WHERE `email`='$google_email'";
                $result = $con->query($sql);

                if ($result) {
                    $user->login($user_name, "", $google_id);
                }
            } else {
                // new user
                if ($user->register($google_name, $google_email, $google_id)) {
                    $user->login($google_name, $google_id);
                }
            }
        } else {
            throw new \Exception ('There was an unexpected error trying to log you in using your Google ID.');
        }
    } catch (Exception $e) {
        $error = array('error_msg' => $e->getMessage());
        echo json_encode($error);
    }
    
}

?>