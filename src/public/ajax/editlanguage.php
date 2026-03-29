<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST) || !isset($_POST['id'])) {
    echo json_encode($response);
    exit;
}

use Aprelendo\Language;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $lang = new Language($pdo, $user->id);
    $lang->loadRecordById($_POST['id']);
    $lang->editRecord($_POST);

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
