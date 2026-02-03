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