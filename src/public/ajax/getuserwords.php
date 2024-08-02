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
use Aprelendo\Language;
use Aprelendo\WordFrequency;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (isset($_POST['txt'])) {
        $text = html_entity_decode($_POST['txt']);
        $result['text'] = $text;

        $user_words = new Words($pdo, $user->id, $user->lang_id);
        $result['user_words'] = $user_words->getAll(10); // 10 = words first, then phrases

        $lang = new Language($pdo, $user->id);
        $lang->loadRecordById($user->lang_id);

        if ($lang->show_freq_words) {
            $word_freq = new WordFrequency($pdo);
            $freq_words = $word_freq->getHighFrequencyList($lang->name);
            $result['high_freq'] = \array_column($freq_words, 'word');
        }

        echo json_encode($result);
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
