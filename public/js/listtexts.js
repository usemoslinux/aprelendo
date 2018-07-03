$(document).ready(function () {

  $('#search').focus();
  $('input:checkbox').prop('checked', false);

  // action menu implementation

  /**
   * Deletes selected texts from the database
   * Trigger: when user selects "Delete" in the action menu
   */
  $("#mDelete").on("click", function () {
    if (confirm("Really delete?")) {
      var ids = [];
      $("input[class=chkbox-selrow]:checked").each(function () {
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
          data: {
            textIDs: JSON.stringify(ids)
          }
        })
        .done(function () {
          var url = window.location.pathname.indexOf('archivedtexts.php') >= 0 ? 'archivedtexts.php?page=' : 'texts.php?page=';
          window.location.replace(url + getCurrentPage().page);
        })
        .fail(function () {
          alert("There was an error when trying to delete the selected texts. Refresh the page and try again.");
        });
    }
  });

  /**
   * Archives selected texts
   * Trigger: when user selects "Archive" in the action menu
   */
  $('#mArchive').on('click', function () {
    var archivetxt = $(this).text() === 'Archive';
    var ids = [];
    $('input[class=chkbox-selrow]:checked').each(function () {
      ids.push($(this).attr('data-idText'));
      //parentTRs.push($(this).closest('tr'));
    });

    /**
     * Moves selected texts from the "texts" table to the "archivedtexts" table in the database (archive);
     * or, vice-versa, moves texts from the "archivedtexts" table to the "texts" table (unarchive)
     * This is done based on text IDs.
     * @param {integer array} ids Ids of the selected elements in the database
     * @param {boolean} archivetxt If true, archive text; else, unarchive text
     */
    $.ajax({
        url: 'db/archivetext.php',
        type: 'POST',
        data: {
          textIDs: JSON.stringify(ids),
          archivetext: archivetxt
        }
      })
      .done(function () {
        var url = archivetxt ? 'texts.php?page=' : 'archivedtexts.php?page=';
        window.location.replace(url + getCurrentPage().page);
      })
      .fail(function () {
        alert("There was an error when trying to archive the selected texts. Refresh the page and try again.");
      }); // end ajax
  }); // end mArchive.on.click

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

  $(document).on('change', '.chkbox-selrow', toggleActionMenu);

  /**
   * Selects/Unselects all texts from the list
   */
  $(document).on('click', '#chkbox-selall', function () {
    var chkboxes = $('.chkbox-selrow');
    chkboxes.prop('checked', $(this).prop('checked'));
    toggleActionMenu();
  });

  /**
   * Returns current page
   */
  function getCurrentPage() {
    var parts = window.location.search.substr(1).split("&");
    if (parts == '') {
      result = {
        page: 1
      };
    } else {
      var result = {};
      for (var i = 0; i < parts.length; i++) {
        var temp = parts[i].split("=");
        result[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
      }
    }
    return result;
  } // end getCurrentPage

});