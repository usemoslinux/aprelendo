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

    $('#btn-save-reader-prefs').on('click', function () {
        // apply changes
        var className = $('#mode').val() + 'mode';

        $doc = $(parent.document.body);
        // change document styles and class
        $doc.css({
            'font-family' : $('#fontfamily').val(),
            'font-size' : $('#fontsize').val(),
            'text-align' : $('#alignment').val(),
            'line-height': $('#lineheight').val()sss
         });
        
        $('#text').css('line-height', $('#lineheight').val());

        $doc.removeClass().addClass(className);

        // change audioplayer class
        $doc.find("#audioplayer-container").removeClass().addClass(className);

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
    });

});
