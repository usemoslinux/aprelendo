$(document).ready(function() {

  $('#search').focus();

  // action menu implementation

  /**
  * Deletes selected words from the database
  * Trigger: when user selects "Delete" in the action menu
  */
  $("#mDelete").on("click", function() {
    if (confirm("Really delete?")) {
      var ids = [];
      $("input[class=txt-checkbox]:checked").each(function() {
        ids.push($(this).attr('data-idWord'));
      });

      /**
      * Deletes selected words from the database (based on their ID).
      * When done, also removes selected rows from HTML table.
      * @param  {integer array} textIDs Ids of the selected elements in the database
      */
      $.ajax({
        url: 'db/removeword.php',
        type: 'POST',
        data: {wordIDs: JSON.stringify(ids)},
        success: function() {
          $("input[class=txt-checkbox]:checked").each(function() {
            $(this).closest('tr').remove();
          });
          // if there are no remaining words to show on the table, remove the entire table
          removeTableIfEmpty();
        },
        error: function (request, status, error) {
          alert("There was an error when trying to delete the selected words. Refresh the page and try again.");
        }
      });
    }
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
    if ($('#wordstable tbody').is(':empty')) {
      if ($('#search').val() == '') {
        $('#wordstable').replaceWith('<p>There are no words in your private library.</p>');  
      } else {
        $('#wordstable').replaceWith('<p>There are no words in your private library that meet that search criteria.</p>');
      }
      $('#actions-menu').remove();
    }
  }

});
