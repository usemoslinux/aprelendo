<div class="row flex">
    <div class="col-xs-12 col-md-9">
      <form class="" action="" method="post">
        <div class="input-group searchbox">
          <input type="text" id="search" name="searchtext" class="form-control" placeholder="Search...">
          <div class="input-group-btn">
            <button type="submit" name="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i></button>
          </div>
        </div>
      </form>
    </div>
    <div class="col-xs-12 col-md-3 searchbox">
      <button type="button" name="btn-addtext" class="btn btn-default" onclick="window.location='addtext.php'"><span class="glyphicon glyphicon-plus"></span> Add text</button>
      <button type="file" name="btn-upload" class="btn btn-success"><span class="glyphicon glyphicon-upload"></span> Upload text</button>
    </div>
</div>

<?php
// functions to print table header, contents & footer
function print_table_header() {
  echo '<div class="row">
  <div class="col-lg-12">
  <table id="textstable" class="table table-bordered">
  <colgroup><col width="33">
  <col width="*">
  <col width="90">
  </colgroup>
  <thead>
  <tr>
  <th class="col-checkbox"></th>
  <th class="col-title">Title</th>
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
  <ul class="dropdown-menu" aria-labelledby="actions-menu" role="menu">
  <li id="mMarkAsRead" role="presentation"><a href="#" role="menuitem">Mark as read</a></li>
  <li id="mDelete" role="presentation"><a href="#" role="menuitem">Delete</a></li>
  </ul>
  </div>
  </div>
  </div>';
}

function print_table_content($textID, $textTitle, $textStatus) {
  echo '<tr><td class="col-checkbox"><label><input type="checkbox" data-idText="' .  $textID . '"></label></td><td class="col-title"><a href ="showtext.php?id=' . $textID . '">' . $textTitle . '</td><td class="col-status">' . $textStatus . '</td></tr>';
}

// show page

require_once('db/dbinit.php'); // connect to database

if (isset($_POST['submit'])) { // if the page is loaded because user searched for something, show search results
  $searchtext = mysqli_real_escape_string($con, $_POST['searchtext']);
  $result = mysqli_query($con, "SELECT textID, textTitle, textWasRead FROM texts WHERE textTitle LIKE '%$searchtext%' ORDER BY textID") or die(mysqli_error($con));

  if (mysqli_num_rows($result) > 0) { // if there are any results, show them
    print_table_header();

    while ($row = mysqli_fetch_array($result)) {
      print_table_content($row['textID'], $row['textTitle'], ($row['textWasRead']) ? 'Read' : 'Not read');
    }
    print_table_footer();
  } else { // if there are not, show a message
    echo '<p>No texts found with that criteria. Try again.</p>';
  }
} else { // if page is loaded at startup, show start page
  $result = mysqli_query($con, "SELECT textID, textTitle, textWasRead FROM texts ORDER BY textID") or die(mysqli_error($con));
  if (mysqli_num_rows($result) > 0) {
    print_table_header();
    while ($row = mysqli_fetch_array($result)) {
      print_table_content($row['textID'], $row['textTitle'], ($row['textWasRead']) ? 'Read' : 'Not read');
    }
    print_table_footer();
  } else {
    echo '<p>There are no texts in your private library.</p>';
  }

}
?>

<script type="text/javascript">
$(document).ready(function() {

  $(document).ready(function() {
    $('#search').focus();
  });

  // action menu implementation

  // action: delete (deletes selected texts from db)
  $("#mDelete").on("click", function() {
    if (confirm("Really delete?")) {
      $("input[type=checkbox]:checked").each(function() {
        id = $(this).attr('data-idText');
        parentTR = $(this).closest('tr');

        deleteText(id, parentTR);
      });
      // if there are no remaining texts to show on the table, remove the entire table
      // TODO: THIS IS NOT WORKING !
      if ($('#textstable tr').length === 0) {
        $('#textstable').replaceWith('<p>There are no texts in your private library.</p>');
      }
    }
  });

  function deleteText(id, parentTR) {
    $.ajax({
      url: 'db/removetext.php',
      type: 'POST',
      data: {idText: id},
      success: function() {
        parentTR.remove();
      },
      error: function (request, status, error) {
        alert("There was an error when trying to delete the selected texts. Refresh the page and try again.");
      }
    });
  }

  $('#mMarkAsRead').on('click', function() {
      $('input[type=checkbox]:checked').each(function() {
        id = $(this).attr('data-idText');
        statusTD = $(this).closest('td').siblings('.col-status');
        MarkTextAsRead(id, statusTD);
        $(this).prop('checked', false);
      });
  });

  function MarkTextAsRead(id, statusTD) {
    $.ajax({
      url: 'db/finishedreading.php',
      type: 'POST',
      data: {textID: id},
      success: function() {
        statusTD.text('Read');
      },
      error: function (request, status, error) {
        alert("There was an error when trying to mark as read the selected texts. Refresh the page and try again.");
      }
    });
  }

  $('input[type=checkbox]').on('click', function() {
      if ($('input[type=checkbox]:checked').length === 0) {
          $('#actions-menu').addClass('disabled');
      } else {
        $('#actions-menu').removeClass('disabled');
      }
  });

});
</script>
