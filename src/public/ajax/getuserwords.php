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

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\Words;
use Aprelendo\WordsUtilities;
use Aprelendo\Language;
use Aprelendo\WordFrequency;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (!empty($_POST['txt'])) {
        $text = html_entity_decode($_POST['txt']);
        $payload['text'] = $text;

        $lang = new Language($pdo, $user->id);
        $lang->loadRecordById($user->lang_id);

        // Parse the text and only fetch words that are present in the text
        $unique_words_in_text = WordsUtilities::splitIntoUniqueWords($text, $lang->name);
        
        $user_words = new Words($pdo, $user->id, $user->lang_id);
        $payload['user_words'] = $user_words->getByWords($unique_words_in_text);

        if ($lang->show_freq_words) {
            $word_freq = new WordFrequency($pdo, $lang->name);
            $freq_words = $word_freq->getHighFrequencyList();
            $payload['high_freq'] = \array_column($freq_words, 'word');
        }

        $response = ['success' => true, 'payload' => $payload];
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
