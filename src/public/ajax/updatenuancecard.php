<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/bootstrap.php'; // initialize application

use Aprelendo\AuthGuard;
use Aprelendo\Database;
use Aprelendo\InternalException;
use Aprelendo\UserException;
use Aprelendo\Words;

$pdo = Database::connection();
$user = AuthGuard::requireAjaxUser();

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

try {
    $word = trim($_POST['word'] ?? '');

    if ($word === '' || !isset($_POST['is_correct'])) {
        throw new UserException('Invalid Nuance Battle answer.');
    }

    $is_correct = filter_var($_POST['is_correct'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

    if ($is_correct === null) {
        throw new UserException('Invalid Nuance Battle answer.');
    }

    $words_table = new Words($pdo, $user->id, $user->lang_id);
    $new_status = $words_table->updateNuanceBattleReview($word, $is_correct);

    $response = [
        'success' => true,
        'payload' => [
            'status' => $new_status->value
        ]
    ];
    echo json_encode($response);
    exit;
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
