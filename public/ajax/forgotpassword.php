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
use Aprelendo\Includes\Classes\UserPassword;
use Aprelendo\Includes\Classes\InternalException;
use Aprelendo\Includes\Classes\UserException;

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

try {
    if (isset($_POST['email']) && isset($_POST['newpassword']) && isset($_POST['newpassword-confirmation'])) {
        if ($_POST['newpassword'] === $_POST['newpassword-confirmation']) {
            $password_hash = UserPassword::createHash($_POST['newpassword']);
            $email = $_POST['email'];

            $user = new User($pdo);
            $user->updatePasswordHash($password_hash, $email);
        } else {
            throw new UserException('The passwords you entered are not identical. Please try again.');
        }
    } elseif (isset($_POST['email'])) {
        if (!empty($_POST['email'])) {
            // check if email exists in db
            $email = $_POST['email'];
            $user = new User($pdo);
            $user->loadRecordByEmail($email);
            
            // get password hash associated to that email address
            $username = $user->name;
            $password_hash = $user->password_hash;
            
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
                throw new UserException('There was an error trying to send you an e-mail with your new '
                    . 'temporary password.');
            }
        } else { // if email address does not exist in db
            throw new UserException('No user registered with that email address. Please try again.');
        }
    } else {
        throw new UserException('There was an error resetting your password.');
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
