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

use Aprelendo\Words;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    if (isset($_POST['word'])) {
        // deletes word by 'name'; used by showtext.php
        $words_table = new Words($pdo, $user_id, $lang_id);
        $words_table->deleteByName($_POST['word']);
    } elseif (isset($_POST['wordIDs'])) {
        // deletes word by id; used by listwords.php
        $word_ids = json_decode($_POST['wordIDs'], true);

        if (!is_array($word_ids)) {
            throw new UserException('Invalid word selection.');
        }

        $words_table = new Words($pdo, $user_id, $lang_id);
        $words_table->delete($word_ids);
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
