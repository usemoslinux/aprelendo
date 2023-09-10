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
     * Sends email
     * This is triggered when user presses the "Send" button & submits the form
     */
    $("#form-support").on("submit", function(e) {
        e.preventDefault();

        showMessage("Sending message to our support team...", "alert-info");

        const form_data = $(this).serialize();

        $.ajax({
            type: "POST",
            url: "ajax/contact.php",
            data: form_data
        })
            .done(function(data) {
                if (data.error_msg == null) {
                    showMessage(
                        "Your message was successfully sent. You shall receive an answer briefly.",
                        "alert-success"
                    );

                    setTimeout(resetControls, 3000);
                } else {
                    showMessage(data.error_msg, "alert-danger");
                }
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                showMessage(
                    "Oops! There was an unexpected error trying to send your message. Please try again later.",
                    "alert-danger"
                );
            }); // end of ajax
    }); // end #form-support.on.submit

    /**
     * Empties form input fields
     */
    function resetControls() {
        $("#alert-msg").addClass("d-none");
        $("#name").val("");
        $("#email").val("");
        $("#message").val("");
    } // end resetControls
});
