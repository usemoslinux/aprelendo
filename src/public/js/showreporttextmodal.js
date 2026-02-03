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

$(document).ready(function() {
    
    /**
     * Reports current text
     * Triggers when user clicks the Report button
     */
    $("#btn-report-text").on("click", async function() {
        const $btn_report = $(this);
        const text_id = $("#text-container").attr("data-idtext");
        const reason = $('input[name="report-reason"]:checked').val();

        if (!reason) {
            showMessage("Please select a reason for reporting.", "alert-danger", null, "report-alert-box");
            throw new Error("User forgot to complete reason for reporting");
        }

        showMessage("Sending... please wait.", "alert-info", null, "report-alert-box");
        $btn_report.prop("disabled", true); // disable report button

        try {
            const form_data = new URLSearchParams();
            form_data.append('text_id', text_id);
            form_data.append('reason', reason);

            const response = await fetch("/ajax/reporttext.php", {
                method: "POST",
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            // Assuming the server always returns JSON, even for errors
            const data = await response.json(); 

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to report text.');
            }

            showMessage(`Thank you! Your report has been submitted. Together,
                we're making our community safer and more enjoyable
                for everyone.`, "alert-success", null, "report-alert-box");

            setTimeout(function() {
                $('#report-text-modal').modal('hide');
                $btn_report.prop("disabled", false); // re-enable report button
            }, 3000);

        } catch (error) {
            console.error(error);
            showMessage(error.message, "alert-danger");
        }
    }); // end #btn-report-text.on.click

    $("#report-text-modal").on('hidden.bs.modal', function() { 
        $("#report-alert-box").hide(timeout);
    }); // end #report-text-modal.on.hidden.bs.modal
});
