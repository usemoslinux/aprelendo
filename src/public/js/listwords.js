/**
 * Copyright (C) 2019 Pablo Castagnino
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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

$(document).ready(function () {
    let dictionary_URI = "";

    $("#search").focus();
    $("input:checkbox").prop("checked", false);

    $('form').submit(function(e) {
        e.preventDefault();

        const params = {    s: $("#s").val().trim(),                // search text
                            o: $('.o.active').data('value') || 0    // order
                        };

        const uri_str = buildQueryString(params);
        window.location.replace("words" + uri_str);
    });

    // ajax call to get dictionary URI
    $.ajax({
        url: "/ajax/getdicuris.php",
        type: "GET",
        dataType: "json"
    }).done(function (data) {
        if (data.error_msg == null) {
            dictionary_URI = data.dictionary_uri;
        }
    }); // end $.ajax 

    // action menu implementation

    /**
     * Deletes selected words from the database
     * Trigger: when user selects "Delete" in the action menu
     */
    $("#mDelete").on("click", function () {
        if (confirm("Really delete?")) {
            let ids = [];
            $("input.chkbox-selrow:checked").each(function () {
                ids.push($(this).attr("data-idWord"));
            });

            /**
             * Deletes selected words from the database (based on their ID).
             * When done, also removes selected rows from HTML table.
             * @param  {integer array} textIDs Ids of the selected elements in the database
             */
            $.ajax({
                url: "ajax/removeword.php",
                type: "POST",
                data: {
                    wordIDs: JSON.stringify(ids)
                }
            })
                .done(function () {
                    window.location.replace(
                        "words" + buildQueryString(getCurrentURIParameters())
                    );
                })
                .fail(function (request, status, error) {
                    alert(
                        "There was an error when trying to delete the selected words. Refresh the page and try again."
                    );
                });
        }
    }); // end #mDelete.on.click

    /**
     * Enables/Disables action menu based on the number of selected elements.
     * If there is at least 1 element selected, it enables it. Otherwise, it is disabled.
     */
    function toggleActionMenu() {
        if ($("input[type=checkbox]:checked").length === 0) {
            $("#actions-menu").addClass("disabled");
        } else {
            $("#actions-menu").removeClass("disabled");
        }
    } // end toggleActionMenu

    $(document).on("change", ".chkbox-selrow", toggleActionMenu);

    /**
     * Selects/Unselects all words from the list
     */
    $(document).on("click", "#chkbox-selall", function (e) {
        e.stopPropagation();

        const $chkboxes = $(".chkbox-selrow");
        $chkboxes.prop("checked", $(this).prop("checked"));

        toggleActionMenu();
    }); // end #chkbox-selall.on.click

    /**
     * Selects sorting
     */
    $("#dropdown-menu-sort .o").on("click", function (e) {
        const filename = getCurrentFileName();
        const params = {    s: $("#s").val().trim(),   // search text
                            o: $(this).data('value') || 0    // order
                        };

        const uri_str = buildQueryString(params);
        window.location.replace(filename + uri_str);
    }); // end #dropdown-menu-sort .o.on.click

    /**
     * Open dictionary modal
     * Triggers when user clicks word
     * @param {event object} e
     */
    $(".word").on("click", function (e) {
        const $selword = $(this);
        const dic_link = LinkBuilder.forWordInDictionary(dictionary_URI, $selword.text());
        openInNewTab(dic_link);
    }); // end #.word.on.click
});
