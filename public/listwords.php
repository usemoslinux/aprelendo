<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/solid.css" integrity="sha384-Rw5qeepMFvJVEZdSo1nDQD5B6wX0m7c5Z/pLNvjkB14W6Yki1hKbSEQaX9ffUbWe" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/fontawesome.css" integrity="sha384-GVa9GOgVQgOk+TNYXu7S/InPTfSDTtBalSgkgqQ7sCik56N9ztlkoTr2f/T44oKV" crossorigin="anonymous">

<?php
// functions to print table header, contents & footer
function print_table_header() {
    echo '<div class="row">
    <div class="col-lg-12">
    <table id="wordstable" class="table table-bordered">
    <colgroup><col width="33">
    <col width="*">
    <col width="60">
    </colgroup>
    <thead>
    <tr>
    <th class="col-checkbox"><input id="chkbox-selall" type="checkbox"></th>
    <th class="col-title">Word/Phrase</th>
    <th class="col-status">Status</th>
    </tr>
    </thead>
    <tbody>';
}

function print_table_footer() {
    echo '</tbody>
    </table>

    <div class="dropdown">
    <button class="btn btn-default dropdown-toggle disabled" type="button" id="actions-menu" data-toggle="dropdown">Actions <span class="caret"></span></button>
    <ul class="dropdown-menu" aria-labelledby="actions-menu" role="menu">';

    echo '<li id="mDelete" role="presentation"><a href="#" role="menuitem">Delete</a></li>
    </ul>
    </div>
    </div>
    </div>';
}

function print_table_content($wordID, $word, $wordStatus) {
    $status = array('fa-hourglass-end status_learned', 'fa-hourglass-half status_learning', 'fa-hourglass-start status_new');
    $status_text = array('Learned', 'Learning', 'New');

    echo '<tr><td class="col-checkbox"><label><input class="chkbox-selrow" type="checkbox" data-idWord="' .
        $wordID . '"></label></td><td class="col-title">' . 
        $word . '</td><td class="col-status text-center">' .
        '<i title="' . $status_text[$wordStatus] . '" class="fas ' . $status[$wordStatus] . '"></i></td></tr>';
}

// show page

require_once('db/dbinit.php'); // connect to database
require_once('pagination.php'); // pagination class
$actlangid = $_COOKIE['actlangid'];

$page = 1;
$limit = 10; // number of rows per page
$adjacents = 2; // adjacent page numbers

if (isset($_POST['submit']) || isset($_GET['search'])) { // if the page is loaded because user searched for something, show search results
    // pagination
    if (isset($_POST['searchtext'])) {
      $search_text = $_POST['searchtext'];
    } else {
      $search_text = isset($_GET['search']) && $_GET['search'] != '' ? $_GET['search'] : '';
      $page = isset($_GET['page']) && $_GET['page'] != '' ? $_GET['page'] : 1;
    }
    
    $search_text_escaped = mysqli_real_escape_string($con, $search_text);
    
    $result = mysqli_query($con, "SELECT COUNT(word) FROM words WHERE word LIKE '%$search_text_escaped%' AND wordLgId='$actlangid'");
    $row = mysqli_fetch_array($result);
    $total_rows = $row[0]; // total number of rows to show
    
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->offset;

    // show search result
    $result = mysqli_query($con, "SELECT wordID, word, wordStatus FROM words WHERE word LIKE '%$search_text_escaped%' AND wordLgId='$actlangid' ORDER BY wordID DESC LIMIT $offset, $limit") or die(mysqli_error($con));

    if (mysqli_num_rows($result) > 0) { // if there are any results, show them
        print_table_header();

        while ($row = mysqli_fetch_array($result)) {
           print_table_content($row['wordID'], $row['word'], $row['wordStatus']);
        }
        print_table_footer();
    } else { // if there are not, show a message
        echo '<p>No words found with that criteria. Try again.</p>';
    }

    echo $pagination->print('words.php', $search_text); // print pagination
} else { // if page is loaded at startup, just show word list
    // pagination
    $page = isset($_GET['page']) && $_GET['page'] != '' ? $_GET['page'] : 1;
    $result = mysqli_query($con, "SELECT COUNT(word) FROM words WHERE wordLgId='$actlangid'");
    $row = mysqli_fetch_array($result);
    $total_rows = $row[0]; // total number of rows to show
    
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->offset;
  
    // show word list
    $result = mysqli_query($con, "SELECT wordID, word, wordStatus FROM words WHERE wordLgId='$actlangid' ORDER BY wordID DESC LIMIT $offset, $limit") or die(mysqli_error($con));

    if (mysqli_num_rows($result) > 0) {
        print_table_header();
        while ($row = mysqli_fetch_array($result)) {
            print_table_content($row['wordID'], $row['word'], $row['wordStatus']);
        }
        print_table_footer();
    } else {
        echo '<p>There are no words in your private library.</p>';
    }

    echo $pagination->print('words.php', ''); // print pagination
}


?>

  <script type="text/javascript" src="js/listwords.js"></script>