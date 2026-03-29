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
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $logout_after_user_delete = $_POST['after_user_delete'] ?? false;
    $user = new User($pdo);
    $user_auth = new UserAuth($user);
    
    $user_auth->logout($logout_after_user_delete);
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
