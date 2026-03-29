// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function() {
    /**
     * Submits email to create new password and reset old one
     * Triggers when user submits #form_forgot_password form
     * @param e {Event}
     */
    $(document).on("submit", "#form_forgot_password", async function(e) {
        e.preventDefault();
        const form_data = $("#form_forgot_password").serialize();
        showMessage(
            "Your request is being processed. Please wait...",
            "alert-info"
        );

        try {
            const response = await fetch("/ajax/forgotpassword.php", {
                method: "POST",
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: form_data
            });
            
            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
            
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to process request.');
            }
            
            const message = `We've sent you an email to the address you provided. It might take a few minutes 
                to arrive in your inbox, so please be patient. If you don't see it there, 
                be sure to check your spam or junk folder, as sometimes it can end up there. Once you 
                receive it, click on the link provided to create your new password.`;

            showMessage(message, "alert-success");
        } catch (error) {
            console.error(error);
            showMessage(error.message, "alert-danger");
        }
    }); // end #form_forgot_password.on.submit

    /**
     * Checks & saves new user password in db
     * Triggers when user submits the #form_create_new_password form
     * @param e {Event}
     */
    $(document).on("submit", "#form_create_new_password", async function(e) {
        e.preventDefault();
        const form_data = $("#form_create_new_password").serialize();
        showMessage(
            "Your request is being processed. Please wait...",
            "alert-info"
        );

        try {
            // check if passwords entered by user are identical
            if ($("#newpassword").val() !== $("#newpassword-confirmation").val()) {
                $("#newpassword").val("");
                $("#newpassword-confirmation").val("");
                throw new Error("The passwords you entered are not identical. Please try again.");
            }

            const response = await fetch("/ajax/forgotpassword.php", {
                method: "post",
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to save new password.');
            }
            
            showMessage(
                `Your new password has been successfully saved! You will be
                soon be redirected to the login page.`, "alert-success"
            );
            
            setTimeout(function() {
                window.location.replace(
                    "https://www.aprelendo.com/login.php"
                );
            }, 2000);
        } catch (error) {
            console.error(error);
            showMessage(error.message, "alert-danger");
        }
    }); // end #form_create_new_password.on.submit
});
