<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

use Aprelendo\Language;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $lang = new Language($pdo, $user->id);
    $lang->loadRecordById($user->lang_id);

    $payload['dictionary_uri']     = $lang->dictionary_uri;
    $payload['img_dictionary_uri'] = $lang->img_dictionary_uri;
    $payload['translator_uri']     = $lang->translator_uri;

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