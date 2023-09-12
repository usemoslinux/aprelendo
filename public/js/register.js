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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function() {
    /**
     * Sends user registration form
     */
    $("#form-register").on("submit", function(e) {
        e.preventDefault();

        const form_data = $("#form-register").serializeArray();
        form_data.push({ name: "time-zone", value: Intl.DateTimeFormat().resolvedOptions().timeZone });

        showMessage("Processing...", "alert-info");

        $.ajax({
            type: "POST",
            url: "ajax/register.php",
            data: form_data
        })
            .done(function(data) {
                if (data.error_msg == null) {
                    showMessage(
                        "An email has been sent to your account with the activation link. Redirecting to login page...",
                        "alert-success"
                    );
                    
                    setTimeout(() => { window.location.replace("/login"); }, 4000);
                } else {
                    showMessage(data.error_msg, "alert-danger");
                }
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                showMessage(
                    "Oops! There was an unexpected error when trying to register you. Please try again later.",
                    "alert-danger"
                );
            });
    }); // end #form-register.on.submit

    /**
     * Updates flag and welcome message based on changes in the selected learning language
     */
    $("#learning-lang").on("change", function() {
        const lang_array = [
                            "Arabic",
                            "Chinese",
                            "Dutch",
                            "English",
                            "French",
                            "German",
                            "Greek",
                            "Hebrew",
                            "Hindi",
                            "Italian",
                            "Japanese",
                            "Korean",
                            "Portuguese",
                            "Russian",              
                            "Spanish"
                        ];
        const iso_array = ["ar", "zh", "nl", "en", "fr", "de", "el", "he", "hi", "it", "ja", "ko", "pt", "ru", "es"];

        const welcome_array = [
                                "أهلا بك!",
                                "欢迎！",
                                "Welkom!",
                                "Welcome!",
                                "Bienvenue!",
                                "Willkommen!",
                                "Καλως ΗΡΘΑΤΕ!",
                                "ברוך הבא!",
                                "स्वागत हे!",
                                "Benvenuto!",
                                "ようこそ！",
                                "어서 오십시오!",
                                "Bemvindo!",
                                "Добро пожаловать!",                    
                                "¡Bienvenido!"
                            ];

        const sel_index = $(this).prop("selectedIndex");
        const img_uri = "img/flags/" + iso_array[sel_index] + ".svg";

        $("h1")
            .text(welcome_array[sel_index])
            .prepend(
                '<img id="learning-flag" class="flag-icon" src="' +
                    img_uri +
                    '" alt="' +
                    lang_array[sel_index] +
                    '"></img><br>'
            );
        $("#welcome-msg").text(
            "You are only one step away from learning " + lang_array[sel_index]
        );
    }); // end #learning-lang.on.change
});
