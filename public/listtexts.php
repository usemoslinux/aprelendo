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

use Aprelendo\Includes\Classes\Texts;
use Aprelendo\Includes\Classes\TextTable;
use Aprelendo\Includes\Classes\ArchivedTexts;
use Aprelendo\Includes\Classes\SearchTextsParameters;
use Aprelendo\Includes\Classes\Pagination;
use Aprelendo\Includes\Classes\Url;

// set variables used for pagination
$page = 1;
$limit = 10; // number of rows per page
$adjacents = 2; // adjacent page numbers

$sort_by = !empty($_GET['o']) ? $_GET['o'] : 0;

$html = ''; // HTML output to print

// if the page is loaded because user searched for something, show search results
// otherwise, show complete texts list

// initialize pagination variables
if (isset($_GET['p'])) {
    $page = !empty($_GET['p']) ? $_GET['p'] : 1;
}

$search_text = !empty($_GET['s']) ? $_GET['s'] : '';

// calculate page count for pagination
if ($show_archived) {
    $texts_table = new ArchivedTexts($pdo, $user_id, $lang_id);
} else {
    $texts_table = new Texts($pdo, $user_id, $lang_id);
}

$total_rows = $texts_table->countSearchRows($filter_type, $filter_level, $search_text);
$pagination = new Pagination($total_rows, $page, $limit, $adjacents);
$offset = $pagination->offset;

// get search result
$search_params = new SearchTextsParameters($filter_type, $filter_level, $search_text, $offset, $limit, $sort_by);
$rows = $texts_table->search($search_params);

// print table
if ($rows) { // if there are any results, show them
    $table = new TextTable($rows, $show_archived);
    $html = $table->print($sort_by);

    // print pagination
    $url_query_options = compact("search_text", "sort_by", "filter_type", "filter_level", "show_archived");
    $page_url = new Url('texts', $url_query_options);
    $html .= $pagination->print($page_url);
} else {
    if (!isset($_GET) || empty($_GET)) {
        if (!isset($_COOKIE['hide_welcome_msg'])) {
            $html = '<div class="alert alert-success alert-dismissible fade show" role="alert">'
                . '<p>Welcome! It seems this is your first time using Aprelendo. '
                . 'Follow these instructions to get started:</p>'
                . '<ol>'
                . '<li>Download and install our <a href="/extensions#extensions" target="_blank" '
                . 'rel="noopener noreferrer" class="alert-link">extensions</a> (Firefox, Chrome & Edge are supported). '
                . 'In case you are using another web browser (i.e. Safari, Opera or Internet Explorer) '
                . 'you should try installing our <a href="/extensions#bookmarklets" target="_blank" '
                . 'rel="noopener noreferrer" class="alert-link">bookmarklet</a>.</li>'
                . '<li>Go to any website containing an article or page written in the language you are trying '
                . 'to learn. Make sure it fits your level of proficiency or a little higher. Press the '
                . 'Aprelendo button, which appeared after installing the extension/bookmarklet. This will add '
                . 'the article to your Aprelendo library.</li>'
                . '<li>Open the newly added article and follow the instructions for each learning phase.</li></ol>'
                . '<p>For more info, check our video on <a href="https://www.youtube.com/watch?v=qimkPHrLkS4" '
                . 'target="_blank" rel="noopener noreferrer" class="alert-link">how our assisted learning method works'
                . '</a>.<p>'
                . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                . '</div>';
        }

        $html .= '<div class="alert alert-info" role="alert">Your private library is empty. '
            . 'Add texts using the green button above or using our extensions, as explained '
            . '<a href="https://www.youtube.com/watch?v=qimkPHrLkS4" target="_blank" rel="noopener noreferrer"'
            . 'class="alert-link">here</a>.';
    } else {
        $html = '<div class="alert alert-info" role="alert">'
            . 'Oops! There are no texts meeting your search criteria.</div>';
    }
}

echo $html;
?>

<script defer src="/js/cookies.min.js"></script>
<script defer src="/js/listtexts.min.js"></script>
