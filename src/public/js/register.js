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
                        "We've sent you an email with the activation link. It might take a few minutes "
                        + "to arrive in your inbox, so please be patient. If you don't see it there, "
                        + "be sure to check your spam or junk folder, as sometimes it can end up there. Once you "
                        + "receive it, click on the link provided to activate your account.",
                        "alert-success"
                    );
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
            "Bulgarian",
            "Catalan",
            "Chinese",
            "Croatian",
            "Czech",
            "Danish",
            "Dutch",
            "English",
            "French",
            "German",
            "Greek",
            "Hebrew",
            "Hindi",
            "Hungarian",
            "Italian",
            "Japanese",
            "Korean",
            "Norwegian",
            "Polish",
            "Portuguese",
            "Romanian",
            "Russian",
            "Slovak",
            "Slovenian",
            "Spanish",
            "Swedish",
            "Turkish",
            "Vietnamese"
        ];
        const iso_array = [ 
            "ar", "bg", "ca", "zh", "hr", "cs", "da", "nl", "en", "fr", "de", "el", "he", "hi", "hu", "it", "ja",
            "ko", "no", "pl", "pt", "ro", "ru", "sk", "sl", "es", "sv", "tr", "vi"
        ];
        
        const welcome_array = [
            "أهلا بك!",         // Arabic
            "Добре дошли!",   // Bulgarian
            "Benvingut!",     // Catalan
            "欢迎！",          // Chinese
            "Dobrodošli!",    // Croatian
            "Vítejte!",       // Czech
            "Velkommen!",     // Danish
            "Welkom!",        // Dutch
            "Welcome!",       // English
            "Bienvenue!",     // French
            "Willkommen!",    // German
            "Καλώς ήρθατε!",  // Greek
            "ברוך הבא!",         // Hebrew
            "स्वागत है!",         // Hindi
            "Üdvözöljük!",    // Hungarian
            "Benvenuto!",     // Italian
            "ようこそ！",      // Japanese
            "환영합니다!",       // Korean
            "Velkommen!",     // Norwegian
            "Witaj!",         // Polish
            "Bem-vindo!",     // Portuguese
            "Bun venit!",     // Romanian
            "Добро пожаловать!", // Russian
            "Vitajte!",       // Slovak
            "Dobrodošli!",    // Slovenian
            "¡Bienvenido!",   // Spanish
            "Välkommen!",     // Swedish
            "Hoş geldiniz!",  // Turkish
            "Chào mừng!"      // Vietnamese
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
