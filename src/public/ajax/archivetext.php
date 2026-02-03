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

if (empty($_POST) || !isset($_POST['textIDs']) || !isset($_POST['archivetext'])) {
    echo json_encode($response);
    exit;
}

use Aprelendo\Texts;
use Aprelendo\ArchivedTexts;
use Aprelendo\Language;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    // if text is not shared, then archive or unarchive text accordingly
    $lang = new Language($pdo, $user_id);
    $lang->loadRecordById($user->lang_id);
    $text_ids = json_decode($_POST['textIDs']);

    if ($_POST['archivetext'] === 'true') { //archive text
        $texts_table = new Texts($pdo, $user_id, $lang_id);
        $texts_table->archive($text_ids);
    } else { // unarchive text
        $texts_table = new ArchivedTexts($pdo, $user_id, $lang_id);
        $texts_table->unarchive($text_ids);
    }

    $response = ['success' => true];
    echo json_encode($response);
    exit;
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
