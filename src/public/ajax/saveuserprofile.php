<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\SecureEncryption;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_id = $user->id;

    // save user profile information
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $new_password1 = $_POST['newpassword'] ?? '';
    $new_password2 = $_POST['newpassword-confirmation'] ?? '';
    $src_lang = $_POST['src_lang'] ?? '';
    $to_lang = $_POST['to_lang'] ?? '';
    $hf_token = $_POST['hf-token'] ?? '';

    $crypto = new SecureEncryption(ENCRYPTION_KEY);
    $hf_token = $hf_token === '' ? '' : $crypto->encrypt($hf_token);

    $user_data = [
        'new_username' => $username,
        'new_email' => $email,
        'password' => $password,
        'new_password' => $new_password1,
        'new_native_lang' => $src_lang,
        'new_lang' => $to_lang,
        'hf_token' => $hf_token
    ];

    if (empty($new_password1) && empty($new_password2)) {
        if (empty($password) && empty($user->google_id)) {
            throw new UserException('Please enter your current password and try again.');
        }
        
        $user->updateProfile($user_data);
    } else {
        if ($new_password1 !== $new_password2) {
            throw new UserException('Both new passwords should be identical. Please, try again.');
        }

        if (mb_strlen($new_password1) < 8) {
            throw new UserException('New password should be at least 8 characters long. Please, try again.');
        }

        $user->updateProfile($user_data);
    }

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
