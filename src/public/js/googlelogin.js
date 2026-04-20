// SPDX-License-Identifier: GPL-3.0-or-later

/**
 * Google Log in
 * Uses the new Google Identity Services library for authentication
 */
async function googleLogIn(googleUser) {
    try {
        const form_data = new URLSearchParams();
        form_data.append('credential', googleUser.credential);
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
