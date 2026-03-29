// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function() {
    /**
     * Returns the CSS font size value for the reader preview.
     *
     * @param {string} font_size
     * @returns {string}
     */
    function getFontSizeCssValue(font_size) {
        return font_size + "rem";
    } // end getFontSizeCssValue

    $('#btn-save-reader-prefs').on('click', async function () {
        // apply color mode
        const class_name = $('#mode').val() + 'mode';
        const color_modes = 'lightmode sepiamode darkmode';
        let $doc = $(parent.document.body);
        let $text_container = $("#text-container");
        $doc.removeClass(color_modes).addClass(class_name);
        
        $text_container.css({
            'font-family' : $('#fontfamily').val(),
            'font-size' : getFontSizeCssValue($('#fontsize').val()),
            'text-align' : $('#alignment').val(),
            'line-height': $('#lineheight').val()
        });

        // change offcanvas color mode if exists
        const $off_canvas = $doc.find('.offcanvas');
        const $close_btn = $doc.find('#close-offcanvas');

        if ($off_canvas.length) {
            $off_canvas.removeClass(color_modes).addClass(class_name);
            $close_btn.toggleClass('btn-close-white', class_name === 'darkmode');
        }

        // change audio player container color mode if exists
        const $audio_container = $doc.find('#audioplayer-container');
        if ($audio_container.length) {
            $audio_container.removeClass(color_modes).addClass(class_name);
        }
        
        // save changes 
        try {
            const form_data = new URLSearchParams($("#prefs-modal-form").serialize());
            const response = await fetch("/ajax/savepreferences.php", {
                method: "POST",
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to save reader preferences.');
            }
        } catch (error) {
            console.error(error);
            showMessage(error.message, "alert-danger");
        }
    }); // end #btn-save-reader-prefs.on.click
});
