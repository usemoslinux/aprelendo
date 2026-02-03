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
    $("span.bi-heart, span.bi-heart-fill").on("click", async function() {
        const $like_btn = $(this);
        const text_id = $like_btn.attr("data-idText");

        toggleLike($like_btn);

        try {
            const form_data = new URLSearchParams();
            form_data.append('id', text_id);

            const response = await fetch("/ajax/togglelike.php", {
                method: "POST",
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to toggle like for text');
            }
        } catch (error) {
            console.error("Error", error);
            toggleLike($like_btn); // Revert the like on any error
        }
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
