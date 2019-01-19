/**
 * Copyright (C) 2018 Pablo Castagnino
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

$(document).ready(function () {
    $('i.fa-heart').on('click', function () {
        var like_btn = $(this);
        var text_id = like_btn.attr('data-idText');
        $.ajax({
            type: "POST",
            url: "ajax/togglelike.php",
            data: {id: text_id}
            // dataType: "dataType"
        })
        .done(function (data) {
            if (data.error_msg) {
                alert('error!');
            } else {
                like_btn.toggleClass('fas far');
                var total_likes = parseInt(like_btn.siblings('small').text());
                if (like_btn.hasClass('fas')) {
                    like_btn.siblings('small').text(total_likes+1);      
                } else {
                    like_btn.siblings('small').text(total_likes-1);  
                }
            }
        })
        .fail(function (xhr, ajaxOptions, thrownError) {
            alert(thrownError);
        });
        
    });
});