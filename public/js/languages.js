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
     * Shows down/right arrow when user opens/closes accordion item
     * Triggers when user opens/closes accordion item
     */
    $(".btn-link").on("click", function() {
        $sel_card = $(".fas", this);

        $sel_card
            .toggleClass("fa-chevron-right")
            .toggleClass("fa-chevron-down");

        $(".fas", "#accordion").each(function() {
            if (
                $(this).hasClass("fa-chevron-down") &&
                $(this)[0] !== $sel_card[0]
            ) {
                $(this)
                    .toggleClass("fa-chevron-right")
                    .toggleClass("fa-chevron-down");
            }
        });
    }); // end .btn-link.on.click
});
