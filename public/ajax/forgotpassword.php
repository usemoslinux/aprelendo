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

use Aprelendo\Includes\Classes\User;
use Aprelendo\Includes\Classes\Curl;

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

try {
    if (isset($_POST['email']) && isset($_POST['newpassword']) && isset($_POST['newpassword-confirmation'])) {
        if ($_POST['newpassword'] === $_POST['newpassword-confirmation']) {
            // create password hash
            $options = [
                'cost' => 11,
            ];
            $email = $_POST['email'];
            $password_hash = password_hash($_POST['newpassword'], PASSWORD_BCRYPT, $options);

            $user = new User($pdo);
            $user->updatePasswordHash($password_hash, $email);
        } else {
            throw new \Exception('The passwords you entered are not identical. Please try again.');
        }
    } elseif (isset($_POST['email'])) {
        if (!empty($_POST['email'])) {
            // check if email exists in db
            $email = $_POST['email'];
            $user = new User($pdo);
            $user->loadRecordByEmail($email);
            
            // get password hash associated to that email address
            $username = $user->getName();
            $password_hash = $user->getPasswordHash();
            
            // create reset link & send email
            $reset_link = "https://www.aprelendo.com/forgotpassword.php?email=$email&reset=$password_hash";
            $to = $email;
            $subject = 'Aprelendo - Password reset';
            
            // get template
            $message = file_get_contents(APP_ROOT . 'templates/password_reset.html');

            // edit template
            $message = str_replace('{{action_url}}', $reset_link, $message);
            $message = str_replace('{{name}}', $username, $message);
            $message = str_replace('{{ip}}', $_SERVER['REMOTE_ADDR'], $message);
            $message = str_replace('{{device}}', $_SERVER['HTTP_USER_AGENT'], $message);
            $message = str_replace('{{current_year}}', date("Y"), $message);
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From:' . EMAIL_SENDER;
            
            $mail_sent = mail($to, $subject, $message, $headers, '-f ' . EMAIL_SENDER);
            if (!$mail_sent) {
                throw new \Exception('There was an error trying to send you an e-mail with your new '
                    . 'temporary password.');
            }
        } else { // if email address does not exist in db
            throw new \Exception('No user registered with that email address. Please try again.');
        }
    } else {
        throw new \Exception('Oops! There was an unexpected error when trying to reset your password.');
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}
