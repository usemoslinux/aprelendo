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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\Texts;
use Aprelendo\ArchivedTexts;
use Aprelendo\Language;
use Aprelendo\ExampleSentences;
use Aprelendo\Words;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_id = $user->id;
    $lang_id = $user->lang_id;

    // if text is archived using green button at the end, update learning status of words first
    if (isset($_POST['words'])) {
        $words_table = new Words($pdo, $user_id, $lang_id);
        $words_table->updateByName($_POST['words']);
    }

    // if text is not shared, then archive or unarchive text accordingly
    if (!empty($_POST['textIDs']) && !empty($_POST['archivetext'])) {
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
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
