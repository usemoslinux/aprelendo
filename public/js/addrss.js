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

    // load rss feeds
    $.ajax({
        type: "GET",
        url: "ajax/fetchrssfeeds.php",
    })
    .done(function (data) {
        $('.lds-ripple').fadeOut(function () { 
            $(this).after(data);
         })
    })
    .fail(function (xhr, ajaxOptions, thrownError) {
        $('.lds-ripple').fadeOut(function () { 
            $(this).after('<div class="alert alert-danger">Oops! There was an error trying to retrieve your RSS feeds. Please try again later.</div>');
        });    
    });


    $(".btn-link").on("click", function () {
        $sel_card = $(".fas", this);

        $sel_card.toggleClass("fa-chevron-right")
            .toggleClass("fa-chevron-down");

        if ($(this).parents('.card-body').length > 0) {
            $accordion = $(this).closest(".card-body"); 
        } else {
            $accordion = $('.subaccordion > .card > .card-header    ');
        }

        $('.fas', $accordion).each(function () {
            if ($(this).hasClass('fa-chevron-down') && $(this)[0] !== $sel_card[0]) {
                $(this).toggleClass("fa-chevron-right")
                    .toggleClass("fa-chevron-down");
            }
        });
    });

    function htmlEscape(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function addTextToLibrary($entry_info, $entry_text, add_mode) {
        var art_title = $.trim($entry_info.text());
        var art_author = $entry_info.attr("data-author");
        var art_url = $entry_info.attr("data-src");
        var art_pubdate = $entry_info.attr("data-pubdate");
        var art_content = $entry_text.text();

        if (add_mode == 'edit') {
            var form = $('<form id="form_add_audio" action="../addtext.php" method="post"></form>')
                .append('<input type="hidden" name="art_title" value="' + htmlEscape(art_title) + '">')
                .append('<input type="hidden" name="art_author" value="' + htmlEscape(art_author) + '">')
                .append('<input type="hidden" name="art_url" value="' + htmlEscape(art_url) + '">')
                .append('<input type="hidden" name="art_pubdate" value="' + htmlEscape(art_pubdate) + '">')
                .append('<input type="hidden" name="art_content" value="' + htmlEscape(art_content) + '">')
                .append('<input type="hidden" name="art_is_shared" value="true">');
            $('body').append(form);
            form.submit();
        } else {
            $.ajax({
                    type: "POST",
                    url: "ajax/addtext.php",
                    dataType: "JSON",
                    data: {
                        title: art_title,
                        author: art_author,
                        url: art_url,
                        pubdate: art_pubdate,
                        text: art_content,
                        mode: 'rss'
                    }
                }).done(function (data) {
                    switch (add_mode) {
                        case "readlater":
                            $entry_text
                                .siblings()
                                .remove()
                                .end()
                                .after("<p class='alert alert-success'>Text was successfully added to your library</p>")
                                .next()
                                .show()
                                .fadeOut(2000);
                            break;
                        case "readnow":
                            location.replace("../showtext.php?id=" + data.insert_id + "&sh=1");
                            break;
                        default:
                            break;
                    }
                })
                .fail(function () {
                    $entry_text
                        .siblings()
                        .remove()
                        .end()
                        .after("<p class='alert alert-danger'>There was an error trying to add this text to your library!</p>")
                        .next()
                        .show()
                        .fadeOut(2000);
                });
        }
    }

    $(".btn-readlater").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        addTextToLibrary(
            $(this).closest(".card").find(' .entry-info'),
            $(this).parent().siblings(".entry-text"),
            "readlater"
        );
    });

    $(".btn-readnow").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        addTextToLibrary(
            $(this).closest(".card").find(' .entry-info'),
            $(this).parent().siblings(".entry-text"),
            "readnow"
        );
    });

    $(".btn-edit").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        addTextToLibrary(
            $(this).closest(".card").find(' .entry-info'),
            $(this).parent().siblings(".entry-text"),
            "edit"
        );
    });

});