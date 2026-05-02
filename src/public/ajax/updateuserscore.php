<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/bootstrap.php'; // initialize application

use Aprelendo\AuthGuard;
use Aprelendo\Database;
use Aprelendo\Gems;
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
    if (isset($_POST['review_data'])) {
        $user_id = $user->id;
        $lang_id = $user->lang_id;

        $gems = new Gems($pdo, $user_id, $lang_id, $user->time_zone);
        $payload = json_decode($_POST['review_data'] ?? '[]', true);
        $new_gems = $gems->updateScore($payload);

        $response = ['success' => true, 'gems_earned' => $new_gems];
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
