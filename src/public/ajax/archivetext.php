<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST) || !isset($_POST['textIDs']) || !isset($_POST['archivetext'])) {
    echo json_encode($response);
    exit;
}

use Aprelendo\Texts;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    $text_ids = json_decode($_POST['textIDs']);
    $texts_table = new Texts($pdo, $user_id, $lang_id);

    if ($_POST['archivetext'] === 'true') { //archive text
        $texts_table->archive($text_ids);
    } else { // unarchive text
        $texts_table->unarchive($text_ids);
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
