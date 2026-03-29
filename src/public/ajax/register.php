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
use Aprelendo\UserRegistrationManager;
use Aprelendo\InternalException;
use Aprelendo\UserException;

$user = new User($pdo);

try {
    // check username, email & password are set and not empty
    if (!isset($_POST['username']) || empty($_POST['username']) ||
        !isset($_POST['newpassword']) || empty($_POST['newpassword']) ||
        !isset($_POST['email'])    || empty($_POST['email'])) {
        throw new UserException('Either username, email or password were not provided. Please try again.');
    }

    // check e-mail address is valid
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new UserException('Invalid e-mail address. Please try again.');
    }

    // check password is valid
    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
    if (!preg_match($regex, $_POST['newpassword'])) {
        throw new UserException("Password must have at least 8 characters and contain letters, special characters"
            . " and digits. Please try again.");
    }

    // check password & password confirmation match
    if ($_POST['newpassword'] != $_POST['newpassword-confirmation']) {
        throw new UserException("Passwords don't match. Please try again.");
    }
    
    $user_data = [
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'password' => $_POST['newpassword'],
        'native_lang' => $_POST['native-lang'],
        'lang' => $_POST['learning-lang'],
        'time_zone' => $_POST['time-zone'],
        'send_email' => !IS_SELF_HOSTED
    ];

    $user_reg = new UserRegistrationManager($user);
    $user_reg->register($user_data);

    $response = ['success' => true, 'is_self_hosted' => IS_SELF_HOSTED];
    echo json_encode($response);
    exit;
} catch (InternalException | UserException $e) {
    $user->delete();
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
