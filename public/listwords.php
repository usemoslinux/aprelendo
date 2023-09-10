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

require_once '../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

use Aprelendo\Includes\Classes\Words;
use Aprelendo\Includes\Classes\WordTable;
use Aprelendo\Includes\Classes\SearchWordsParameters;
use Aprelendo\Includes\Classes\Pagination;
use Aprelendo\Includes\Classes\Url;

$user_id = $user->id;
$lang_id = $user->lang_id;

// set variables used for pagination
$page = 1;
$limit = 10; // number of rows per page
$adjacents = 2; // adjacent page numbers

$sort_by = !empty($_GET['o']) ? $_GET['o'] : 0;

// if the page is loaded because user searched for something, show search results
// otherwise, show complete word list

// initialize pagination variables
if (isset($_GET['p'])) {
    $page = !empty($_GET['p']) ? $_GET['p'] : 1;
}

$search_text = !empty($_GET['s']) ? $_GET['s'] : '';

$words_table = new Words($pdo, $user_id, $lang_id);
$total_rows = $words_table->countSearchRows($search_text);
$pagination = new Pagination($total_rows, $page, $limit, $adjacents);
$offset = $pagination->offset;

// get search result
$search_params = new SearchWordsParameters($search_text, $sort_by, $offset, $limit);
$rows = $words_table->search($search_params);

// print table
if ($rows) { // if there are any results, show them
    $table = new WordTable($rows);
    $html = $table->print($sort_by);

    // print pagination
    $url_query_options = compact("search_text", "sort_by");
    $page_url = new Url('words', $url_query_options);
    $html .= $pagination->print($page_url);
} else {
    if (!empty($_GET)) {
        $html = '<div class="alert alert-info" role="alert">No words found with that criteria. Try again.</div>';
    } else {
        $html = '<div class="alert alert-info" role="alert">There are no words in your private library.</div>';
    }
}

echo $html;

require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window

?>

<script defer src="/js/listwords.js"></script>
