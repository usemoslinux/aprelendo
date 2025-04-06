<?php
/**
 * Copyright (C) 2019 Pablo Castagnino
 *
 * This file is part of aprelendo.
 *
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\Words;
use Aprelendo\SM2;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    if (!empty($_POST['word']) && isset($_POST['answer'])) {
        $answer = (int)$_POST['answer'];
        $word = $_POST['word'];

        $words_table = new Words($pdo, $user_id, $lang_id);
        $words_table->loadRecordByWord($word);
        $sm2 = new SM2($words_table->easiness, $words_table->repetitions, $words_table->review_interval);
        $sm2->processReview($answer);

        $words_table->updateSM2(
            $word,
            $sm2->getInterval(),
            $sm2->getEasiness(),
            $sm2->getRepetitions()
        );

        $words_table->updateStatus($word, $answer);
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
