<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

use Aprelendo\Preferences;
use Aprelendo\InternalException;
use Aprelendo\UserException;

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

// save preferences to database
try {
    $pref = new Preferences($pdo, $user->id);
    $pref->edit(
        $_POST['fontfamily'],
        $_POST['fontsize'],
        $_POST['lineheight'],
        $_POST['alignment'],
        $_POST['mode'],
        $_POST['assistedlearning']
    );

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
