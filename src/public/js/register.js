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
     * Sends user registration form
     */
    $("#form-register").on("submit", function(e) {
        e.preventDefault();

        const form_data = $("#form-register").serializeArray();
        form_data.push({ name: "time-zone", value: Intl.DateTimeFormat().resolvedOptions().timeZone });

        showMessage(
            "We are processing your registration. Please wait a moment while we securely handle your information. "
            + "This should only take a few seconds.",
            "alert-info"
        );

        $("#btn_register").prop("disabled", true); // Disable button to prevent multiple submissions

        $.ajax({
            type: "POST",
            url: "ajax/register.php",
            data: form_data
        })
            .done(function(data) {
                $("#btn_register").prop("disabled", false); // Re-enable button
                if (data.error_msg == null) {
                    if (data.is_self_hosted === true) {
                        showMessage("Registration successful! You will soon be redirected to the login page.",
                            "alert-success");
                        setTimeout(() => { window.location.replace("/login"); }, 2000);
                    } else {
                        showMessage(
                        "Registration successful! We've sent an activation email to the address you provided. "
                            + "Please check your inbox to complete the registration process. "
                            + "The email might take a few minutes to arrive. If you don't find it in your inbox, check "
                            + "your spam or junk folder as it might have been filtered there. "
                            + "Once you locate the email, click on the activation link to activate your account and "
                            + "start using our platform.",
                            "alert-success"
                        );
                    }
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
