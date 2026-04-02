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
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (isset($_POST['textIDs']) && isset($_POST['is_archived'])) {
        $text_ids = json_decode($_POST['textIDs']);
        $is_archived = $_POST['is_archived'] === '1';
        $user_id = $user->id;
        $lang_id = $user->lang_id;

        $texts_table = new Texts($pdo, $user_id, $lang_id);
        $texts_table->setArchiveFilter($is_archived);
        $texts_table->delete($text_ids);
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
