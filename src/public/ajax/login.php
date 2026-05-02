<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/bootstrap.php'; // initialize application

use Aprelendo\Database;
use Aprelendo\User;
use Aprelendo\UserAuth;
use Aprelendo\InternalException;
use Aprelendo\UserException;

$pdo = Database::connection();

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

try {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        throw new UserException('Either username, email or password were not provided. Please try again.');
    }
    
    $user = new User($pdo);
    $user_auth = new UserAuth($user);
    $user_auth->login($_POST['username'], $_POST['password']);
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
