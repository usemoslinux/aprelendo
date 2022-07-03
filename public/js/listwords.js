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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function() {
    var dictionary_URI = "";
    var $dic_frame = $("#dicFrame");
    var $sel_word = $();

    $("#search").focus();
    $("input:checkbox").prop("checked", false);
    
    // ajax call to get dictionary URI
    $.ajax({
        url: "/ajax/getdicuris.php",
        type: "GET",
        dataType: "json"
    }).done(function(data) {
        if (data.error_msg == null) {
            dictionary_URI = data.dictionary_uri;
        }
    }); // end $.ajax 

    // action menu implementation

    /**
     * Deletes selected words from the database
     * Trigger: when user selects "Delete" in the action menu
     */
    $("#mDelete").on("click", function() {
        if (confirm("Really delete?")) {
            var ids = [];
            $("input.chkbox-selrow:checked").each(function() {
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
                .done(function() {
                    window.location.replace(
                        "words.php" + parameterizeArray(getCurrentURIParameters())
                    );
                })
                .fail(function(request, status, error) {
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

    /**
     * Returns current URI parameters
     */
    function getCurrentURIParameters() {
        var parts = window.location.search.substr(1).split("&");
        var result = { p: "1" };

        if (parts != "") {
            for (var i = 0; i < parts.length; i++) {
                var temp = parts[i].split("=");
                result[decodeURIComponent(temp[0])] = decodeURIComponent(
                    temp[1]
                );
            }
        }
        
        return result; // remove trailing '&'
    } // end getCurrentURIParameters

    /**
     * Converts array to URI string with parameters
     * @param {array} arr 
     * @returns string
     */
    function parameterizeArray(arr) {
        let result = '?';
                        
        for (const key in arr) {
            if (arr[key]) {
                result += key + '=' + encodeURIComponent(arr[key]) + '&';
            }
        }

        return result.slice(0,-1);
    } // end parameterizeArray

    $(document).on("change", ".chkbox-selrow", toggleActionMenu);

    /**
     * Selects/Unselects all texts from the list
     */
    $(document).on("click", "#chkbox-selall", function(e) {
        e.stopPropagation();

        var $chkboxes = $(".chkbox-selrow");
        $chkboxes.prop("checked", $(this).prop("checked"));

        toggleActionMenu();
    }); // end #chkbox-selall.on.click

    /**
     * Selects sorting
     */
    $("#dropdown-menu-sort").on("click", function(e) {
        var params = { s: $("#s").val(), 
                       o: $("#o").val() };

        var uri_str = parameterizeArray(params);
        
        window.location.replace("words.php" + uri_str);
    }); // end #dropdown-menu-sort.on.click


    /**
     * Open dictionary modal
     * Triggers when user clicks word
     * @param {event object} e
     */
    $(".word").on("click", function(e) {
        $sel_word = $(this);
        var url = dictionary_URI.replace("%s", encodeURI($sel_word.text()));

        // set up buttons
        $("#btnadd").text("Forgot");
        $("#btn-translate").hide();
        $("#btnremove").removeClass().addClass("btn btn-danger mr-auto");
        
        // show loading spinner
        $("#iframe-loader").attr('class','lds-ellipsis m-auto');
        $dic_frame.attr('class','d-none');

        $dic_frame.get(0).contentWindow.location.replace(url);
        // the previous line loads iframe content without adding it to browser history,
        // as this one does: $dic_frame.attr('src', url);

        $("#myModal").modal("show");
    }); // end #.word.on.click

    /**
     * Adds anchor '#dictionary' to URL, to allow closing modal with back button
     */
     $('#myModal').on('show.bs.modal', function(e) {
        window.location.hash = "dictionary";
    }); // end of #myModal.on.show.bs.modal

    /**
     * Workaround to allow closing modal with back button 
     */
    $(window).on('hashchange', function (event) {
        if(window.location.hash != "#dictionary") {
            $('#myModal').modal('hide');
        }
    }); // end of window.on.hashchange

    /**
     * Hides loader spinner when dictionary iframe finished loading
     */
     $dic_frame.on("load", function() {
        $("#iframe-loader").attr('class','d-none');
        $dic_frame.removeClass();
    }); // end $dic_frame.on.load()

    /**
     * Updates status of forgotten words
     * Triggers when user click in Forgot button in dictionary modal
     */
    $("#btnadd").on("click", function(e) {
        // add selection to "words" table
        $.ajax({
            type: "POST",
            url: "/ajax/addword.php",
            data: {
                word: $sel_word.text()
            }
        }).done(function(data) {
            if (data.error_msg != null) {
                alert(data.error_msg);
            } else {
                var $hourglass = $sel_word.parent().next().children(":first");
                $hourglass.removeClass().addClass("fas fa-hourglass-start status_forgotten");
                $hourglass.attr("title", "Forgotten");
            }
        }).fail(function() {
            alert(
                "Oops! There was an error updating this words' status."
            );
        });
    }); // end #btnadd.on.click

    /**
     * Deletes current word
     * Triggers when user selects Delete button in dictionary modal
     * @param {event object} e
     */
    $("#btnremove").on("click", function(e) {
        var sel_word_id = $sel_word.parent().prev().children(":first").children(":first").data("idword");

        $.ajax({
            url: "ajax/removeword.php",
            type: "POST",
            data: {
                word: $sel_word.text() //JSON.stringify(ids)
            }
        })
            .done(function() {
                window.location.replace(
                    "words.php" + parameterizeArray(getCurrentURIParameters())
                );
            })
            .fail(function(request, status, error) {
                alert(
                    "There was an error when trying to delete the selected words. Refresh the page and try again."
                );
            });
    }); // end #btnremove.on.click
});
