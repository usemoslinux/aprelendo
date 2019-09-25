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

    /**
     * Sends user login form
     */
    $('#form_login').on('submit', function (e) {
        e.preventDefault();

        var form_data = $('#form_login').serialize();

        $.ajax({
                type: "POST",
                url: "ajax/login.php",
                data: form_data
            })
            .done(function (data) {
                if (data.error_msg == null) {
                    window.location.replace('texts.php');
                } else {
                    showMessage(data.error_msg, 'alert-danger');
                }
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                showMessage('Oops! There was an unexpected error when trying to log you in. Please try again later.', 'alert-danger');
            }); // end of ajax
    }); // end of #form_login.on.submit
});

/**
 * Google Sign-in
 */
function googleSignIn(googleUser) {
    var profile = googleUser.getBasicProfile();
    // console.log('ID: ' + profile.getId());
    // console.log('Name: ' + profile.getName());
    // console.log('Image URL: ' + profile.getImageUrl());
    // console.log('Email: ' + profile.getEmail());
    
    //pass information to server to insert or update the user record
    $.ajax({
        type: "POST",
        data: profile,
        url: 'ajax/google_oauth.php'
    })
    .done(function (data) {
        if (data.error_msg == undefined) {
            window.location.replace('texts.php');
        } else {
            showMessage(data.error_msg, 'alert-danger');
        }
    })
    .fail(function (xhr, ajaxOptions, thrownError) {
        showMessage('Oops! There was an unexpected error when trying to register you. Please try again later.', 'alert-danger');
    }); // end of ajax
}
 
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