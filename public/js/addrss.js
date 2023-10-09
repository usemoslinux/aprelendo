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

$(document).ready(function () {
    // load rss feeds
    $.ajax({
        type: "GET",
        url: "ajax/fetchrssfeeds.php"
    })
        .done(function (data) {
            $(".lds-ellipsis").fadeOut(function () {
                if (data.error_msg != null) {
                    showMessage(data.error_msg, 'alert-danger');
                } else {
                    $(this).after(data);
                }
            });
        })
        .fail(function (xhr, ajaxOptions, thrownError) {
            $(".lds-ellipsis").fadeOut(function () {
                showMessage('There was an error trying to retrieve your RSS feeds. Please try again later.',
                'alert-danger');
            });
        }); // end $.ajax

    /**
     * Escapes string
     * @param {string} str
     */
    function htmlEscape(str) {
        return str
            .replace(/&/g, "&amp;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    } // end htmlEscape

    /**
     * Adds RSS text to the db
     * @param {html entity} $entry_info contains text info (author, src, pubdate, etc.)
     * @param {html entity} $entry_text contains text to add
     * @param {string} add_mode three possibilities, each corresponding to one button: 'edit', 'readlater', 'readnow'
     */
    function addTextToLibrary($entry_info, $entry_text, add_mode) {
        const text_title = $.trim($entry_info.text());
        const text_author = $entry_info.attr("data-author");
        const text_url = $entry_info.attr("data-src");
        const art_pubdate = $entry_info.attr("data-pubdate");
        const text_content = $entry_text[0].innerText;

        if (add_mode == "edit") {
            // create a hidden form and submit it
            const form = $(
                '<form id="form_add_audio" action="../addtext" method="post"></form>'
            )
                .append(
                    '<input type="hidden" name="text_title" value="' +
                    text_title +
                    '">'
                )
                .append(
                    '<input type="hidden" name="text_author" value="' +
                    htmlEscape(text_author) +
                    '">'
                )
                .append(
                    '<input type="hidden" name="text_url" value="' +
                    htmlEscape(text_url) +
                    '">'
                )
                .append(
                    '<input type="hidden" name="art_pubdate" value="' +
                    htmlEscape(art_pubdate) +
                    '">'
                )
                .append(
                    '<input type="hidden" name="text_content" value="' +
                    htmlEscape(text_content) +
                    '">'
                )
                .append(
                    '<input type="hidden" name="text_is_shared" value="true">'
                );
            $("body").append(form);
            form.submit();
        } else {
            $.ajax({
                type: "POST",
                url: "ajax/addtext.php",
                dataType: "JSON",
                data: {
                    title: text_title,
                    author: text_author,
                    url: text_url,
                    pubdate: art_pubdate,
                    text: text_content,
                    mode: "rss"
                }
            })
                .done(function (data) {
                    if (data.error_msg != null) {
                        showRSSMessage(
                            $entry_text,
                            data.error_msg,
                            "alert-danger"
                        );
                    } else {
                        switch (add_mode) {
                            case "readlater":
                                showRSSMessage(
                                    $entry_text,
                                    "Text was successfully added to the shared texts library",
                                    "alert-success"
                                );
                                break;
                            case "readnow":
                                location.replace(
                                    "../showtext?id=" +
                                    data.insert_id +
                                    "&sh=1"
                                );
                                break;
                            default:
                                break;
                        }
                    }
                })
                .fail(function () {
                    showRSSMessage(
                        $entry_text,
                        "There was an error trying to add this text to your library!",
                        "alert-danger"
                    );
                });
        }
    } // end addTextToLibrary

    /**
     * Shows custom message in the top section of the screen
     * @param {Jquery object} $entry_text
     * @param {string} html
     * @param {string} type
     */
    function showRSSMessage($entry_text, html, type) {
        html = '<p class="alert ' + type + '">' + html + "</p>";
        $entry_text
            .siblings()
            .remove()
            .end()
            .after(html)
            .next()
            .show()
            .fadeOut(3000);
    } // end showRSSMessage

    /**
     * Triggers when user clicks the Edit, Read now or Read later buttons
     * @param e {Event}
     */
    $(document).on("click", ".btn-readlater, .btn-readnow, .btn-edit", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const action = $(this).data("type");
        addTextToLibrary(
            $(this)
                .closest(".accordion-item")
                .find(".entry-info"),
            $(this)
                .parent()
                .siblings(".entry-text"),
            action
        );
    });
});


