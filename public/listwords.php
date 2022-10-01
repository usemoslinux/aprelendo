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
$sort_menu = array( 'mSortNewFirst' => 'New first', 
                    'mSortOldFirst' => 'Old first', 
                    'mSortLearnedFirst' => 'Learned first', 
                    'mSortForgottenFirst' => 'Forgotten first');
$sort_by = isset($_GET['o']) && !empty($_GET['o']) ? $_GET['o'] : 0;

if (isset($_GET) && !empty($_GET)) { // if the page is loaded because user searched for something, show search results
    // initialize pagination variables
    if (isset($_GET['p'])) {
        $page = isset($_GET['p']) && !empty($_GET['p']) ? $_GET['p'] : 1;
    }
    
    $search_text = isset($_GET['s']) && !empty($_GET['s']) ? $_GET['s'] : '';
    
    $words_table = new Words($pdo, $user_id, $lang_id);
    $total_rows = $words_table->countSearchRows($search_text);
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->getOffset();

    // get search result
    $rows = $words_table->getSearch($search_text, $offset, $limit, $sort_by);

    // print table
    if (sizeof($rows) > 0) { // if there are any results, show them
        $table = New WordTable($headings, $col_widths, $rows, $action_menu, $sort_menu);
        echo $table->print($sort_by);
    } else { // if there are not, show a message
        echo '<p>No words found with that criteria. Try again.</p>';
    }

    // print pagination
    echo $pagination->print('words', $search_text, $sort_by);
} else { // if page is loaded at startup, just show word list
    // initialize pagination variables
    $page = isset($_GET['page']) && $_GET['page'] != '' ? $_GET['page'] : 1;
    $words_table = new Words($pdo, $user_id, $lang_id);
    $total_rows = $words_table->countAllRows(); 
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->getOffset();
  
    // get word list
    $rows = $words_table->getAll($offset, $limit, $sort_by);

    // print table
    if ($rows && sizeof($rows) > 0) {
        $table = New WordTable($headings, $col_widths, $rows, $action_menu, $sort_menu);
        echo $table->print($sort_by);
    } else {
        echo '<p>There are no words in your private library.</p>';
    }

    // print pagination
    echo $pagination->print('words', '', $sort_by);
}

require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window

?>

  <script defer src="js/listwords-min.js"></script>