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
     * Saves preferences to the database and shows success/failure message
     * It is executed when user clicks the submit button
     * @param  {event object} e Used to prevent reloading of the page
     */
    $("#prefs-form").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        $.ajax({
            url: "ajax/savepreferences.php",
            type: "POST",
            data: $("#prefs-form").serialize()
        })
            .done(function(data) {
                if (data.error_msg) {
                    showMessage(data.error_msg, "alert-danger");
                } else {
                    showMessage("Your preferences were successfully saved."
                    + " You will soon be redirected to the main page.", "alert-success");
                    
                    setTimeout(() => { window.location.replace("/texts"); }, 2000);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                showMessage("<strong>Oops!</strong> Something went wrong when trying to save your preferences.", "alert-danger");
            });
    }); // end #prefs-form.submit
});
