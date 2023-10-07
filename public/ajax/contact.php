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

use Aprelendo\EmailSender;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['message'])) {
        $name = $_POST['name'];
        $reply_to = $_POST['email'];
        $message = $_POST['message'];

        // check if email is valid
        if (!filter_var($reply_to, FILTER_VALIDATE_EMAIL)) {
            throw new UserException('The email address you entered is invalid. Please try again.');
        }

        // check if fields have the allowed length
        if (strlen($name) > 100 || strlen($reply_to) > 100 || strlen($message) > 5000) {
            throw new UserException('You have exceeded the allowed length for one or more fields.');
        }

        // create & send email
        $subject = 'Support request - ' . $name;

        $message .= "\r\n\r\nE-mail: " . $reply_to;
        $message .= "\r\n\r\nIP: " . $_SERVER['REMOTE_ADDR'];
        $message .= "\r\n\r\nDevice: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n\r\n";
        
        $email_sender = new EmailSender();

        $email_sender->mail->addReplyTo($reply_to);
        $email_sender->mail->addAddress(SUPPORT_EMAIL);
        $email_sender->mail->Subject = $subject;
        $email_sender->mail->Body = $message;
        $email_sender->mail->isHTML(false);

        $email_sender->mail->send();
    } else {
        throw new UserException('You need to complete all required form fields. Please try again.');
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
} catch (Exception $e) {
    $exception = new InternalException($e);
    echo $exception->getJsonError();
}
