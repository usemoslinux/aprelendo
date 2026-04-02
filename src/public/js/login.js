// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function() {
    /**
     * Sends user login form
     */
    $("#form_login").on("submit", async function(e) {
        e.preventDefault();

        const form_data = $("#form_login").serialize();

        try {
            const response = await fetch("/ajax/login.php", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Login failed');
            }

            window.location.replace("/texts");
        } catch (error) {
            console.error(error);
            showMessage(error.message, "alert-danger");
        }
    }); 
});