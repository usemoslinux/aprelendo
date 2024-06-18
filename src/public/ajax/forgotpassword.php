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

use Aprelendo\User;
use Aprelendo\UserPassword;
use Aprelendo\EmailSender;
use Aprelendo\InternalException;
use Aprelendo\UserException;

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

            if (!$user->id) {
                throw new UserException('The email address you provided is not in our database of registered users.'
                    . ' Please double-check your email address or consider <a href="/register" class="alert-link">'
                    . 'signing up</a> if you are new to our platform.');
            }
            
            // get password hash associated to that email address
            $username = $user->name;
            $password_hash = $user->password_hash;
            
            // create reset link & send email
            $reset_link = "https://www.aprelendo.com/forgotpassword.php?email=$email&reset=$password_hash";
            $to = $email;
            $subject = 'Aprelendo - Password reset';
            
            // get template
            $message = file_get_contents(TEMPLATES_PATH . 'password_reset.html');

            // edit template
            $message = str_replace('{{action_url}}', $reset_link, $message);
            $message = str_replace('{{name}}', $username, $message);
            $message = str_replace('{{ip}}', $_SERVER['REMOTE_ADDR'], $message);
            $message = str_replace('{{device}}', $_SERVER['HTTP_USER_AGENT'], $message);
            $message = str_replace('{{current_year}}', date("Y"), $message);
            
            $email_sender = new EmailSender();

            $email_sender->mail->addAddress($to);
            $email_sender->mail->Subject = $subject;
            $email_sender->mail->Body = $message;
            $email_sender->mail->isHTML(true);

            $email_sender->mail->send();
        } else { // if email address does not exist in db
            throw new UserException('No user registered with that email address. Please try again.');
        }
    } else {
        throw new UserException('There was an unexpected error resetting your password.');
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
} catch (Exception $e) {
    $exception = new InternalException($e);
    echo $exception->getJsonError();
}
