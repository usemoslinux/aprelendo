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
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & checks if user is logged in

use Aprelendo\Includes\Classes\Words;
use Aprelendo\Includes\Classes\WordTable;
use Aprelendo\Includes\Classes\Pagination;

$user_id = $user->getId();
$lang_id = $user->getLangId();

// set variables used for pagination
$page = 1;
$limit = 10; // number of rows per page
$adjacents = 2; // adjacent page numbers

// set variables used for creating the table
$headings = array('Word', 'Status');
$col_widths = array('33px', '', '60px');
$action_menu = array('mDelete' => 'Delete');
$sort_menu = array('mSortNewFirst' => 'New first',
                   'mSortOldFirst' => 'Old first',
                   'mSortLearnedFirst' => 'Learned first',
                   'mSortForgottenFirst' => 'Forgotten first');
$sort_by = isset($_GET['o']) && !empty($_GET['o']) ? $_GET['o'] : 0;

// if the page is loaded because user searched for something, show search results
// otherwise, show complete word list

// initialize pagination variables
if (isset($_GET['p'])) {
    $page = isset($_GET['p']) && !empty($_GET['p']) ? $_GET['p'] : 1;
}

$search_text = isset($_GET['s']) && !empty($_GET['s']) ? $_GET['s'] : '';

$words_table = new Words($pdo, $user_id, $lang_id);
$total_rows = $words_table->countSearchRows($search_text);
$pagination = new Pagination($total_rows, $page, $limit, $adjacents);
$offset = $pagination->getOffset();

try {
    // get search result
    $rows = $words_table->getSearch($search_text, $offset, $limit, $sort_by);

    // print table
    if (sizeof($rows) > 0) { // if there are any results, show them
        $table = new WordTable($headings, $col_widths, $rows, $action_menu, $sort_menu);
        echo $table->print($sort_by);
    }

    // print pagination
    echo $pagination->print('words', $search_text, $sort_by);
} catch (\Exception $e) {
    if (isset($_GET) && !empty($_GET)) {
        echo '<div class="alert alert-info" role="alert">No words found with that criteria. Try again.</div>';
    } else {
        echo '<div class="alert alert-info" role="alert">There are no words in your private library.</div>';
    }
}

require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window

?>

<script defer src="js/listwords-min.js"></script>
