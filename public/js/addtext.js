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
        fetch_url($("#url").val());
    }

    /**
     * Adds text to database
     * This is triggered when user presses the "Save" button & submits the form
     */
    $("#form-addtext").on("submit", function(e) {
        e.preventDefault();

        const form_data = new FormData(document.getElementById("form-addtext"));

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
                        window.location.replace("/sharedtexts");
                    } else {
                        window.location.replace("/texts");
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
     * Checks how many characters are left for user input
     */
    $("#text").on("input", function() {
        updateCharsLeft();
    }); // end #text.on.input

    /**
     * Tells user how many characters are left in message box
     */
    function updateCharsLeft() {
        const MAX_CHARS = 10000;
        const $textarea = $("#text");
        const $msg_box = $("#span-chars-left");
        const chars_left = MAX_CHARS - $textarea.val().length;

        if (chars_left < 0) {
            $msg_box
                .removeClass("text-success")
                .addClass("text-danger");
            $msg_box.html(
                chars_left.toLocaleString() + " for Text-to-Speech (TTS) Support"
            );
        } else {
            $msg_box
                .removeClass("text-danger")
                .addClass("text-success");
            $msg_box.text(chars_left.toLocaleString() + " left");
        }
    } // end updateCharsLeft()

    /**
     * Inserts text from file in textarea
     * This is triggered when the user clicks the "upload" text button
     */
    $("#upload-text").on("change", function() {
        const file = $(this)[0].files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const text = e.target.result;
            $("#text").val($.trim(text));
            updateCharsLeft();
        };
        reader.readAsText(file);
        $(this).val(""); // reset value, allows user to select same file if necessary
    }); // end #upload-text.on.change

    /**
     * Checks that the string parameter is a valid URL
     * @param {string} str 
     */
    function validateUrl(str)
    {
        const patt = new RegExp(/[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi);
        return patt.test(str);
    } 

    /**
     * Fetches text from url using Mozilla's redability parser
     * This is triggered when user clicks the Fetch button or, externally, by bookmarklet/addons calls
     */
    function fetch_url(url) {
        resetControls(true);

        if (validateUrl(url)) {
            $("#btn-fetch-img")
                .removeClass()
                .addClass("fas fa-sync fa-spin text-warning");
            $.ajax({
                type: "GET",
                url: "ajax/fetchurl.php",
                data: { url: url }
            })
                .done(function(data) {
                    if (data.error_msg != null) {
                        showMessage(data.error_msg, "alert-danger");
                    } else {
                        if (typeof data !== "undefined" && data.length != 0) {
                            const doc = document.implementation.createHTMLDocument(
                                "New Document"
                            );
                            doc.body.parentElement.innerHTML = data;
                            const article = new Readability(doc).parse();
                            $("#title").val($("<input>").html(article.title).text());
                            $("#author").val($("<input>").html(article.byline).text());
                            let txt = "";
                            let $tempDom = $("<output>").append(
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
                        .addClass("fas fa-arrow-down text-warning");
                });
        }
    } // end fetch_url

    /**
     * Fetch text from URL
     * This is triggered when user clicks the Fetch button
     */
    $("#btn-fetch").on("click", function(e) {
        fetch_url($("#url").val());
    });

    /**
     * Automatically fetch text from URL
     * This is triggered when user pastes a URL 
     */
    $("#url").on("paste", function(e) {
        const text = $("#text").val();
        // only auto-fetch text if textarea is empty
        if (!text || /^\s*$/.test(text)) {
            const pastedData = e.originalEvent.clipboardData.getData('text');
            fetch_url(pastedData);    
        }
    });

    /**
     * Resets control values, i.e. empty form
     * @param {string} exceptSourceURI 
     */
    function resetControls(exceptSourceURI) {
        $("#alert-msg").addClass("d-none");
        $("#type").prop("selectedIndex", 0);
        $("#title").val("");
        $("#author").val("");
        $("#title").val("");
        $("#text").val("");
        $("#upload-text").val("");
        if (!exceptSourceURI) {
            $("#url").val("");
        }
    } // end resetControls

    // when page loads, check if text was imported from RSS feed
    // in that case, trigger an input event to refresh the amount of chars left
    if ($("#text").text().length > 0) {
        $("#text").trigger("input");
    }
});
