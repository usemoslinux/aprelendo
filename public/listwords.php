<?php
// functions to print table header, contents & footer
function print_table_header() {
    echo '<div class="row">
    <div class="col-lg-12">
    <table id="wordstable" class="table table-bordered">
    <colgroup><col width="33">
    <col width="*">
    <col width="90">
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

    echo '<li id="mDelete" role="presentation"><a href="#" role="menuitem">Delete word</a></li>
    </ul>
    </div>
    </div>
    </div>';
}

function print_table_content($wordID, $word, $wordStatus) {
    echo '<tr><td class="col-checkbox"><label><input class="chkbox-selrow" type="checkbox" data-idWord="' .
        $wordID . '"></label></td><td class="col-title">' . 
        $word . '</td><td class="col-status">' .
        $wordStatus . '</td></tr>';
}

// show page

require_once('db/dbinit.php'); // connect to database
$actlangid = $_SESSION['actlangid'];

if (isset($_POST['submit'])) { // if the page is loaded because user searched for something, show search results
    $searchtext = mysqli_real_escape_string($con, $_POST['searchtext']);
    // decide whether to show active or archived texts
    $result = mysqli_query($con, "SELECT wordID, word, wordStatus FROM words WHERE word LIKE '%$searchtext%' AND textLgId='$actlangid' ORDER BY wordID DESC") or die(mysqli_error($con));

    if (mysqli_num_rows($result) > 0) { // if there are any results, show them
        print_table_header();

        while ($row = mysqli_fetch_array($result)) {
           print_table_content($row['wordID'], $row['word'], $row['wordStatus']);
        }
        print_table_footer();
    } else { // if there are not, show a message
        echo '<p>No words found with that criteria. Try again.</p>';
    }
} else { // if page is loaded at startup, show start page
    // decide whether to show active or archived texts
    $result = mysqli_query($con, "SELECT wordID, word, wordStatus FROM words WHERE textLgId='$actlangid' ORDER BY wordID DESC") or die(mysqli_error($con));

    if (mysqli_num_rows($result) > 0) {
        print_table_header();
        while ($row = mysqli_fetch_array($result)) {
            print_table_content($row['wordID'], $row['word'], $row['wordStatus']);
        }
        print_table_footer();
    } else {
        echo '<p>There are no words in your private library.</p>';
    }

}
?>

  <script type="text/javascript" src="js/listwords.js"></script>