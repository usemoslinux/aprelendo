/**
 * Copyright (C) 2018 Pablo Castagnino
 * 
 * This file is part of aprelendo.
 * 
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function () {
    $('#search').focus();
    $('input:checkbox').prop('checked', false);
    
    /**
    * Deletes selected texts from the database
    * Trigger: when user selects "Delete" in the action menu
    */
    $("#mDelete").on("click", function () {
        if (confirm("Really delete?")) {
            var ids = [];
            $("input[class=chkbox-selrow]:checked").each(function () {
                ids.push($(this).attr('data-idText'));
            });
            
            /**
            * Deletes selected texts from the database (based on their ID).
            * When done, also removes selected rows from HTML table.
            * @param  {integer array} textIDs Ids of the selected elements in the database
            */
            $.ajax({
                url: 'ajax/removetext.php',
                type: 'POST',
                data: {
                    textIDs: JSON.stringify(ids)
                }
            })
            .done(function () {
                var params = 'f=' + $('#f').val() + '&s=' + $('#s').val() + '&sa=' + $('#sa').val() + '&o=' + $('#o').val();
                window.location.replace('texts.php?' + params);
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
        });
        
        /**
        * Moves selected texts from the "texts" table to the "archivedtexts" table in the database (archive);
        * or, vice-versa, moves texts from the "archivedtexts" table to the "texts" table (unarchive)
        * This is done based on text IDs.
        * @param {integer array} ids Ids of the selected elements in the database
        * @param {boolean} archivetxt If true, archive text; else, unarchive text
        */
        $.ajax({
            url: 'ajax/archivetext.php',
            type: 'POST',
            data: {
                textIDs: JSON.stringify(ids),
                archivetext: archivetxt
            }
        })
        .done(function () {
            var params = 'f=' + $('#f').val() + '&s=' + $('#s').val() + '&sa=' + $('#sa').val() + '&o=' + $('#o').val();
            // var params = window.location.search.substr(1).split("?");

            window.location.replace('texts.php?' + params);
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
        var $chkboxes = $('.chkbox-selrow');
        $chkboxes.prop('checked', $(this).prop('checked'));

        if ($(this).is(":checked")) {
            $chkboxes.closest('tr').addClass("info"); 
        } else {
            $chkboxes.closest('tr').removeClass("info");
        }

        toggleActionMenu();
    });
    
    /**
    * Shows selection in Filter menu
    */
    $('#btn-filter + div > a').on('click', function() {
        var $item = $(this);
        if ($item.is(':last-child') && $item.text().trim() == 'Archived') {
            $item.toggleClass('active');    
        } else {
            $item.addClass('active');
            
            $item.siblings('.active').each(function(index, element) {
                if ($(this).text().trim() !== 'Archived') {
                    $(this).toggleClass('active');
                }
            });
        }    
    });

    /**
     * Selects sorting
     */
    $('#dropdown-menu-sort').on('click', function(e) {
        var url = window.location.pathname;

        var filename = '';
        if (url.indexOf('&') > -1) {
            filename = url.substring(url.lastIndexOf('/')+1, url.indexOf('&'));    
        } else {
            filename = url.substring(url.lastIndexOf('/')+1);    
        }
        
        var params = 'f=' + $('#f').val() + '&s=' + $('#s').val() + '&sa=' + $('#sa').val() + '&o=' + $('#o').val();
        window.location.replace(filename + '?' + params);        
    });

    /**
     * Allows selecting text by clicking on a row, instead of the checkbox.
     * Makes it easier for mobile device users to select texts.
     */
    $('tr').on('click', function(e) {
        if (e.target.type !== 'checkbox' && e.target.tagName !== 'A') {
            $(':checkbox', this).trigger('click');
        }
    });

    /**
     * Adds/removes a blue background to selected/unselected rows
     */
    $('.chkbox-selrow').on('click', function() {
        if ($(this).is(":checked")) {
            $(this).closest('tr').addClass("info"); 
        } else {
            $(this).closest('tr').removeClass("info");
        }
    });

    /**
     * Turn off clicking for disabled navigation items
     */
    $(document).on('click', 'li.disabled', function () {
        return false;
    });
    
});