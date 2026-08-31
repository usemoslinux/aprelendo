<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/bootstrap.php';  // initialize application

use Aprelendo\AuthGuard;
use Aprelendo\Database;
use Aprelendo\Videos;
use Aprelendo\InternalException;
use Aprelendo\UserException;

$pdo = Database::connection();
$user = AuthGuard::requireAjaxUser();

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}


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
    echo json_encode([
        'success' => false,
        'error_msg' => $e instanceof UserException
            ? $e->getMessage()
            : 'Oops! There was an unexpected error processing your request.',
        'error_html' => $e instanceof UserException && str_contains($e->getMessage(), '<a '),
    ]);
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
