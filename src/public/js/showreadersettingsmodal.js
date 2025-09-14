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

    $('#btn-save-reader-prefs').on('click', function () {
        // apply color mode
        const class_name = $('#mode').val() + 'mode';
        let $doc = $(parent.document.body);
        let $text_container = $("#text-container");
        $doc.removeClass().addClass(class_name);
        
        $text_container.css({
            'font-family' : $('#fontfamily').val(),
            'font-size' : $('#fontsize').val(),
            'text-align' : $('#alignment').val(),
            'line-height': $('#lineheight').val()
        });

        // change offcanvas color mode if exists
        const $off_canvas = $doc.find('.offcanvas');
        const $close_btn = $doc.find('#close-offcanvas');
        const color_modes = 'lightmode sepiamode darkmode';

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
        $.ajax({
            url: "/ajax/savepreferences.php",
            type: "POST",
            data: $("#prefs-modal-form").serialize()
        });
    }); // end #btn-save-reader-prefs.on.click
});