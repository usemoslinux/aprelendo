// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function() {
    const $banner = $("#eucookielaw");

    if (document.cookie.indexOf("accept_cookies") === -1) {
        $banner.addClass("show");
    }

    /**
     * Triggers when user closes the cookie consent message
     */
    $("#removecookie").on("click", function() {
        setCookie("accept_cookies", true, 365 * 10);
        $banner.removeClass("show");
    }); // end #removecookie.on.click
});
