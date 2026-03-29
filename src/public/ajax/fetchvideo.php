<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php';  // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\Videos;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (empty($_POST['video_id'])) {
        throw new UserException('Error retrieving that URL. Please check it is not empty or malformed');
    }
    
    $video_id = $_POST['video_id'];
    $video = new Videos($pdo, $user->id, $user->lang_id);
    $payload = $video->fetchVideo($user->lang, $video_id);
    
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