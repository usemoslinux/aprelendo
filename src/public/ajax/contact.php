<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\EmailSender;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
        throw new UserException('You need to complete all required form fields. Please try again.');
    }
        
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

    $email_sender->mail->setFrom(SUPPORT_EMAIL, 'Aprelendo - Contact Form');
    $email_sender->mail->addReplyTo($reply_to);
    $email_sender->mail->addAddress(SUPPORT_EMAIL);
    $email_sender->mail->Subject = $subject;
    $email_sender->mail->Body = $message;
    $email_sender->mail->isHTML(false);

    $email_sender->mail->send();

    $response = ['success' => true];
    echo json_encode($response);
    exit;
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
