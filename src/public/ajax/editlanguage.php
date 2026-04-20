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
    $lang_id = (int)$_POST['id'];

    if ($lang_id === 0) {
        throw new UserException('Unauthorized access.', 403);
    }

    $lang = new Language($pdo, $user->id);
    if (!$lang->loadRecordById($lang_id)) {
        throw new UserException('Unauthorized access.', 403);
    }
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
