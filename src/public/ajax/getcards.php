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

use Aprelendo\Card;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    // initialize variables
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    $card = new Card($pdo, $user_id, $lang_id);

    if (!isset($_POST['word']) || empty($_POST['word'])) {
        $limit = $_POST['limit'];
        $status = isset($_POST['status']) && $_POST['status'] !== '' ? (int)$_POST['status'] : null;
        $result = $card->getWordsUserIsLearning((int)$limit, $status);
    } else {
        $result = $card->getExampleSentencesForWord($_POST['word']);
    }

    echo json_encode($result);
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
