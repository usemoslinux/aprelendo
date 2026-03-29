<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\User;
use Aprelendo\UserPassword;
use Aprelendo\EmailSender;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    // Branch 1: User is submitting a new password with a token
    if (isset($_POST['token']) && isset($_POST['newpassword']) && isset($_POST['newpassword-confirmation'])) {
        if ($_POST['newpassword'] !== $_POST['newpassword-confirmation']) {
            throw new UserException('The passwords you entered are not identical. Please try again.');
        }

        $token = $_POST['token'];
        $user = new User($pdo);

        // Find user by valid token and check expiry
        if ($user->loadUserByValidResetToken($token)) {
            // Token is valid, proceed with password update
            $password_hash = UserPassword::createHash($_POST['newpassword']);
            $user->updatePasswordHash($password_hash, $user->email);
            
            // Invalidate the token so it cannot be reused
            $user->clearResetToken();
        } else {
            // Token is invalid or expired
            throw new UserException('Invalid or expired password reset token. Please request a new one.');
        }
    } 
    // Branch 2: User is requesting a password reset link
    elseif (isset($_POST['email'])) {
        if (empty($_POST['email'])) {
            throw new UserException('Please provide a valid email address.');
        }

        $email = $_POST['email'];
        $user = new User($pdo);
        $user->loadRecordByEmail($email);

        // Important: Do not reveal if the user exists or not.
        // Send a success message regardless to prevent user enumeration.
        if ($user->id) {
            // User exists, generate token and send email
            $token = $user->setResetToken();
            $username = $user->name;
        
            // Create reset link & send email
            $reset_link = "https://www.aprelendo.com/forgotpassword.php?token=$token";
            $to = $email;
            $subject = 'Aprelendo - Password reset';
            
            $message = file_get_contents(TEMPLATES_PATH . 'password-reset.html');
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
        }
    } else {
        throw new UserException('There was an unexpected error processing your request.');
    }

    // On success, send a generic confirmation message
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