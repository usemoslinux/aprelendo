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

$(document).ready(function() {
    /**
     * Saves user profile information to the database and shows success/failure message
     * It is executed when user clicks the submit button
     * @param  {event object} e Used to prevent reloading of the page
     */
    $("#userprofile-form").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        $.ajax({
            url: "ajax/saveuserprofile.php",
            type: "POST",
            data: $("#userprofile-form").serialize()
        })
            .done(function(data) {
                if (data.error_msg) {
                    showMessage(data.error_msg, "alert-danger");
                } else {
                    showMessage("Your user profile information was successfully saved."
                        + " You will soon be redirected to the main page.", "alert-success");

                    setTimeout(() => { window.location.replace("/texts"); }, 2000);
                }
            })
            .fail(function() {
                showMessage("<strong>Oops!</strong> Something went wrong when trying to save your user profile information.", "alert-danger");
            })
            .always(function() {
                $("#password, #newpassword, #newpassword-confirmation").val("");
            });
    }); // end #userprofile-form.submit

    /**
     * Shows delete account confirmation dialog
     */
    $("#btn-delete-account").on("click", function() {
        $(document)
            .find("#delete-account-modal")
            .modal("show");
    }); // end #btn-delete-account.on.click

    /**
     * Deletes account
     */
    $("#btn-confirm-delete-account").on("click", function() {
        $.ajax({
            url: "ajax/deleteaccount.php",
            type: "POST",
            data: $("#userprofile-form").serialize()
        })
            .done(function(data) {
                if (data.error_msg) {
                    showMessage(data.error_msg, "alert-danger");
                } else {
                    window.location.replace("/index");
                }
            })
            .fail(function() {
                showMessage("<strong>Oops!</strong> Something went wrong when trying to delete your user account.", "alert-danger");
            });
    }); // #btn-confirm-delete-account.on.click
});
