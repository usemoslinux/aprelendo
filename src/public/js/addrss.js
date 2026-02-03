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

$(document).ready(function () {
    // load rss feeds
    const $accordion_items = $(".accordion").find(".accordion-item");
    const feed_count = $accordion_items.length;

    if (feed_count == 0) {
        $("#accordion").html(function () {
            return showMessage(
                '<p>No RSS feeds detected yet.</p>'
                + '<p>To begin discovering new content, visit the <a class="alert-link" href="/languages">languages</a> '
                + 'section and add up to three feeds per language.</p><p>If you need further guidance, explore our quick '
                + '<a href="https://blog.aprelendo.com/2024/12/a-step-by-step-guide-to-adding-rss-texts-to-aprelendo/" '
                + 'target="_blank" rel="noopener noreferrer" class="alert-link">setup guide</a>.</p>',
                'alert-warning','Get Started'
            );
        });
        return;
    }

    for (let i = 0; i < feed_count; i++) {
        const $feed = $accordion_items.eq(i);
        const feed_full_id = '#' + $feed.attr('id');
        const feed_index = $feed.data('feed-index');

        (async (feed_full_id, feed_index) => {
            try {
                const params = new URLSearchParams({ feed_index: feed_index });
                
                const response = await fetch(`ajax/fetchrssfeeds.php?${params.toString()}`);
                
                const data = await response.json();;

                if (!data.success) {
                    let $acordion_item = $(feed_full_id);
                    let item_index = feed_index+1;

                    $acordion_item.find('.rss-placeholder-text').text("Error loading feed");
                    $acordion_item.find('.accordion-button').removeClass('rss-placeholder-glow').addClass('text-bg-danger');
                    $acordion_item.find('h2').after('<div id="item-' + item_index 
                        + '" class="collapse" data-bs-parent="#accordion"><div class="accordion-body"><div>'
                        + data.error_msg + '</div></div></div>');

                    throw new Error(data.error_msg || 'Failed to fetch RSS feed');
                }

                $(feed_full_id).html(data.payload);
            } catch (error) {
                console.error(error);
            }
        })(feed_full_id, feed_index); // Immediately invoke with current loop variables
    }

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
     * Opens the RSS entry in the editor.
     * @param {html entity} $entry_info contains text info (author, src, pubdate, etc.)
     * @param {html entity} $entry_text contains text to add
     */
    function openRSSInEditor($entry_info, $entry_text) {
        const text_title = $entry_info.text().trim();
        const text_author = $entry_info.attr("data-author");
        const text_url = $entry_info.attr("data-src");
        const art_pubdate = $entry_info.attr("data-pubdate");
        const text_content = $entry_text[0].innerText;

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
        form.trigger( "submit" );
    } // end openRSSInEditor

    /**
     * Triggers when user clicks the Edit button
     * @param e {Event}
     */
    $(document).on("click", ".btn-edit", function (e) {
        e.preventDefault();
        e.stopPropagation();

        openRSSInEditor(
            $(this)
                .closest(".accordion-item")
                .find(".entry-info"),
            $(this)
                .parent()
                .siblings(".entry-text"),
        );
    });
});

