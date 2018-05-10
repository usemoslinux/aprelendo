$(document).ready(function() {

  $('#search').focus();

  // action menu implementation

  /**
  * Deletes selected texts from the database
  * Trigger: when user selects "Delete" in the action menu
  */
  $("#mDelete").on("click", function() {
    if (confirm("Really delete?")) {
      var ids = [];
      $("input[class=txt-checkbox]:checked").each(function() {
        ids.push($(this).attr('data-idText'));
        //var parentTR = $(this).closest('tr');
      });

      /**
      * Deletes selected texts from the database (based on their ID).
      * When done, also removes selected rows from HTML table.
      * @param  {integer array} textIDs Ids of the selected elements in the database
      */
      $.ajax({
        url: 'db/removetext.php',
        type: 'POST',
        data: {textIDs: JSON.stringify(ids)},
        success: function() {
          $("input[class=txt-checkbox]:checked").each(function() {
            $(this).closest('tr').remove();
          });
          // if there are no remaining texts to show on the table, remove the entire table
          removeTableIfEmpty();
        },
        error: function (request, status, error) {
          alert("There was an error when trying to delete the selected texts. Refresh the page and try again.");
        }
      });
    }
  });

  /**
  * Archives selected texts
  * Trigger: when user selects "Archive" in the action menu
  */
  $('#mArchive').on('click', function() {
    var archivetxt = $(this).text() === 'Archive text';
    var ids = [];
    $('input[class=txt-checkbox]:checked').each(function() {
      ids.push($(this).attr('data-idText'));
      //parentTRs.push($(this).closest('tr'));
    });

    /**
    * Moves selected texts from the "texts" table to the "archivedtexts" table in the database (archive);
    * or, vice-versa, moves texts from the "archivedtexts" table to the "texts" table (unarchive)
    * This is done based on text IDs.
    * When done, also removes selected rows from the HTML table.
    * @param {integer array} ids Ids of the selected elements in the database
    * @param {boolean} archivetxt If true, archive text; else, unarchive text
    */
    $.ajax({
      url: 'db/archivetext.php',
      type: 'POST',
      data: {textIDs: JSON.stringify(ids), archivetext: archivetxt},
      success: function() {
        $('input[class=txt-checkbox]:checked').each(function() {
          $(this).closest('tr').remove();
        });
        removeTableIfEmpty();
      },
      error: function (request, status, error) {
        alert("There was an error when trying to archive the selected texts. Refresh the page and try again.");
      }
    });

  });

  /**
  * Enables/Disables action menu based on the number of selected elements.
  * If there is at least 1 element selected, it enables it. Otherwise, it is disabled.
  */
  function toggleActionMenu() {
      if ($('input[type=checkbox]:checked').length === 0) {
          $('#actions-menu').addClass('disabled');
      } else {
          $('#actions-menu').removeClass('disabled');
      }
    }

  $(document).on('change', '.txt-checkbox', toggleActionMenu);

  /**
  * Selects/Unselects all texts from the list
  */
  $(document).on('click', '.alltxt-checkbox', function() {
    var chkboxes = $('.txt-checkbox');
    chkboxes.prop('checked', $(this).prop('checked'));
    toggleActionMenu();
  });

  function removeTableIfEmpty() {
    if ($('#textstable tbody').is(':empty')) {
      if ($('#search').val() == '') {
        $('#textstable').replaceWith('<p>There are no texts in your private library.</p>');
      } else {
        $('#textstable').replaceWith('<p>There are no texts in your private library that meet that search criteria.</p>');
      }
      $('#actions-menu').remove();
    }
  }

});
