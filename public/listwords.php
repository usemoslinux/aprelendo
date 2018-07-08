
<?php
require_once(PUBLIC_PATH . '/classes/words.php'); // loads Words class
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
$headings = array('Word', 'Status');
$col_widths = array('33', '*', '60');
$url = '';
$action_menu = array('mDelete' => 'Delete');
$sort_menu = array( 'mSortNewFirst' => 'New first', 
                    'mSortOldFirst' => 'Old first', 
                    'mSortLearnedFirst' => 'Learned first', 
                    'mSortForgottenFirst' => 'Forgotten first');

if (isset($_GET) && !empty($_GET)) { // if the page is loaded because user searched for something, show search results
    // initialize pagination variables
    if (isset($_GET['p'])) {
        $page = !empty($_GET['p']) ? $_GET['p'] : 1;
    }
    
    $search_text = isset($_GET['s']) && !empty($_GET['s']) ? $_GET['s'] : '';
    $search_text_escaped = $con->real_escape_string($search_text);
    
    $words_table = new Words($con, $user_id, $learning_lang_id);
    $total_rows = $words_table->countRowsFromSearch($search_text_escaped);
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->offset;

    // get search result
    $rows = $words_table->getSearch($search_text_escaped, $offset, $limit);

    // print table
    if (sizeof($rows) > 0) { // if there are any results, show them
        $table = New WordTable($headings, $col_widths, $rows, $url, $action_menu, $sort_menu);
        echo $table->print();
    } else { // if there are not, show a message
        echo '<p>No words found with that criteria. Try again.</p>';
    }

    // print pagination
    echo $pagination->print('words.php', $search_text);
} else { // if page is loaded at startup, just show word list
    // initialize pagination variables
    $page = isset($_GET['page']) && $_GET['page'] != '' ? $_GET['page'] : 1;
    $words_table = new Words($con, $user_id, $learning_lang_id);
    $total_rows = $words_table->countAllRows(); 
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->offset;
  
    // get word list
    $rows = $words_table->getAll($offset, $limit);

    // print table
    if (sizeof($rows) > 0) {
        $table = New WordTable($headings, $col_widths, $rows, $url, $action_menu, $sort_menu);
        echo $table->print();
    } else {
        echo '<p>There are no words in your private library.</p>';
    }

    // print pagination
    echo $pagination->print('words.php', '');
}


?>

  <script type="text/javascript" src="js/listwords.js"></script>