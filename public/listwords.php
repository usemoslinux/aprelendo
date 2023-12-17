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

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

use Aprelendo\Words;
use Aprelendo\WordTable;
use Aprelendo\SearchWordsParameters;
use Aprelendo\Pagination;
use Aprelendo\Url;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
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
            $html = <<<'HTML_SEARCH_RESULT'
            <div id="alert-box" class="alert alert-danger">
            <div class="alert-flag fs-5"><i class="bi bi-exclamation-circle-fill"></i>Oops!</div>
            <div class="alert-msg">
            <p>No words with that search criteria were found for the active language.</p>
            <p>Please, take a moment to fine-tune your search to improve your results. Keep in mind that searches are
            case-insensitive and include partial matches (i.e. 'cat' can find 'cats').</p>
            <p>With this in mind, feel free to modify your search query and try again.</p>
            </div>
            </div>
            HTML_SEARCH_RESULT;
        } else {
            $html = '<div class="alert alert-info" role="alert">You haven\'t marked any words for learning in this '
                .' language yet. Consider adding some as you read any text using Aprelendo.</div>';
            $html = <<<'HTML_EMPTY_LIST'
            <div id="alert-box" class="alert alert-danger">
            <div class="alert-flag fs-5"><i class="bi bi-exclamation-circle-fill"></i>Oops!</div>
            <div class="alert-msg">
            <p>Your word list for the currently active language is looking a bit lonely!</p>
            <p>But don't worry, it's super easy to add new words to your learning journey while you read texts using
            Aprelendo. Just check out this <a href="https://www.youtube.com/watch?v=qimkPHrLkS4" target="_blank"
            rel="noopener noreferrer" class="alert-link">helpful video</a> for a quick guide!</p>
            </div>
            </div>
            HTML_EMPTY_LIST;
        }
    }
} catch (\Throwable $e) {
    $html = <<<'HTML_UNEXPECTED_ERROR'
    <div id="alert-box" class="alert alert-danger">
    <div class="alert-flag fs-5"><i class="bi bi-exclamation-circle-fill"></i>Oops!</div>
    <div class="alert-msg">
    <p>There was an unexpected error trying to show your word list.</p>
    </div>
    </div>
    HTML_UNEXPECTED_ERROR;
} finally {
    echo $html;
}

require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window
require_once PUBLIC_PATH . 'showimportwordsmodal.php'; // load import words modal window

?>

<script defer src="/js/listwords.min.js"></script>
<script defer src="/js/tooltips.min.js"></script>

