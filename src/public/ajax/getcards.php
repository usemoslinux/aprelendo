<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/bootstrap.php'; // initialize application

use Aprelendo\AuthGuard;
use Aprelendo\Database;
use Aprelendo\Card;
use Aprelendo\WordStatus;
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
    // initialize variables
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    $card = new Card($pdo, $user_id, $lang_id);

    if (!isset($_POST['word']) || empty($_POST['word'])) {
        $limit = $_POST['limit'];
        $status = null;
        if (isset($_POST['status']) && $_POST['status'] !== '') {
            $status = WordStatus::tryFrom((int)$_POST['status']);
            if ($status === null) {
                throw new UserException('Invalid card status filter.');
            }
        }
        $payload = $card->getWordsUserIsLearning((int)$limit, $status);
    } else {
        $payload = $card->getExampleSentencesForWord($_POST['word']);
    }

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
