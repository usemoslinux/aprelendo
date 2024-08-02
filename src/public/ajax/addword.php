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
use Aprelendo\ExampleSentences;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    // $_POST['word'] is set when user is adding or modifying one word
    // this is the reason why addwords.php would be usually called
    if (isset($_POST['word'])) {
        $user_id = $user->id;
        $lang_id = $user->lang_id;

        $word = $_POST['word'];
        $is_phrase =  (!empty($_POST['is_phrase'])) ? $_POST['is_phrase'] : false;

        // 1. Add word to table
        $words_table = new Words($pdo, $user_id, $lang_id);

        // if word already exists in table, status = 3 ("forgotten")
        // otherwise, $status = 2 ("new")
        $status = $words_table->exists($word) ? 3 : 2;
        $words_table->add($word, $status, $is_phrase);

        // 2. If new word, save example sentence

        if (isset($_POST['source_id'])) {
            $source_id = $_POST['source_id'];
            $source_table = $_POST['text_is_shared'] ? 'shared_texts' : 'texts';
            $sentence = $_POST['sentence'];

            $new_sentence_record = [
                'source_id' => $source_id,
                'source_table' => $source_table,
                'word' => $word,
                'sentence' => $sentence
            ];

            $example_sentence = new ExampleSentences($pdo, $user_id);
            $example_sentence->addRecord($new_sentence_record);
        }
    } elseif (isset($_POST['words'])) {
        // $_POST['words'] would be used for ONLY importing many words
        // using the "import words" button
        $user_id = $user->id;
        $lang_id = $user->lang_id;
        $words = $_POST['words'];
        $is_phrase =  false;

        $words_table = new Words($pdo, $user_id, $lang_id);

        foreach ($words as $word) {
            // if word already exists in table, status = 3 ("forgotten")
            // otherwise, $status = 2 ("new")
            $status = $words_table->exists($word) ? 3 : 2;

            $words_table->add($word, $status, $is_phrase);
        }
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
