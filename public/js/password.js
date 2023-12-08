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
     * Checks password confirmation matches original password
     */
    function checkPasswordsAreEqual() {
        const $password = $('#newpassword')
        const $password_confirmation = $('#newpassword-confirmation')
        const $text = $('#passwords-match-text')

        if ($password.val() === '' || $password_confirmation.val() === '') {
            $password_confirmation.css('border-bottom', '1px solid #ced4da')
            $text.text('')
        } else if ($password_confirmation.val() !== $password.val()) {
            $password_confirmation.css('border-bottom', '2px solid red')
            $text.text('Passwords dont match')
        } else {
            $password_confirmation.css('border-bottom', '2px solid green')
            $text.text('Passwords match')
        }
    } // end checkPasswordsAreEqual

    /**
     * Checks password strength and changes progress bar accordingly
     */
    $('#newpassword').on('input', function() {
        const number = /([0-9])/
        const letters = /([a-zA-Z])/
        const special_chars = /([~`!@#$%^&*()\-_+={};:[\]?./,])/

        const $password = $(this)
        const $password_confirmation = $('#newpassword-confirmation')
        const $text = $('#password-strength-text')

        if ($password.val().length < 8) {
            $password.css('border-bottom', '2px solid red')
            $text.text('Weak (should be at least 8 characters long)')
        } else if (
            $password.val().match(number) &&
            $password.val().match(letters) &&
            $password.val().match(special_chars)
        ) {
            $password.css('border-bottom', '2px solid green')
            $text.text('Strong')
        } else {
            $password.css('border-bottom', '2px solid yellow')
            $text.text(
                'Medium (should include letters, numbers and special characters)'
            )
        }

        if ($password_confirmation.val() !== '') {
            checkPasswordsAreEqual()
        }
    }) // end #newpassword.on.input

    /**
     * Triggered when user is writing password confirmation
     */
    $('#newpassword-confirmation').on('input', function() {
        checkPasswordsAreEqual()
    }) // end #newpassword-confirmation.on.input

    /**
     * Shows/hides password
     */
    $('.show-hide-password-btn').on('click', function(e) {
        e.preventDefault()
        let $password_input = $(this)
            .parent()
            .find('input')
        let $password_span = $(this).find('span')

        if ($password_input.attr('type') === 'text') {
            $password_input.attr('type', 'password')
            $password_span.addClass('bi-eye-slash-fill').removeClass('bi-eye-fill')
        } else if ($password_input.attr('type') === 'password') {
            $password_input.attr('type', 'text')
            $password_span.removeClass('bi-eye-slash-fill').addClass('bi-eye-fill')
        }
    }) // end .show-hide-password-btn.on.click
})