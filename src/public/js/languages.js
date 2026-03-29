// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function() {
    /**
     * Edits language record
     * This is triggered when user presses the "Save" button & submits the form
     */
    $("#form-editlanguage").on("submit", async function(e) {
        e.preventDefault();

        try {
            const form_data = new URLSearchParams($(this).serialize());
            const response = await fetch("/ajax/editlanguage.php", {
                method: "post",
                body: form_data,
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to save language information.');
            }

            showMessage(`Your language information was successfully saved. You
                will soon be redirected to the main page.`, "alert-success");

            setTimeout(() => { window.location.replace("/texts"); }, 2000);
        } catch (error) {
            console.error(error);
            showMessage(error.message, "alert-danger");
        }
    });

    /**
     * Does some checks before submiting the form's data
     * Triggers when user clicks the Save button
     */
    $('#savebtn').on('click', function(e) {
        const dict_uri = $('#dict-uri').val();
        const translator_uri = $('#translator-uri').val();
        let error = false;

        // show an error message if...
        
        if (dict_uri.length == 0) {
            // 1. user forgot to include the dictionary URL
            
            showMessage("You need to specify the URL of the dictionary you want to use.", "alert-danger");
            error = true;
        } else if (dict_uri.indexOf('%s') == -1) {
            // 2. user forgot to include '%s' in the dictionary URL

            showMessage("The dictionary URL needs to include the position of the lookup word or phrase. For this, use '%s' (without quotation marks).", "alert-danger");
            error = true;
        } else if (translator_uri.length == 0) {
            // 3. user forgot to include the translator URL

            showMessage("You need to specify the URL of the translator you want to use.", "alert-danger");
            error = true;
        } else if (translator_uri.indexOf('%s') == -1) {
            // 4. user forgot to include '%s' in the translator URL

            showMessage("The translator URL needs to include the position of the lookup word or phrase. For this, use '%s' (without quotation marks).", "alert-danger");
            error = true;
        }

        if (error) {
            e.preventDefault();
            e.stopPropagation();
        }
    }); // end #savebtn.on.click

    // Listen for clicks on dropdown items
    $('.dict-select .dropdown-item').on('click', function () {
        // Get the value attribute of the clicked item
        let value = $(this).attr('value');
        
        // Find the input box within the same input-group and set its value
        $(this).closest('.input-group').find('input[type="url"]').val(value);
    });
});
