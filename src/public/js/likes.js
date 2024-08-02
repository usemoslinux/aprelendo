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
     * Toggles like for text
     * Triggers when user clicks on a heart
     */
    $("span.bi-heart, span.bi-heart-fill").on("click", function() {
        const $like_btn = $(this);
        const text_id = $like_btn.attr("data-idText");

        toggleLike($like_btn);

        $.ajax({
            type: "POST",
            url: "ajax/togglelike.php",
            data: { id: text_id }
        })
            .done(function(data) {
                if (data.error_msg) {
                    console.log("Oops! There was an unexpected error");
                    toggleLike($like_btn);
                }
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                console.log("Oops! There was an unexpected error");
                toggleLike($like_btn);
            });
    }); // end span.bi-heart-fill.on.click

    function toggleLike($like_btn) {
        $like_btn.toggleClass("bi-heart bi-heart-fill");
        const total_likes = parseInt(
            $like_btn.siblings("small").text()
        );
        if ($like_btn.hasClass("bi-heart-fill")) {
            $like_btn.siblings("small").text(total_likes + 1);
        } else {
            $like_btn.siblings("small").text(total_likes - 1);
        }
    }
});
