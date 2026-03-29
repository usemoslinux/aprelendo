// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function() {
    /**
     * Saves preferences to the database and shows success/failure message
     * It is executed when user clicks the submit button
     * @param  {event object} e Used to prevent reloading of the page
     */
    $("#prefs-form").on( "submit", async function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        try {
            const form_data = new URLSearchParams($("#prefs-form").serialize());
            
            const response = await fetch("/ajax/savepreferences.php", {
                method: "POST",
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to save preferences.');
            }
            
            showMessage(`Your preferences were successfully saved. You will
                soon be redirected to the main page.`, "alert-success");
            
            setTimeout(() => { window.location.replace("/texts"); }, 2000);
        } catch (error) {
            console.error(error);
            showMessage(error.message, "alert-danger");
        }
    }); // end #prefs-form.submit
});
