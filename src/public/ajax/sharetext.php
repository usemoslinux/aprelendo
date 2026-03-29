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

use Aprelendo\Texts;
use Aprelendo\Language;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    // if text is not shared, then archive or unarchive text accordingly
    if (!empty($_POST['textID'])) {
        $lang = new Language($pdo, $user_id);
        $lang->loadRecordById($user->lang_id);
        $text_id = $_POST['textID'];

        $texts_table = new Texts($pdo, $user_id, $lang_id);
        $texts_table->share($text_id);
        $response = ['success' => true];
    }

    echo json_encode($response);
    exit;
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}