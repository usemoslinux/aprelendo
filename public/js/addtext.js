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
    // Check for an external api call. If detected, try to fetch text using Mozilla's readability parser
    if ($("#external_call").length) {
        fetch_url();
    }

    /**
     * Adds text to database
     * This is triggered when user presses the "Save" button & submits the form
     */
    $("#form-addtext").on("submit", function(e) {
        e.preventDefault();

        var form_data = new FormData(document.getElementById("form-addtext"));

        $.ajax({
            type: "POST",
            url: "ajax/addtext.php",
            data: form_data,
            dataType: "json",
            contentType: false,
            processData: false
        })
            .done(function(data) {
                if (typeof data != "undefined") {
                    showMessage(data.error_msg, "alert-danger");
                } else {
                    if (form_data.get("shared-text") == "on") {
                        window.location.replace("sharedtexts.php");
                    } else {
                        window.location.replace("texts.php");
                    }
                }
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                showMessage(
                    "Oops! There was an unexpected error when uploading this text.",
                    "alert-danger"
                );
            });
    }); // end #form-addtext.on.submit

    /**
     * Shows custom message in the top section of the screen
     * @param {string} html
     * @param {string} type
     */
    function showMessage(html, type) {
        $("#alert-msg")
            .html(html)
            .removeClass()
            .addClass("alert " + type);
        $(window).scrollTop(0);
    } // end showMessage

    /**
     * Makes #btn-upload-audio (button) behave like #audio-uri (input).
     * #audio-uri is the input element used for selecting audio files to upload.
     * It is hidden by default and replaced by a nicer button element (#btn-upload-audio).
     */
    $("#btn-upload-audio").on("click", function() {
        $("#audio-uri").trigger("click");
    }); // end #btn-upload-audio.on.click

    /**
     * Checks if the audio file being uploaded is bigger than the allowed limit
     * This is triggered when the user clicks the "upload" audio file button
     */
    $("#audio-uri").on("change", function() {
        var $input_audio = $(this);
        var max_file_size =
            $("#form-addtext").attr("data-premium") == "0" ? 2097152 : 10485760;
        if ($input_audio[0].files[0].size > max_file_size) {
            showMessage(
                "This file is bigger than the allowed limit (" +
                    max_file_size / 1048576 +
                    " MB). " +
                    "Notice that if f you decide to continue the text will be uploaded without an audio file.",
                "alert-danger"
            );
            $input_audio.val("");
        }
    }); // end #audio-uri.on.change

    /**
     * Checks how many characters are left for user input
     */
    $("#text").on("input", function() {
        var $textarea = $(this);
        var $span_chars_left = $("#span-chars-left");
        var chars_left = 10000 - $textarea.val().length;
        var msg_text = chars_left < 0 ? " chars over maximum" : " left";

        if (chars_left < 0) {
            $span_chars_left
                .removeClass("text-success")
                .addClass("text-danger");
            $span_chars_left.text(
                chars_left.toLocaleString() + " over maximum"
            );
        } else {
            $span_chars_left
                .removeClass("text-danger")
                .addClass("text-success");
            $span_chars_left.text(chars_left.toLocaleString() + " left");
        }
    }); // end #text.on.input

    /**
     * Checks if the text file being uploaded is bigger than the allowed limit
     * This is triggered when the user clicks the "upload" text button
     */
    $("#upload-text").on("change", function() {
        var file = $(this)[0].files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            var text = e.target.result;
            if (text.length > 10000) {
                showMessage(
                    "This file has more than 10,000 characters. Please try again with a shorter one.",
                    "alert-danger"
                );
            } else {
                $("#text").val($.trim(text));
            }
        };
        reader.readAsText(file);
    }); // end #upload-text.on.change

    /**
     * Fetches text from url using Mozilla's redability parser
     * This is triggered when user clicks the Fetch button or, externally, by bookmarklet/addons calls
     */
    function fetch_url() {
        resetControls(true);

        var url = $("#url").val();

        if (url != "") {
            $("#btn-fetch-img")
                .removeClass()
                .addClass("fas fa-sync fa-spin");
            $.ajax({
                type: "GET",
                url: "ajax/fetchurl.php",
                data: { url: url }
                //dataType: "html"
            })
                .done(function(data) {
                    if (data.error_msg != null) {
                        showMessage(data.error_msg, "alert-danger");
                    } else {
                        if (typeof data !== "undefined" && data.length != 0) {
                            var doc = document.implementation.createHTMLDocument(
                                "New Document"
                            );
                            doc.body.parentElement.innerHTML = data;
                            var article = new Readability(doc).parse();
                            $("#title").val(article.title);
                            $("#author").val(article.byline);
                            var txt = "";
                            var $tempDom = $("<output>").append(
                                $.parseHTML(article.content)
                            );
                            $("p, h1, h2, h3, h4, h5, h6", $tempDom).each(
                                function() {
                                    txt +=
                                        $(this)
                                            .text()
                                            .replace(/\s+/g, " ") + "\n\n";
                                }
                            );

                            txt = txt.replace(/(\n){3,}/g, "\n"); // remove multiple line breaks
                            txt = txt.replace(/\t/g, ""); // remove tabs
                            // txt = txt.replace(/  /g, ' '); // remove multiple spaces

                            $("#text").val($.trim(txt));
                            $("#text").trigger("input");
                        } else {
                            showMessage(
                                "Oops! There was an error trying to fetch this text.",
                                "alert-danger"
                            );
                        }
                    }
                })
                .fail(function(xhr, ajaxOptions, thrownError) {
                    showMessage(
                        "Oops! There was an error trying to fetch this text.",
                        "alert-danger"
                    );
                })
                .always(function() {
                    $("#btn-fetch-img")
                        .removeClass()
                        .addClass("fas fa-arrow-down");
                });
        }
    } // end fetch_url

    $("#btn-fetch").on("click", fetch_url);

    function resetControls(exceptSourceURI) {
        $("#alert-msg").addClass("d-none");
        $("#type").prop("selectedIndex", 0);
        $("#title").val("");
        $("#author").val("");
        $("#title").val("");
        $("#text").val("");
        $("#upload-text").val("");
        $("#audio-uri").val("");
        if (!exceptSourceURI) {
            $("#url").val("");
        }
        $("#shared-text").prop("checked", false);
    } // end resetControls

    // when page loads, check if text was imported from RSS feed
    // in that case, trigger an input event to refresh the amount of chars left
    if ($("#text").text().length > 0) {
        $("#text").trigger("input");
    }
});
