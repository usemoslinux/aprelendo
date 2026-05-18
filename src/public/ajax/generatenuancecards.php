<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/bootstrap.php'; // initialize application

use Aprelendo\AIBot;
use Aprelendo\AuthGuard;
use Aprelendo\ConfusionSets;
use Aprelendo\Database;
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
    if (empty($user->hf_token)) {
        throw new UserException('Configure Lingobot in your profile to play Nuance Battle.');
    }

    $confusion_sets = new ConfusionSets($pdo, $user->id, $user->lang_id);
    $set = $confusion_sets->getById((int)($_POST['set_id'] ?? 0));

    if (count($set['words']) < 2) {
        throw new UserException('Add at least two words to this set before playing.');
    }

    $ai_bot = new AIBot($user->hf_token, $user->lang, $user->native_lang);
    $cards = $ai_bot->generateNuanceBattleCards($set['words']);

    $response = [
        'success' => true,
        'payload' => [
            'set' => $set,
            'cards' => $cards
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
