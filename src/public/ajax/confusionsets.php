<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/bootstrap.php'; // initialize application

use Aprelendo\AuthGuard;
use Aprelendo\ConfusionSets;
use Aprelendo\Database;
use Aprelendo\InternalException;
use Aprelendo\UserException;

$pdo = Database::connection();
$user = AuthGuard::requireAjaxUser();

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

try {
    $confusion_sets = new ConfusionSets($pdo, $user->id, $user->lang_id);
    $action = $_POST['action'] ?? $_GET['action'] ?? 'list';

    if ($action === 'list') {
        $response = [
            'success' => true,
            'payload' => [
                'sets' => $confusion_sets->getAll()
            ]
        ];
        echo json_encode($response);
        exit;
    }

    if ($action === 'public_list') {
        $response = [
            'success' => true,
            'payload' => [
                'sets' => $confusion_sets->getPublicSets($user->lang)
            ]
        ];
        echo json_encode($response);
        exit;
    }

    if (empty($_POST)) {
        echo json_encode($response);
        exit;
    }

    if ($action === 'save') {
        $set_id = (int)($_POST['id'] ?? 0);
        $title = $_POST['title'] ?? '';
        $words = ConfusionSets::parseWordsText($_POST['words'] ?? '');

        if ($set_id > 0) {
            $confusion_sets->update($set_id, $title, $words);
        } else {
            $set_id = $confusion_sets->create($title, $words);
        }

        $response = [
            'success' => true,
            'payload' => [
                'id' => $set_id,
                'sets' => $confusion_sets->getAll()
            ]
        ];
        echo json_encode($response);
        exit;
    }

    if ($action === 'delete') {
        $confusion_sets->delete((int)($_POST['id'] ?? 0));

        $response = [
            'success' => true,
            'payload' => [
                'sets' => $confusion_sets->getAll()
            ]
        ];
        echo json_encode($response);
        exit;
    }

    if ($action === 'copy_public') {
        $set_id = $confusion_sets->copyPublicSet((int)($_POST['id'] ?? 0), $user->lang);

        $response = [
            'success' => true,
            'payload' => [
                'id' => $set_id,
                'sets' => $confusion_sets->getAll()
            ]
        ];
        echo json_encode($response);
        exit;
    }

    throw new UserException('Invalid action.');
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
