// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function() {
    /**
     * Sends email
     * This is triggered when user presses the "Send" button & submits the form
     */
    $("#form-support").on("submit", async function(e) {
        e.preventDefault();

        showMessage("Sending message to our support team...", "alert-info");

        const form_data = $(this).serialize();

        try {
            const response = await fetch("ajax/contact.php", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to send message.');
            }

            showMessage(
                    "Your message was successfully sent. You shall receive an answer briefly.",
                    "alert-success"
                );
            setTimeout(resetControls, 2000);
        } catch (error) {
            console.error(error);
            showMessage(error.message, "alert-danger");
        }
    }); 

    /**
     * Empties form input fields
     */
    function resetControls() {
        $("#alert-box").addClass("d-none");
        $("#name").val("");
        $("#email").val("");
        $("#message").val("");
    } 
});
