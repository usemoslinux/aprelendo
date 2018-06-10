<?php
// functions to print table header, contents & footer
function print_table_header() {
    echo '<div class="row">
    <div class="col-lg-12">
    <table id="textstable" class="table table-bordered">
    <colgroup><col width="33">
    <col width="*">
    </colgroup>
    <thead>
    <tr>
    <th class="col-checkbox"><input id="chkbox-selall" type="checkbox"></th>
    <th class="col-title">Title</th>
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

    global $showarchivedtexts;
    if($showarchivedtexts) {
        echo '<li id="mArchive" role="presentation"><a href="#" role="menuitem">Unarchive</a></li>';
    } else {
        echo '<li id="mArchive" role="presentation"><a href="#" role="menuitem">Archive</a></li>';
    }

    echo '<li id="mDelete" role="presentation"><a href="#" role="menuitem">Delete</a></li>
    </ul>
    </div>
    </div>
    </div>';
}

function print_table_content($textID, $textTitle) {
    global $showarchivedtexts;
    $link = $showarchivedtexts ? '' : '<a href ="showtext.php?id=' . $textID . '">';
    echo '<tr><td class="col-checkbox"><label><input class="chkbox-selrow" type="checkbox" data-idText="' .
        $textID . '"></label></td><td class="col-title">' . $link .
        $textTitle . '</td></tr>';
}

// show page

require_once('db/dbinit.php'); // connect to database
require_once('pagination.php'); // pagination class
$actlangid = isset($actlangid) ? $actlangid : $_COOKIE['actlangid'];

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
    
    if ($showarchivedtexts) {
      $result = mysqli_query($con, "SELECT COUNT(atextID) FROM archivedtexts WHERE atextTitle LIKE '%$search_text_escaped%' AND atextLgId='$actlangid'");
    } else {
      $result = mysqli_query($con, "SELECT COUNT(textID) FROM texts WHERE textTitle LIKE '%$search_text_escaped%' AND textLgId='$actlangid'");
    }
    
    $row = mysqli_fetch_array($result);
    $total_rows = $row[0]; // total number of rows to show
    
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->offset;

    // show search result
    // decide whether to show active or archived texts
    if ($showarchivedtexts) {
        $result = mysqli_query($con, "SELECT atextID, atextTitle FROM archivedtexts WHERE atextTitle LIKE '%$search_text_escaped%' AND atextLgId='$actlangid' ORDER BY atextID DESC LIMIT $offset, $limit") or die(mysqli_error($con));
    } else {
        $result = mysqli_query($con, "SELECT textID, textTitle FROM texts WHERE textTitle LIKE '%$search_text_escaped%' AND textLgId='$actlangid' ORDER BY textID DESC LIMIT $offset, $limit") or die(mysqli_error($con));
    }

    if (mysqli_num_rows($result) > 0) { // if there are any results, show them
        print_table_header();

        while ($row = mysqli_fetch_array($result)) {
            if ($showarchivedtexts) {
                print_table_content($row['atextID'], $row['atextTitle'], '');
            } else {
                print_table_content($row['textID'], $row['textTitle'], '');
            }
        }
        print_table_footer();
        $page_name = $showarchivedtexts ? 'archivedtexts.php' : 'index.php';
        echo $pagination->print($page_name, $search_text); // print pagination
    } else { // if there are no texts to show, print a message
        echo '<p>No texts found with that criteria. Try again.</p>';
    }
} else { // if page is loaded at startup, show start page
    // pagination
    $page = isset($_GET['page']) && $_GET['page'] != '' ? $_GET['page'] : 1;
    
    if ($showarchivedtexts) {
        $result = mysqli_query($con, "SELECT COUNT(atextID) FROM archivedtexts WHERE atextLgId='$actlangid'") or die(mysqli_error($con));
    } else {
        $result = mysqli_query($con, "SELECT COUNT(textID) FROM texts WHERE textLgId='$actlangid'") or die(mysqli_error($con));
    }
    $row = mysqli_fetch_array($result);
    $total_rows = $row[0]; // total number of rows to show
    
    $pagination = new Pagination($page, $limit, $total_rows, $adjacents);
    $offset = $pagination->offset;

    // show word list
    // decide whether to show active or archived texts
    if ($showarchivedtexts) {
        $result = mysqli_query($con, "SELECT atextID, atextTitle FROM archivedtexts WHERE atextLgId='$actlangid' ORDER BY atextID DESC LIMIT $offset, $limit") or die(mysqli_error($con));
    } else {
        $result = mysqli_query($con, "SELECT textID, textTitle FROM texts WHERE textLgId='$actlangid' ORDER BY textID DESC LIMIT $offset, $limit") or die(mysqli_error($con));
    }

    if (mysqli_num_rows($result) > 0) {
        print_table_header();
        while ($row = mysqli_fetch_array($result)) {
            if ($showarchivedtexts) {
                print_table_content($row['atextID'], $row['atextTitle'], '');
            } else {
                print_table_content($row['textID'], $row['textTitle'], '');
            }
        }
        print_table_footer();
        $page_name = $showarchivedtexts ? 'archivedtexts.php' : 'index.php';
        echo $pagination->print($page_name, ''); // print pagination
    } else { // if there are no texts to show, print a message
        echo '<p>There are no texts in your private library.</p>';
    }

}
?>

<script type="text/javascript" src="js/listtexts.js"></script>
