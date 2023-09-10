    /**
     * Shows custom message in the top section of the screen
     * @param {string} html
     * @param {string} type
     */
    function showMessage(html, type) {
        $("#alert-msg")
            .html(html)
            .removeClass()
            .addClass("alert " + type);
        $(window).scrollTop(0);
    } // end showMessage