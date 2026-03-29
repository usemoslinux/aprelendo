// SPDX-License-Identifier: GPL-3.0-or-later

/**
 * Logs out the user both from Google and from the server, then redirects to the main page
 * Set after_user_delete to true if this logout is happening after user deletion
 * @param {boolean} after_user_delete 
 */
async function handleLogout(after_user_delete = false) {
    // Google cleanup
    if (typeof google !== 'undefined') {
        google.accounts.id.disableAutoSelect();
    }

    // Server cleanup
    try {
        const form_data = new URLSearchParams();
        form_data.append('after_user_delete', after_user_delete);

        const response = await fetch('/ajax/logout.php', {
            method: "POST",
            body: form_data
        });

        if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error_msg || 'Failed to logout.');
        }

        window.location.href = '/';
    } catch (error) {
        console.error("Logout failed", error);
        alert(`Oops! ${error.message}`);
    }
}