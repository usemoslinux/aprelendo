$(document).ready(function() {

  $('#search').focus();

  // action menu implementation

  /**
  * Deletes selected texts from the database
  * Trigger: when user selects "Delete" in the action menu
  */
  $("#mDelete").on("click", function() {
    if (confirm("Really delete?")) {
      $("input[type=checkbox]:checked").each(function() {
        var id = $(this).attr('data-idText');
        var parentTR = $(this).closest('tr');

        /**
        * Deletes selected texts from the database (based on their ID).
        * When done, also removes selected rows from HTML table.
        * @param  {integer} id Id of the selected element in the database
        * @param  {jQuery object} parentTR Parent TR element of the selected checkbox
        */
        $.ajax({
          url: 'db/removetext.php',
          type: 'POST',
          data: {idText: id},
          success: function() {
            parentTR.remove();
            // if there are no remaining texts to show on the table, remove the entire table
            if ($('#textstable tbody').is(':empty')) {
              $('#textstable').replaceWith('<p>There are no texts in your private library.</p>');
              $('#actions-menu').remove();
            }
          },
          error: function (request, status, error) {
            alert("There was an error when trying to delete the selected texts. Refresh the page and try again.");
          }
        });

      });
    }
  });

  /**
  * Archives selected texts
  * Trigger: when user selects "Archive" in the action menu
  */
  $('#mArchive').on('click', function() {
    var archivetxt = $(this).text() === 'Archive text';
    $('input[type=checkbox]:checked').each(function() {
      var id = $(this).attr('data-idText');
      var parentTR = $(this).closest('tr');

      /**
      * Moves selected texts from the "texts" table to the "archivedtexts" table in the database (archive);
      * or, vice-versa, moves texts from the "archivedtexts" table to the "texts" table (unarchive)
      * This is done based on text IDs.
      * When done, also removes selected rows from the HTML table.
      * @param {integer} id Id of the selected element in the database
      * @param {jQuery object} parentTR Parent TR element of the selected checkbox
      * @param {boolean} archivetxt If true, archive text; if false, unarchive text
      */
      $.ajax({
        url: 'db/archivetext.php',
        type: 'POST',
        data: {textID: id, archivetext: archivetxt},
        success: function() {
          parentTR.remove();
        },
        error: function (request, status, error) {
          alert("There was an error when trying to archive the selected texts. Refresh the page and try again.");
        }
      });
    });
  });

  /**
  * Enables/Disables action menu based on the number of selected elements.
  * If there is at least 1 element selected, it enables it. Otherwise, it is disabled.
  */
  $(document).on('change', '.txt-checkbox', function() {
    if ($('input[type=checkbox]:checked').length === 0) {
      $('#actions-menu').addClass('disabled');
    } else {
      $('#actions-menu').removeClass('disabled');
    }
  });

  /**
  * Selects/Unselects all texts from the list
  */
  $(document).on('click', '.alltxt-checkbox', function() {
    var chkboxes = $('.txt-checkbox');
    chkboxes.prop('checked', $(this).prop('checked'));
    toggleActionMenu();
  });

});
