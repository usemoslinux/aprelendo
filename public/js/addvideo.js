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
    emptyAll();
    $("#url").focus();

    /**
     * Adds video to database
     * This is triggered when user presses the "Save" button & submits the form
     * @param e {Event}
     */
    $("#form-addvideo").on("submit", function(e) {
        e.preventDefault();

        const form_data = $("#form-addvideo").serializeArray();
        form_data.push({ name: "shared-text", value: true });

        $.ajax({
            type: "POST",
            url: "ajax/addtext.php",
            data: form_data
        })
            .done(function(data) {
                if (typeof data != "undefined") {
                    if (typeof data.error_msg != "undefined") {
                        showMessage(data.error_msg, "alert-danger");
                    }
                } else {
                    window.location.replace("/sharedtexts");
                }
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                showMessage(
                    "Oops! There was an unexpected error when uploading this text.",
                    "alert-danger"
                );
            }); // end of ajax
    }); // end #form-addvideo.on.submit

    /**
     * Fetches YouTube video, including title, channel & subtitle
     */
    function fetch_url(url) {       
        emptyAll(); // empty all input boxes

        const video_id = extractYTId(url); //get YouTube video id

        if (video_id != "") {
            const embed_url = "https://www.youtube.com/embed/" + video_id;

            $("#btn-fetch-img")
                .removeClass()
                .addClass("fas fa-sync fa-spin text-warning");

            $.ajax({
                type: "POST",
                url: "ajax/fetchvideo.php",
                data: { video_id: video_id }
            })
                .done(function(data) {
                    if (typeof data != "undefined") {
                        if (typeof data.error_msg != "undefined") {
                            showMessage(data.error_msg, "alert-danger");
                        } else {
                            if ($("#yt-video").length) {
                                $("#yt-video")
                                    .get(0)
                                    .contentWindow.location.replace(embed_url);
                                // changing $('#yt-video') src attribute would affect browser history, that's why
                                // we do it this way
                            }
                            $("#title").val(data.title);
                            $("#author").val(data.author);
                            $("#url").val(url);

                            if (data.text == "") {
                                $("#text").val("");
                                showMessage(
                                    'This video does not include valid subtitles.',
                                    "alert-danger"
                                );
                            } else {
                                $("#text").val(data.text);
                                $("#alert-box").addClass("d-none");
                            }
                        }
                    }
                })
                .fail(function(xhr, ajaxOptions, thrownError) {
                    showMessage(
                        "Oops! There was an unexpected error trying to get that video. Please try again later.",
                        "alert-danger"
                    );
                })
                .always(function() {
                    $("#btn-fetch-img")
                        .removeClass()
                        .addClass("fas fa-arrow-down text-warning");
                }); // end ajax
        } else {
            showMessage(
                'Malformed Youtube URL link. It should have the following format: https://www.youtube.com/watch?' +
                'v=video_id or https://youtu.be/video_id. Remember to replace "video_id" with the corresponding ' +
                'video ID and try again.',
                "alert-danger"
            );
        }
    } // end fetch_url

    /**
     * Empties form
     */
    function emptyAll() {
        if ($("#external_call").length == 0) {
            $("input")
                .not(":hidden")
                .val("");
            $("#text").val("");
            $("#yt-video").attr("src", "about:blank");
        }
    } // end emptyAll

    /**
     * Converts string to Sentence case (first character in upper case, the rest in lower case)
     * @param {string} str
     */
    function toSentenceCase(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    } // end toSentenceCase

    function extractYTId(url) {
        if (url == "") {
            return "";
        }

        // check if user copied the url by right-clicking the video (Google's recommended method)
        if (url.lastIndexOf("https://youtu.be/") === 0) {
            return url.slice(17);
        } else {
            // check if user copied the url directly from the url bar (alternative method)
            const yt_urls = new Array(
                "https://www.youtube.com/watch",
                "https://youtube.com/watch",
                "https://m.youtube.com/watch"                
            );

            const url_split = url.split("?");
            const url_params = url_split[1].split("&");

            // check if it's a valid youtube URL
            for (const yt_url of yt_urls) {
                if (url_split[0].lastIndexOf(yt_url) === 0) {
                    // extract YouTube video id
                    for (const url_param of url_params) {
                        if (url_param.lastIndexOf("v=") === 0) {
                            return url_param.substring(2);
                        }
                    }
                }
            }
        }
    } // end extractYTId

    // Check for an external api call. If detected, try to fetch text using Mozilla's readability parser
    if ($("#external_call").length) {
        fetch_url($("#url").val());
    }

    $("#btn-fetch").on("click", function(e) {
        fetch_url($("#url").val());
    });

    $("#url").on("paste", function(e) {
        const pastedData = e.originalEvent.clipboardData.getData('text');
        fetch_url(pastedData);
    });
});
