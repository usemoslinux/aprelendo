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
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST) || !isset($_POST['mode'])) {
    echo json_encode($response);
    exit;
}

use Aprelendo\Texts;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $text = new Texts($pdo, $user->id, $user->lang_id);

    if ($_POST['mode'] == "GET") {
        $text->loadRecord($_POST['id']);
        $payload['audio_pos'] = $text->audio_pos;
        $payload['text_pos'] = $text->text_pos;
        $response = ['success' => true, 'payload' => $payload];
    } elseif ($_POST['mode'] == "SAVE") {
        $payload['audio_pos'] = !empty($_POST['audio_pos']) ? $_POST['audio_pos'] : null;
        $payload['text_pos'] = !empty($_POST['text_pos']) ? $_POST['text_pos'] : null;
        $text->update($_POST['id'], $payload);
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
