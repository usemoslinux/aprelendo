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
    /**
     * Edits language record
     * This is triggered when user presses the "Save" button & submits the form
     */
    $("#form-editlanguage").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            url: "ajax/editlanguage.php",
            data: $(this).serialize(),
        })
        .done(function (data) {
            if (typeof data != "undefined" && data !== "") {
                showMessage(data.error_msg, "alert-danger");
            } else {
                showMessage("Your language information was successfully saved."
                    + " You will soon be redirected to the main page.", "alert-success");
                setTimeout(() => { window.location.replace("/texts"); }, 2000);
            }
        })
        .fail(function (xhr, ajaxOptions, thrownError) {
            showMessage(
                "Oops! There was an unexpected error trying to edit your language preferences.",
                "alert-danger"
            );
        });
    });

    /**
     * Does some checks before submiting the form's data
     * Triggers when user clicks the Save button
     */
    $('#savebtn').on('click', function(e) {
        const dict_uri = $('#dict-uri').val();
        const translator_uri = $('#translator-uri').val();
        let error = false;

        // show an error message if...
        
        if (dict_uri.length == 0) {
            // 1. user forgot to include the dictionary URL
            
            showMessage("You need to specify the URL of the dictionary you want to use.", "alert-danger");
            error = true;
        } else if (dict_uri.indexOf('%s') == -1) {
            // 2. user forgot to include '%s' in the dictionary URL

            showMessage("The dictionary URL needs to include the position of the lookup word or phrase. For this, use '%s' (without quotation marks).", "alert-danger");
            error = true;
        } else if (translator_uri.length == 0) {
            // 3. user forgot to include the translator URL

            showMessage("You need to specify the URL of the translator you want to use.", "alert-danger");
            error = true;
        } else if (translator_uri.indexOf('%s') == -1) {
            // 4. user forgot to include '%s' in the translator URL

            showMessage("The translator URL needs to include the position of the lookup word or phrase. For this, use '%s' (without quotation marks).", "alert-danger");
            error = true;
        }

        if (error) {
            e.preventDefault();
            e.stopPropagation();
        }
    }); // end #savebtn.on.click
});
