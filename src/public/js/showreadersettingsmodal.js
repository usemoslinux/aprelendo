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
        const className = $('#mode').val() + 'mode';
        let $doc = $(parent.document.body);
        let $text_container = $("#text-container");
        $doc.removeClass().addClass(className);
        
        $text_container.css({
            'font-family' : $('#fontfamily').val(),
            'font-size' : $('#fontsize').val(),
            'text-align' : $('#alignment').val(),
            'line-height': $('#lineheight').val()
        });

        // change audioplayer class
        let $audioplayer = $doc.find("#audioplayer-container");
        
        if (!$audioplayer.hasClass('d-none')) {
            $audioplayer.removeClass().addClass(className + ' py-3');
        } else {
            $audioplayer.removeClass().addClass(className).addClass('py-3 d-none');
        }

        // change offcanvas classes
        if ($doc.find('.offcanvas').length) {
            if (className == 'darkmode') {
                $doc.find('.offcanvas').addClass('text-bg-dark');
                $doc.find('#close-offcanvas').addClass('btn-close-white');
            } else {
                $doc.find('.offcanvas').removeClass('text-bg-dark');
                $doc.find('#close-offcanvas').removeClass('btn-close-white');
            }    
        }
        // save changes 

        $.ajax({
            url: "/ajax/savepreferences.php",
            type: "POST",
            data: $("#prefs-modal-form").serialize()
        });
    }); // end #btn-save-reader-prefs.on.click
});