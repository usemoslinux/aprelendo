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

require_once '../../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & checks if user is logged in

use Aprelendo\Includes\Classes\Words;
use Aprelendo\Includes\Classes\SearchWordsParameters;
use Aprelendo\Includes\Classes\WordsUtilities;
use Aprelendo\Includes\Classes\AprelendoException;

$user_id = $user->getId();
$lang_id = $user->getLangId();

try {
    // set search criteria, if any
    $search_text = isset($_GET['s']) ? $_GET['s'] : '';
    $sort_by = isset($_GET['o']) ? $_GET['o'] : 0;

    // export to csv
    $words_table = new Words($pdo, $user_id, $lang_id);
    $search_params = new SearchWordsParameters($search_text, $sort_by);
    $words = $words_table->search($search_params);
    
    WordsUtilities::exportToCSV($words);

} catch (AprelendoException $e) {
    http_response_code($e->getCode());
}
