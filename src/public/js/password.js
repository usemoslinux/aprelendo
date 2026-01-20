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
    const $password = $('#newpassword');
    const $password_confirmation = $('#newpassword-confirmation');
    const $password_match_text = $('#passwords-match-text');
    const $password_strength_text = $('#password-strength-text');

    const number = /(\d)/;
    const letters = /([a-zA-Z])/;
    const special_chars = /([~`!@#$%^&*()\-_+={};:[\]?./,])/;

    /**
     * Checks password confirmation matches original password
     */
    function checkPasswordsAreEqual(password_value, confirmation_value) {
        if (password_value === '' || confirmation_value === '') {
            $password_confirmation.css('border-bottom', '1px solid #ced4da');
            $password_match_text.text('');
        } else if (confirmation_value !== password_value) {
            $password_confirmation.css('border-bottom', '2px solid red');
            $password_match_text.text("Passwords don't match");
        } else {
            $password_confirmation.css('border-bottom', '2px solid green');
            $password_match_text.text('Passwords match');
        }
    } // end checkPasswordsAreEqual

    /**
     * Updates password strength state
     */
    function setPasswordStrengthState(border_style, message) {
        $password.css('border-bottom', border_style);
        $password_strength_text.text(message);
    } // end setPasswordStrengthState

    /**
     * Checks password strength and changes progress bar accordingly
     */
    $password.on('input', function() {
        const password_value = $password.val();
        const confirmation_value = $password_confirmation.val();

        if (password_value.length === 0) {
            setPasswordStrengthState('', '');
            return;
        }

        if (password_value.length < 8) {
            setPasswordStrengthState(
                '2px solid red',
                'Weak - should be at least 8 characters long'
            );
        } else {
            const has_number = number.test(password_value);
            const has_letters = letters.test(password_value);
            const has_special_chars = special_chars.test(password_value);

            if (has_number && has_letters && has_special_chars) {
                setPasswordStrengthState('2px solid green', 'Strong');
            } else {
                setPasswordStrengthState(
                    '2px solid yellow',
                    'Medium - should include letters, numbers, and special characters'
                );
            }
        }

        if (confirmation_value !== '') {
            checkPasswordsAreEqual(password_value, confirmation_value);
        }
    }); // end #newpassword.on.input

    /**
     * Triggered when user is writing password confirmation
     */
    $password_confirmation.on('input', function() {
        const password_value = $password.val();
        const confirmation_value = $password_confirmation.val();

        checkPasswordsAreEqual(password_value, confirmation_value);
    }); // end #newpassword-confirmation.on.input

    /**
     * Shows/hides password
     */
    $('.show-hide-password-btn').on('click', function(e) {
        e.preventDefault();
        const $button = $(this);
        const $password_input = $button.parent().find('input');
        const $password_span = $button.find('span');
        const input_type = $password_input.attr('type');

        if (input_type === 'text') {
            $password_input.attr('type', 'password');
            $password_span.addClass('bi-eye-slash-fill').removeClass('bi-eye-fill');
        } else if (input_type === 'password') {
            $password_input.attr('type', 'text');
            $password_span.removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
        }
    }); // end .show-hide-password-btn.on.click
});
