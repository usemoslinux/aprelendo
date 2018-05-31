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
$actlangid = isset($actlangid) ? $actlangid : $_COOKIE['actlangid'];

if (isset($_POST['submit'])) { // if the page is loaded because user searched for something, show search results
    $searchtext = mysqli_real_escape_string($con, $_POST['searchtext']);
    // decide whether to show active or archived texts
    if ($showarchivedtexts) {
        $result = mysqli_query($con, "SELECT atextID, atextTitle FROM archivedtexts WHERE atextTitle LIKE '%$searchtext%' AND atextLgId='$actlangid' ORDER BY atextID DESC") or die(mysqli_error($con));
    } else {
        $result = mysqli_query($con, "SELECT textID, textTitle FROM texts WHERE textTitle LIKE '%$searchtext%' AND textLgId='$actlangid' ORDER BY textID DESC") or die(mysqli_error($con));
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
    } else { // if there are not, show a message
        echo '<p>No texts found with that criteria. Try again.</p>';
    }
} else { // if page is loaded at startup, show start page
    // decide whether to show active or archived texts
    if ($showarchivedtexts) {
        $result = mysqli_query($con, "SELECT atextID, atextTitle FROM archivedtexts WHERE atextLgId='$actlangid' ORDER BY atextID DESC") or die(mysqli_error($con));
    } else {
        $result = mysqli_query($con, "SELECT textID, textTitle FROM texts WHERE textLgId='$actlangid' ORDER BY textID DESC") or die(mysqli_error($con));
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
    } else {
        echo '<p>There are no texts in your private library.</p>';
    }

}
?>

<script type="text/javascript" src="js/listtexts.js"></script>
