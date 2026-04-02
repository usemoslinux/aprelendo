// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function() {
    
    /**
     * Toggles like for text
     * Triggers when user clicks on a heart
     */
    $(document).on("click", "span.bi-heart, span.bi-heart-fill", async function() {
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
    }); 

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
