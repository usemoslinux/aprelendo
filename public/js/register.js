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
    $('#form_register').on('submit', function (e) {
        e.preventDefault();

        var form_data = $('#form_register').serialize();

        showMessage('Processing...', 'alert-info');

        $.ajax({
                type: "POST",
                url: "ajax/register.php",
                data: form_data
            })
            .done(function (data) {
                if (data.error_msg == null) {
                    // window.location.replace('texts.php');
                    showMessage('An email has been sent to your account with the activation link.', 'alert-success');
                } else {
                    showMessage(data.error_msg, 'alert-danger');
                }
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                showMessage('Oops! There was an unexpected error when trying to register you. Please try again later.', 'alert-danger');
            }); // end of ajax
    }); // end of #form_register.on.submit


    /**
     * Shows custom message in the top section of the screen
     * @param {string} html
     * @param {string} type 
     */
    function showMessage(html, type) {
        $('#error-msg').html(html)
            .removeClass()
            .addClass('alert ' + type);
        $(window).scrollTop(0);
    } // end of showMessage

    /**
     * Checks password confirmation matches original password
     */
    function checkPasswordAreEqual() { 
        var $password = $('#password');
        var $password_confirmation = $('#password-confirmation');
        var $text = $('#passwords-match-text');

        if ($password.val() == '' || $password_confirmation.val() == '') {
            $password_confirmation.css('border-bottom', '1px solid #ced4da');
            $text.text('');
        } else if ($password_confirmation.val() != $password.val()) {
            $password_confirmation.css('border-bottom', '2px solid red');
            $text.text('Passwords dont match');
        } else {
            $password_confirmation.css('border-bottom', '2px solid green');
            $text.text('Passwords match');
        }
    }

    /**
     * Checks password strength and changes progress bar accordingly
     */
    $('#password').on('input', function () {
        var number = /([0-9])/;
        var letters = /([a-zA-Z])/;
        var special_chars = /([~`!@#$%^&*()\-_+={};:\[\]\?\.\/,])/;

        var $password = $(this);
        var $password_confirmation = $('#password-confirmation');
        var $text = $('#password-strength-text');

        if ($password.val().length < 8) {
            $password.css('border-bottom', '2px solid red');
            $text.text('Weak (should be at least 8 characters long)');
        } else if ($password.val().match(number) && $password.val().match(letters) && $password.val().match(special_chars)) {
            $password.css('border-bottom', '2px solid green');
            $text.text('Strong');
        } else {
            $password.css('border-bottom', '2px solid yellow');
            $text.text('Medium (should include letters, numbers and special characters)');
        }

        if ($password_confirmation.val() != '') {
            checkPasswordAreEqual();    
        }
    });
    
    /**
     * Triggered when user is writing password confirmation
     */
    $('#password-confirmation').on('input', function () {
        checkPasswordAreEqual();
    });

    /**
     * Shows/hides password
     */
    $(".show-hide-password-btn").on('click', function (e) {
        e.preventDefault();
        var $password_input = $(this).parent().siblings('input');
        var $password_i = $(this).find('i');

        if ($password_input.attr("type") == "text") {
            $password_input.attr('type', 'password');
            $password_i.addClass("fa-eye-slash")
                .removeClass("fa-eye");
        } else if ($password_input.attr("type") == "password") {
            $password_input.attr('type', 'text');
            $password_i.removeClass("fa-eye-slash")
                .addClass("fa-eye");
        }
    });

});