<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\Words;
use Aprelendo\WordStatus;
use Aprelendo\SM2;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    if (!empty($_POST['word']) && isset($_POST['answer'])) {
        $answer = (int)$_POST['answer'];
        $word_status = WordStatus::tryFrom($answer);
        if ($word_status === null) {
            throw new UserException('Invalid card status.');
        }

        $word = $_POST['word'];

        $words_table = new Words($pdo, $user_id, $lang_id);
        $words_table->loadRecordByWord($word);
        $sm2 = new SM2($words_table->easiness, $words_table->repetitions, $words_table->review_interval);
        $sm2->processReview($word_status->value);

        $words_table->updateSM2(
            $word,
            $sm2->getInterval(),
            $sm2->getEasiness(),
            $sm2->getRepetitions()
        );

        $words_table->updateStatus($word, $word_status);
        $response = ['success' => true];
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
