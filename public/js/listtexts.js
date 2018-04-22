$(document).ready(function() {
    $('#search').focus();

    // action menu implementation

    // action: delete (deletes selected texts from db)
    $("#mDelete").on("click", function() {
        if (confirm("Really delete?")) {
            $("input[type=checkbox]:checked").each(function() {
                var id = $(this).attr('data-idText');
                var parentTR = $(this).closest('tr');

                deleteText(id, parentTR);
            });

        }
    });

    function deleteText(id, parentTR) {
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
    }

    $('#mArchive').on('click', function() {
        var archivetext = $(this).text() === 'Archive text';
        $('input[type=checkbox]:checked').each(function() {
            var id = $(this).attr('data-idText');
            var parentTR = $(this).closest('tr');

            ArchiveText(id, parentTR, archivetext);
        });
    });

    function ArchiveText(id, parentTR, archivetxt) {
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
    }

    function toggleActionMenu() {
      if ($('input[type=checkbox]:checked').length === 0) {
          $('#actions-menu').addClass('disabled');
      } else {
          $('#actions-menu').removeClass('disabled');
      }
    }

    $(document).on('change', '.txt-checkbox', toggleActionMenu);

    $(document).on('click', '.alltxt-checkbox', function() {
      var chkboxes = $('.txt-checkbox');
      chkboxes.prop('checked', $(this).prop('checked'));
      toggleActionMenu();
    });

});
