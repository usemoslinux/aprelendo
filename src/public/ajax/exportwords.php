<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/bootstrap.php'; // initialize application

use Aprelendo\AuthGuard;
use Aprelendo\Database;
use Aprelendo\Words;
use Aprelendo\SearchWordsParameters;
use Aprelendo\WordsUtilities;
use Aprelendo\UserException;

$pdo = Database::connection();
$user = AuthGuard::requireAjaxUser();

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
    http_response_code(500);
    exit;
}
