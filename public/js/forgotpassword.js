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
    $(document).on('submit', '#form_forgot_password', function (e) {
        e.preventDefault();
        var form_data = $('#form_forgot_password').serialize();
        showMsg('Your request is being processed. Please wait...', 'alert-info');

        $.ajax({
                type: "post",
                url: "db/forgotpassword.php",
                data: form_data
            })
            .done(function (data) {
                if (data.error_msg == null) {
                    showMsg('An email was sent. Access the link and create a new password.', 'alert-success');
                } else {
                    showMsg(data.error_msg, 'alert-danger');
                }
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                showMsg('Oops! There was an unexpected error when trying to register you. Please try again later.', 'alert-danger');
            });
    });

    $(document).on('submit', '#form_create_new_password', function (e) {
        e.preventDefault();
        var form_data = $('#form_create_new_password').serialize();
        showMsg('Your request is being processed. Please wait...', 'alert-info');

        // 1. passwords entered by user are identical
        if ($('#pass1').val() === $('#pass2').val()) {
            $.ajax({
                type: "post",
                url: "db/forgotpassword.php",
                data: form_data
            })
            .done(function (data) {
                if (data.error_msg == null) {
                    showMsg('Your new password was successfully saved!<br/>You will soon be redirected to the login page', 'alert-success');
                    setTimeout(function() {window.location.replace("https://www.aprelendo.com/login.php")}, 5000);
                } else {
                    showMsg(data.error_msg, 'alert-danger');
                }
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                showMsg('Oops! There was an unexpected error when trying to save your new password. Please try again later.', 'alert-danger');
            });    
        } else { // 2. passwords entered by user are not identical 
            showMsg('The passwords you entered are not identical. Please try again.', 'alert-danger');
            $('#pass1').val('');
            $('#pass2').val('');
        }
        
    });

    /**
     * Shows custom error message in the top section of the screen
     * @param {string} msg 
     */
    function showMsg(msg, msg_type) {
        $('#alert_msg').html(msg)
            .removeClass()
            .addClass('alert ' + msg_type);
        $(window).scrollTop(0);
    } // end of showError
});