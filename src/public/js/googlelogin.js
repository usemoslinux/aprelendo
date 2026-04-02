// SPDX-License-Identifier: GPL-3.0-or-later

/**
 * Google Log in
 * Uses the new Google Identity Services library for authentication
 */
async function googleLogIn(googleUser) {
    const profile = decodeJwtResponse(googleUser.credential);

    try {
        const form_data = new URLSearchParams();
        form_data.append('id', profile.sub);
        form_data.append('name', profile.name);
        form_data.append('email', profile.email);
        form_data.append('time-zone', Intl.DateTimeFormat().resolvedOptions().timeZone);
        
        const response = await fetch("/ajax/google_oauth.php", {
            method: "POST",
            body: form_data
        });

        if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error_msg || 'Failed to log in with Google.');
        }
                
        window.location.replace("/texts");
    } catch (error) {
        console.error(error);
        showMessage(error.message, "alert-danger");
    }
} 

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
} 
