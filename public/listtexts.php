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

use Aprelendo\Includes\Classes\Texts;
use Aprelendo\Includes\Classes\TextTable;
use Aprelendo\Includes\Classes\ArchivedTexts;
use Aprelendo\Includes\Classes\Pagination;

// set variables used for pagination
$page = 1;
$limit = 10; // number of rows per page
$adjacents = 2; // adjacent page numbers

// set variables used for creating the table
$headings = array('Title');
$col_widths = array('33px', '');
$action_menu = $show_archived ? array('mArchive' => 'Unarchive', 'mDelete' => 'Delete') : array('mArchive' => 'Archive', 'mDelete' => 'Delete');
$sort_menu = array( 'mSortByNew' => 'New first', 'mSortByOld' => 'Old first');
$sort_by = isset($_GET['o']) && !empty($_GET['o']) ? $_GET['o'] : 0;

$html = ''; // HTML output to print

if (isset($_GET) && !empty($_GET)) { // if the page is loaded because user searched for something, show search results
    // initialize pagination variables
    if (isset($_GET['p'])) {
        $page = !empty($_GET['p']) ? $_GET['p'] : 1;
    }
    
    // calculate page count for pagination
    if ($show_archived) {
        $texts_table = new ArchivedTexts($pdo, $user_id, $lang_id);
    } else {
        $texts_table = new Texts($pdo, $user_id, $lang_id);
    }
    
    $total_rows = $texts_table->countSearchRows($filter_type, $filter_level, $search_text);
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->getOffset();
    
    // get search result
    try {
        $rows = $texts_table->getSearch($filter_type, $filter_level, $search_text, $offset, $limit, $sort_by);

        // print table
        if ($rows) { // if there are any results, show them
            $table = New TextTable($headings, $col_widths, $rows, $show_archived, $action_menu, $sort_menu);
            $html = $table->print($sort_by);
            $html .= $pagination->print('texts.php', $search_text, $sort_by, $filter_type, $filter_level, $show_archived); // print pagination
        }
    } catch (\Exception $e) {
        $html = '<p>' . $e->getMessage() . '</p>';
    }
} else { // if page is loaded at startup, show start page
    // initialize pagination variables
    $page = isset($_GET['p']) && $_GET['p'] != '' ? $_GET['p'] : 1;
    
    if ($show_archived) {
        $texts_table = new ArchivedTexts($pdo, $user_id, $lang_id);
    } else {
        $texts_table = new Texts($pdo, $user_id, $lang_id);
    }

    $total_rows = $texts_table->countAllRows();
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->getOffset();
    
    // get text list
    try {
        $rows = $texts_table->getAll($offset, $limit, $sort_by);
    
        // print table
        if ($rows) {
            $table = New TextTable($headings, $col_widths, $rows, $show_archived, $action_menu, $sort_menu);
            $html = $table->print($sort_by);
            $html .= $pagination->print('texts.php', '', $sort_by, $filter_type, $filter_level, $show_archived); // print pagination
        } else { // if there are no texts to show, print a message
            if (!isset($_COOKIE['hide_welcome_msg'])) {
                
            $html = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <p><strong>Welcome!</strong> It seems this is your first time using Aprelendo. Follow these instructions to get started:</p>
                        <ol>
                            <li>Download and install our extensions (<a href="https://addons.mozilla.org/en-US/firefox/addon/aprelendo/" target="_blank" rel="noopener noreferrer">Firefox</a> & <a href="https://chrome.google.com/webstore/detail/aprelendo/aocicejjgilfkeeklfcomejgphjhjonj/related?hl=en-US" target="_blank" rel="noopener noreferrer">Chrome</a> are supported). In case you are using another web browser (i.e. Safari, Opera or Internet Explorer) you should try installing our <a href="extensions.php#bookmarklets" target="_blank" rel="noopener noreferrer">bookmarklet</a>.</li>
                            <li>Go to any website containing an article or page written in the language you are trying to learn. Make sure it fits your level of proficiency or a little higher. Press the aprelendo button, which  appeared after installing the extension/bookmarklet. This will add the article to your Aprelendo library. </li>
                            <li>Open the newly added article and follow the instructions for each learning phase. For more info, check our video on <a href="https://www.youtube.com/watch?v=5HLr9uxJNDs" target="_blank" rel="noopener noreferrer">how our assisted learning method works</a>.</li>
                        </ol>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            }
            
            $html .= '<p class="text-center">Your private library is empty. Check out some <a href="sources.php">popular sources</a> for this language.</p>';
        }
    } catch (\Exception $e) {
        $html = '<p class="text-center">' . $e->getMessage() . '</p>';
    }
}

echo $html;
?>

<script defer src="js/cookies.js"></script>
<script defer src="js/listtexts.js"></script>
