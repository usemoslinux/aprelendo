<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

use Aprelendo\Words;
use Aprelendo\SearchWordsParameters;
use Aprelendo\WordsUtilities;
use Aprelendo\InternalException;
use Aprelendo\UserException;

$user_id = $user->id;
$lang_id = $user->lang_id;

try {
    // set search criteria, if any
    $search_text = $_GET['s'] ?? '';
    $sort_by = $_GET['o'] ?? 0;

    // export to csv
    $words_table = new Words($pdo, $user_id, $lang_id);
    $search_params = new SearchWordsParameters($search_text, $sort_by);
    $words = $words_table->search($search_params);
    
    WordsUtilities::exportToCSV($words);
} catch (UserException $e) {
    http_response_code($e->getCode());
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
