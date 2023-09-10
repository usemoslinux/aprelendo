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
     * Submits email to create new password and reset old one
     * Triggers when user submits #form_forgot_password form
     * @param e {Event}
     */
    $(document).on("submit", "#form_forgot_password", function(e) {
        e.preventDefault();
        const form_data = $("#form_forgot_password").serialize();
        showMessage(
            "Your request is being processed. Please wait...",
            "alert-info"
        );

        $.ajax({
            type: "POST",
            url: "ajax/forgotpassword.php",
            data: form_data
        })
            .done(function(data) {
                if (data.error_msg == null) {
                    showMessage(
                        "An email was sent. Access the link and create a new password.",
                        "alert-success"
                    );
                } else {
                    showMessage(data.error_msg, "alert-danger");
                }
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                showMessage(
                    "Oops! There was an unexpected error when trying to register you. Please try again later.",
                    "alert-danger"
                );
            });
    }); // end #form_forgot_password.on.submit

    /**
     * Checks & saves new user password in db
     * Triggers when user submits the #form_create_new_password form
     * @param e {Event}
     */
    $(document).on("submit", "#form_create_new_password", function(e) {
        e.preventDefault();
        const form_data = $("#form_create_new_password").serialize();
        showMessage(
            "Your request is being processed. Please wait...",
            "alert-info"
        );

        if ($("#newpassword").val() === $("#newpassword-confirmation").val()) {
            // 1. passwords entered by user are identical
            $.ajax({
                type: "post",
                url: "ajax/forgotpassword.php",
                data: form_data
            })
                .done(function(data) {
                    if (data.error_msg == null) {
                        showMessage(
                            "Your new password was successfully saved!<br>You will soon be redirected to the login page",
                            "alert-success"
                        );
                        setTimeout(function() {
                            window.location.replace(
                                "https://www.aprelendo.com/login.php"
                            );
                        }, 5000);
                    } else {
                        showMessage(data.error_msg, "alert-danger");
                    }
                })
                .fail(function(xhr, ajaxOptions, thrownError) {
                    showMessage(
                        "Oops! There was an unexpected error when trying to save your new password. Please try again later.",
                        "alert-danger"
                    );
                });
        } else {
            // 2. passwords entered by user are not identical
            showMessage(
                "The passwords you entered are not identical. Please try again.",
                "alert-danger"
            );
            $("#newpassword").val("");
            $("#newpassword-confirmation").val("");
        }
    }); // end #form_create_new_password.on.submit
});
