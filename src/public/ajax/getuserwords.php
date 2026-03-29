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
