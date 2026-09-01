<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/bootstrap.php'; // initialize application

use Aprelendo\EmailSender;
use Aprelendo\InternalException;
use Aprelendo\UserException;

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

function validateTurnstile(string $token, string $expected_action, array $expected_host_names): bool
{
    // Tokens over 2048 characters are invalid per Turnstile specs
    if ($token === '' || strlen($token) > 2048 || TURNSTILE_SECRET_KEY === '' || empty($expected_host_names)) {
        return false;
    }

    $request_body = http_build_query([
        'secret' => TURNSTILE_SECRET_KEY,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);

    $curl = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    if ($curl === false) {
        error_log('Unable to initialize Turnstile verification request.');
        return false;
    }

    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $request_body,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    ]);

    $response_body = curl_exec($curl);
    $curl_error = curl_error($curl);
    curl_close($curl);

    if ($response_body === false) {
        error_log('Turnstile verification request failed: ' . $curl_error);
        return false;
    }

    $result = json_decode($response_body, true);

    // 1. Verify the challenge was successful
    if (!is_array($result) || empty($result['success'])) {
        $error_codes = is_array($result['error-codes'] ?? null) ? implode(', ', $result['error-codes']) : 'unknown';
        error_log('Turnstile validation failed: ' . $error_codes);
        return false;
    }

    // 2. Verify the action matches the specific form submitted
    if (($result['action'] ?? '') !== $expected_action) {
        error_log('Turnstile validation failed: action mismatch. Expected ' . $expected_action . ', got ' . ($result['action'] ?? 'none'));
        return false;
    }

    // 3. Verify the token was generated on an approved domain
    if (!in_array($result['hostname'] ?? '', $expected_host_names, true)) {
        error_log('Turnstile validation failed: hostname mismatch. Got ' . ($result['hostname'] ?? 'none'));
        return false;
    }

    return true;
}

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

try {
    // Define the action you set in data-action="contact" on your frontend HTML
    $expected_action = 'contact';
    $expected_host_names = ['aprelendo.com', 'www.aprelendo.com'];

    if (!IS_SELF_HOSTED && !validateTurnstile($_POST['cf-turnstile-response'] ?? '', $expected_action, $expected_host_names)) {
        throw new UserException('Verification failed. Please try again.');
    }

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
