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

/**
 * Google Log in
 * Uses the new Google Identity Services library for authentication
 */
function googleLogIn(googleUser) {
    const profile = decodeJwtResponse(googleUser.credential);

    //pass information to server to insert or update the user record
    $.ajax({
        type: "POST",
        data: {
            "id": profile.sub,
            "name": profile.name,
            "email": profile.email,
            "time-zone": Intl.DateTimeFormat().resolvedOptions().timeZone
        },
        url: "ajax/google_oauth.php"
    })
        .done(function(data) {
            if (data.error_msg == undefined) {
                window.location.replace("/texts");
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
} // end googleLogIn

/**
 * Decodes Google credentials JWT token
 * @param {*} token 
 * @returns 
 */
function decodeJwtResponse(token) {
    let base64Url = token.split('.')[1]
    let base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    let jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
    return JSON.parse(jsonPayload)
} // end decodeJwtResponse