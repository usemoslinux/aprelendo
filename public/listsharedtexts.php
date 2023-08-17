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
require_once APP_ROOT . 'includes/checklogin.php';

use Aprelendo\Includes\Classes\SharedTexts;
use Aprelendo\Includes\Classes\SharedTextTable;
use Aprelendo\Includes\Classes\SearchTextsParameters;
use Aprelendo\Includes\Classes\Pagination;
use Aprelendo\Includes\Classes\Url;

// set variables used for pagination
$page = 1;
$limit = 10; // number of rows per page
$adjacents = 2; // adjacent page numbers

$sort_by = isset($_GET['o']) && !empty($_GET['o']) ? $_GET['o'] : 0;

$html = ''; // HTML output to print

// if the page is loaded because user searched for something, show search results
// otherwise, show complete shared texts list

// initialize pagination variables
if (isset($_GET['p'])) {
    $page = isset($_GET['p']) && $_GET['p'] != '' ? $_GET['p'] : 1;
}

$search_text = isset($_GET['s']) && !empty($_GET['s']) ? $_GET['s'] : '';

$texts_table = new SharedTexts($pdo, $user_id, $lang_id);

$total_rows = $texts_table->countSearchRows($filter_type, $filter_level, $search_text);
$pagination = new Pagination($total_rows, $page, $limit, $adjacents);
$offset = $pagination->getOffset();

try {
    // get search result
    $search_params = new SearchTextsParameters($filter_type, $filter_level, $search_text, $offset, $limit, $sort_by);
    $rows = $texts_table->search($search_params);

    // print table
    if ($rows) {
        // if there are any results, show them
        $table = new SharedTextTable($rows);
        $html = $table->print($sort_by);
        // print pagination
        $url_query_options = compact("search_text", "sort_by", "filter_type", "filter_level");
        $page_url = new Url('sharedtexts', $url_query_options);
        $html .= $pagination->print($page_url);
    }
} catch (\Exception $e) {
    // if there are no texts to show, print a message
    if (isset($_GET) && !empty($_GET)) {
        $html = '<div class="alert alert-info" role="alert">No shared texts found with that criteria. Try again.</div>';
    } else {
        $html = '<div class="alert alert-info" role="alert">There are still no shared texts for this language. '
            . 'Be the first to add one!</div>';
    }
}

echo $html;
?>

<script defer src="js/listtexts.min.js"></script>
<script defer src="js/likes.min.js"></script>
