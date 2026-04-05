// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function () {
    const languages = [
        { name: "Arabic", iso: "ar", welcome: "أهلا بك!" },
        { name: "Bulgarian", iso: "bg", welcome: "Добре дошли!" },
        { name: "Catalan", iso: "ca", welcome: "Benvingut!" },
        { name: "Chinese", iso: "zh", welcome: "欢迎！" },
        { name: "Croatian", iso: "hr", welcome: "Dobrodošli!" },
        { name: "Czech", iso: "cs", welcome: "Vítejte!" },
        { name: "Danish", iso: "da", welcome: "Velkommen!" },
        { name: "Dutch", iso: "nl", welcome: "Welkom!" },
        { name: "English", iso: "en", welcome: "Welcome!" },
        { name: "French", iso: "fr", welcome: "Bienvenue!" },
        { name: "German", iso: "de", welcome: "Willkommen!" },
        { name: "Greek", iso: "el", welcome: "Καλώς ήρθατε!" },
        { name: "Hebrew", iso: "he", welcome: "ברוך הבא!" },
        { name: "Hindi", iso: "hi", welcome: "स्वागत है!" },
        { name: "Hungarian", iso: "hu", welcome: "Üdvözöljük!" },
        { name: "Italian", iso: "it", welcome: "Benvenuto!" },
        { name: "Japanese", iso: "ja", welcome: "ようこそ！" },
        { name: "Korean", iso: "ko", welcome: "환영합니다!" },
        { name: "Norwegian", iso: "no", welcome: "Velkommen!" },
        { name: "Polish", iso: "pl", welcome: "Witaj!" },
        { name: "Portuguese", iso: "pt", welcome: "Bem-vindo!" },
        { name: "Romanian", iso: "ro", welcome: "Bun venit!" },
        { name: "Russian", iso: "ru", welcome: "Добро пожаловать!" },
        { name: "Slovak", iso: "sk", welcome: "Vitajte!" },
        { name: "Slovenian", iso: "sl", welcome: "Dobrodošli!" },
        { name: "Spanish", iso: "es", welcome: "¡Bienvenido!" },
        { name: "Swedish", iso: "sv", welcome: "Välkommen!" },
        { name: "Turkish", iso: "tr", welcome: "Hoş geldiniz!" },
        { name: "Vietnamese", iso: "vi", welcome: "Chào mừng!" }
    ];

    /**
     * Gets a language object from its ISO code.
     *
     * @param {string} language_iso
     * @returns {Object|null}
     */
    function getLanguageByIso(language_iso) {
        return languages.find(language => language.iso === language_iso) || null;
    }

    /**
     * Updates the register side panel based on the selected learning language.
     *
     * @param {string} language_iso
     * @returns {void}
     */
    function updateWelcomeMessage(language_iso) {
        const language_data = getLanguageByIso(language_iso);

        if (!language_data) {
            return;
        }

        $("#register-language-title").text(language_data.welcome);
        $("#welcome-msg").text(`You are only one step away from learning ${language_data.name}.`);

        $(".auth-language-pill[data-learning-lang]").removeClass("is-active");
        $(`.auth-language-pill[data-learning-lang="${language_iso}"]`).addClass("is-active");
    }
    
    /**
     * Sends user registration form
     */
    $("#form-register").on("submit", async function (e) {
        e.preventDefault();

        const form_data_array = $("#form-register").serializeArray();
        form_data_array.push({ name: "time-zone", value: Intl.DateTimeFormat().resolvedOptions().timeZone });

        showMessage(
            `We are processing your registration. Please wait a moment while
            we securely handle your information. This should only take a
            few seconds.`, "alert-info"
        );

        $("#btn_register").prop("disabled", true); // Disable button to prevent multiple submissions

        try {
            const form_data = new URLSearchParams();
            form_data_array.forEach(item => {
                form_data.append(item.name, item.value);
            });

            const response = await fetch("/ajax/register.php", {
                method: "POST",
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            $("#btn_register").prop("disabled", false); // Re-enable button
            if (!data.success) {
                throw new Error(data.error_msg || 'Registration failed.');
            }

            if (data.is_self_hosted === true) {
                showMessage("Registration successful! You will soon be redirected to the login page.",
                    "alert-success");
                setTimeout(() => { window.location.replace("/login"); }, 2000);
            } else {
                const message = `Registration successful! We've sent an activation email to the address you provided.
                    Please check your inbox to complete the registration process. The email might take a few minutes
                    to arrive. If you don't find it in your inbox, check your spam or junk folder as it might have
                    been filtered there. Once you locate the email, click on the activation link to activate your
                    account and start using our platform.`;

                showMessage(message, "alert-success");
            }
        } catch (error) {
            $("#btn_register").prop("disabled", false); // Re-enable button on error too
            console.error(error);
            showMessage(error.message, "alert-danger");
        }
    }); 

    /**
     * Updates the side panel based on changes in the selected learning language.
     */
    $("#learning-lang").on("change", function () {
        updateWelcomeMessage($(this).val());
    });

    /**
     * Syncs side-panel language pills with the learning language selector.
     */
    $(".auth-language-pill[data-learning-lang]").on("click", function () {
        const language_iso = $(this).data("learning-lang");

        $("#learning-lang").val(language_iso).trigger("change");
    });

    /**
     * Focuses the learning language selector when the user wants the full language list.
     */
    $(".auth-language-pill[data-focus-learning-lang]").on("click", function () {
        $("#learning-lang").trigger("focus");
    });

    updateWelcomeMessage($("#learning-lang").val());
});
