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

    $("#search").trigger("focus");
    $("input:checkbox").prop("checked", false);

    $('form').on( "submit", function(e) {
        e.preventDefault();

        const params = {    s: $("#s").val().trim(),                // search text
                            o: $('.o.active').data('value') || 0    // order
                        };

        const uri_str = buildQueryString(params);
        window.location.replace("words" + uri_str);
    });

    // ajax call to get dictionary URI
    (async () => {
        try {
            const response = await fetch("/ajax/getdicuris.php");
            
            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to fetch dictionary URIs');
            }
            
            dictionary_URI = data.payload.dictionary_uri;
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
    })();

    // action menu implementation

    /**
     * Deletes selected words from the database
     * Trigger: when user selects "Delete" in the action menu
     */
    $("#mDelete").on("click", async function () {
        if (confirm("Really delete?")) {
            let ids = [];
            $("input.chkbox-selrow:checked").each(function () {
                ids.push($(this).attr("data-idWord"));
            });

            if (ids.length === 0) {
                return;
            }

            try {
                const form_data = new URLSearchParams();
                form_data.append('wordIDs', JSON.stringify(ids));

                const response = await fetch("/ajax/removeword.php", {
                    method: "POST",
                    body: form_data
                });

                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error_msg || 'Failed to delete words');
                }

                window.location.replace(
                    "words" + buildQueryString(getCurrentURIParameters())
                );
            } catch (error) {
                console.error(error);
                alert(`Oops! ${error.message}`);
            }
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
