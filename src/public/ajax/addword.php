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
use Aprelendo\ExampleSentences;
use Aprelendo\InternalException;
use Aprelendo\UserException;

/**
 * Processes a single word addition.
 *
 * @param \PDO $pdo The PDO instance for database connection.
 * @param object $user The user object containing user details.
 * @param array $post The POST data containing word details.
 * @return void
 */
function processSingleWord(\PDO $pdo, $user, array $post): void {
    $user_id = $user->id;
    $lang_id = $user->lang_id;
    $text_is_shared = isset($post['text_is_shared']) ? $post['text_is_shared'] : false;

    // If a source_id is provided, load the text record and update the language id accordingly.
    if (isset($post['source_id']) && is_numeric($post['source_id'])) {
        $text_class_name = $text_is_shared ? 'Aprelendo\SharedTexts' : 'Aprelendo\Texts';
        $text = new $text_class_name($pdo, $user_id, $lang_id);
        $text->loadRecord($post['source_id']);
        $lang_id = $text->lang_id;
    }

    $word = $post['word'];
    $is_phrase = !empty($post['is_phrase']) ? (bool) $post['is_phrase'] : false;

    // // 1. Add word to table
    $words_table = new Words($pdo, $user_id, $lang_id);
    $status = $words_table->exists($word) ? 3 : 2;
    $words_table->add($word, $status, $is_phrase);

    // 2. If a source_id is provided, save the example sentence
    if (isset($post['source_id'])) {
        $source_id = $post['source_id'];
        $source_table = $text_is_shared ? 'shared_texts' : 'texts';
        $sentence = isset($post['sentence']) ? $post['sentence'] : '';

        $new_sentence_record = [
            'source_id' => $source_id,
            'source_table' => $source_table,
            'word' => $word,
            'sentence' => $sentence
        ];

        $example_sentence = new ExampleSentences($pdo, $user_id);
        $example_sentence->addRecord($new_sentence_record);
    }
}

/**
 * Processes the import of multiple words.
 *
 * @param \PDO $pdo The PDO instance for database connection.
 * @param object $user The user object containing user details.
 * @param array $post The POST data containing words details.
 * @return void
 */
function processWordsImport(\PDO $pdo, $user, array $post): void {
    $user_id = $user->id;
    $lang_id = $user->lang_id;
    $words = $post['words'];
    $is_phrase = false;

    $words_table = new Words($pdo, $user_id, $lang_id);
    foreach ($words as $word) {
        $status = $words_table->exists($word) ? 3 : 2;
        $words_table->add($word, $status, $is_phrase);
    }
}

try {
    if (isset($_POST['word'])) {
        processSingleWord($pdo, $user, $_POST);
    } elseif (isset($_POST['words'])) {
        processWordsImport($pdo, $user, $_POST);
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
