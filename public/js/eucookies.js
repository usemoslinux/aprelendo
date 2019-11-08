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
    if (document.cookie.indexOf("accept_cookies") === -1) {
        $("#eucookielaw").fadeIn(1200, function() {
            $(this).show();
        });
    }

    /**
     * Triggers when user closes the cookie consent message
     */
    $("#removecookie").click(function() {
        setCookie("accept_cookies", true, 365 * 10);
        $("#eucookielaw").fadeOut(1200, function() {
            $(this).remove();
        });
    }); // end #removecookie.on.click
});
