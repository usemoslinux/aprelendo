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
use Aprelendo\UserAuth;
use Aprelendo\UserRegistrationManager;
use Aprelendo\GoogleIdTokenVerifier;
use Aprelendo\InternalException;
use Aprelendo\UserException;
use Aprelendo\UserPassword;

try {
    if (empty($_POST['credential'])) {
        throw new UserException('Google credential was not provided. Please try again.');
    }

    $google_client_id = defined('GOOGLE_CLIENT_ID')
        ? GOOGLE_CLIENT_ID
        : '913422235077-082170c2l6b58ck8ie0f03rigombl2pc.apps.googleusercontent.com';

    $google_verifier = new GoogleIdTokenVerifier($google_client_id);
    $google_profile = $google_verifier->verify($_POST['credential']);

    $google_id = $google_profile['sub'];
    $google_email = $google_profile['email'];
    $google_name = $google_profile['name'];

    if ($google_name === '') {
        $google_name = strstr($google_email, '@', true) ?: $google_email;
    }

    $time_zone = $_POST['time-zone'] ?? 'UTC';
    if (!is_string($time_zone) || !in_array($time_zone, timezone_identifiers_list(), true)) {
        $time_zone = 'UTC';
    }

    $user = new User($pdo);

    $user->loadRecordByEmail($google_email);

    if (!empty($user->email)) {
        if (!empty($user->google_id) && !hash_equals($user->google_id, $google_id)) {
            throw new UserException('This Google account is not linked to your Aprelendo account.');
        }

        if (empty($user->google_id)) {
            if (!$google_verifier->isEmailAuthoritative($google_profile)) {
                throw new UserException('This Google account cannot be linked automatically. '
                    . 'Log in with your password instead.');
            }

            $user->updateGoogleId($google_id, $google_email);
            $user->loadRecordByEmail($google_email);
        }
    } else {
        $user_data = [
            'username' => $google_name,
            'email' => $google_email,
            'password' => bin2hex(random_bytes(32)),
            'time_zone' => $time_zone
        ];

        $user_reg = new UserRegistrationManager($user);
        $user_reg->register($user_data);
        $user->updateGoogleId($google_id, $google_email);
        $user->loadRecordByEmail($google_email);
    }

    if (!empty($user->google_id) && password_verify($user->google_id, $user->password_hash)) {
        $user->updatePasswordHash(UserPassword::createHash(bin2hex(random_bytes(32))), $google_email);
        $user->loadRecordByEmail($google_email);
    }

    $user_auth = new UserAuth($user);
    $user_auth->loginWithGoogle($google_id);

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
