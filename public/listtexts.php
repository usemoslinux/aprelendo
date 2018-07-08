<?php
require_once(PUBLIC_PATH . '/classes/texts.php'); // loads Texts class
require_once(PUBLIC_PATH . '/classes/archivedtexts.php'); // loads ArchivedTexts class
require_once(PUBLIC_PATH . '/classes/table.php'); // table class
require_once(PUBLIC_PATH . '/classes/pagination.php'); // pagination class
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

// set variables used for pagination
$page = 1;
$limit = 10; // number of rows per page
$adjacents = 2; // adjacent page numbers

// set variables used for creating the table
$headings = array('Title');
$col_widths = array('33', '*');
$url = $show_archived ? '' : 'showtext.php';
$action_menu = $show_archived ? array('mArchive' => 'Unarchive', 'mDelete' => 'Delete') : array('mArchive' => 'Archive', 'mDelete' => 'Delete');
$sort_menu = array( 'mSortByNew' => 'New', 'mSortByHot' => 'Hot');

if (isset($_GET) && !empty($_GET)) { // if the page is loaded because user searched for something, show search results
    // initialize pagination variables
    if (isset($_GET['p'])) {
        $page = !empty($_GET['p']) ? $_GET['p'] : 1;
    }
    
    $search_text_escaped = $con->real_escape_string($search_text);
    
    // calculate page count for pagination
    if ($show_archived) {
        $texts_table = new ArchivedTexts($con, $user_id, $learning_lang_id);
    } else {
        $texts_table = new Texts($con, $user_id, $learning_lang_id);
    }
    
    $total_rows = $texts_table->countRowsFromSearch($filter_sql, $search_text_escaped);
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->offset;
    
    // get search result
    $rows = $texts_table->getSearch($filter_sql, $search_text_escaped, $offset, $limit);
    
    // print table
    if (sizeof($rows) > 0) { // if there are any results, show them
        $table = New TextTable($headings, $col_widths, $rows, $url, $action_menu, $sort_menu);
        echo $table->print();

        echo $pagination->print('texts.php', $search_text, $filter, $show_archived); // print pagination
    } else { // if there are no texts to show, print a message
        echo '<p>No texts found with that criteria. Try again.</p>';
    }
} else { // if page is loaded at startup, show start page
    // initialize pagination variables
    $page = isset($_GET['p']) && $_GET['p'] != '' ? $_GET['p'] : 1;
    
    if ($show_archived) {
        $texts_table = new ArchivedTexts($con, $user_id, $learning_lang_id);
        // $result = $con->query("SELECT COUNT(atextID) FROM archivedtexts WHERE atextUserId='$user_id' AND atextLgId='$learning_lang_id'") or die(mysqli_error($con));
    } else {
        $texts_table = new Texts($con, $user_id, $learning_lang_id);
        // $result = $con->query("SELECT COUNT(textID) FROM texts WHERE textUserId='$user_id' AND textLgId='$learning_lang_id'") or die(mysqli_error($con));
    }

    $total_rows = $texts_table->countAllRows();
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->offset;
    
    // get text list
    $rows = $texts_table->getAll($offset, $limit);
    
    // print table
    if (sizeof($rows) > 0) {
        $table = New TextTable($headings, $col_widths, $rows, $url, $action_menu, $sort_menu);
        echo $table->print();

        echo $pagination->print('texts.php', '', $filter, $show_archived); // print pagination
    } else { // if there are no texts to show, print a message
        echo '<p>There are no texts in your private library.</p>';
    }
    
}
?>

<script type="text/javascript" src="js/listtexts.js"></script>
