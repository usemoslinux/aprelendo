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
        
        $doc = $(parent.document.body);
        $doc.css({
            'font-family' : $('#fontfamily').val(),
            'font-size' : $('#fontsize').val(),
            'text-align' : $('#alignment').val(),
            'line-height': $('#lineheight').val()
         });
        
        $('#text').css('line-height', $('#lineheight').val());

        $doc.removeClass().addClass($('#mode').val() + 'mode');

        // save changes 

        $.ajax({
            url: "/ajax/savepreferences.php",
            type: "POST",
            data: $("#prefs-modal-form").serialize()
        });
    });

});
