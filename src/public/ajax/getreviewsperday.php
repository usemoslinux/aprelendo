<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_GET)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\User;
use Aprelendo\Language;
use Aprelendo\WordStats;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_name = !empty($_GET['u']) ? $_GET['u'] : $user->name;
    $lang_name = $user->lang;

    // if the GET u is different from the current user name
    if ($user_name != $user->name) {
        $user = new User($pdo);
        $user->loadRecordByName($user_name);

        $lang = new Language($pdo, $user->id);
        $lang->loadRecordByName($lang_name);

        $user->lang = $lang->name;
        $user->lang_id = $lang->id;
    }

    $stats = new WordStats($pdo, $user->id, $user->lang_id);
    $payload = $stats->getReviewsPerDay();

    $response = ['success' => true, 'payload' => $payload];
    echo json_encode($response);
    exit;
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
