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
        fetch_text($("#url").val());
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
                } else if (form_data.get("shared-text") == "on") {
                    window.location.replace("/sharedtexts");
                } else {
                    window.location.replace("/texts");
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
     * Checks how many characters are left for user input
     */
    $("#text").on("input", function() {
        updateCharsLeft();
    }); // end #text.on.input

    /**
     * Inserts text from file in textarea
     * This is triggered when the user clicks the "upload" text button
     */
    $("#upload-text").on("change", function() {
        const file = $(this)[0].files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const text = e.target.result;
            $("#text").val(text);
            updateCharsLeft();
        };
        reader.readAsText(file);
        $(this).val(""); // reset value, allows user to select same file if necessary
    }); // end #upload-text.on.change

    /**
     * Process text pasted inside textarea
     */
    $("#text").on("paste", function(e) {
        updateCharsLeft();
    });

    /**
     * Fetch text from URL
     * This is triggered when user clicks the Fetch button
     */
    $("#btn-fetch").on("click", function(e) {
        fetch_text($("#url").val());
    });

    /**
     * Automatically fetch text from URL
     * This is triggered when user pastes a URL 
     */
    $("#url").on("paste", function(e) {
        const text = $("#text").val();
        // only auto-fetch text if textarea is empty
        if (!text || /^\s*$/.test(text)) {
            const url = e.originalEvent.clipboardData.getData('text');
            fetch_text(url);
        }
    });

    /**
     * Resets control values, i.e. empty form
     * @param {string} exceptSourceURI 
     */
    function resetControls(exceptSourceURI) {
        $("#alert-box").addClass("d-none");
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
     * Checks that the URL passed as a parameter is valid
     * @param {string} url
     */
    function isValidUrl(url)
    {
        try {
            new URL(url);
            return true;
        } catch (e) {
            return false;
        }
    }
    
    function fetch_text(url) {
        if (isWikiArticle(url)) {
            const urlObj = new URL(url);
            const pathname = urlObj.pathname;
            const title = pathname.split('/').pop(); // get the last part of the path

            fetch_wikipedia_article(title);
        } else {
            fetch_readable_text_version(url);
        }
    }

    /**
     * Fetches text from url using Mozilla's redability parser
     * This is triggered when user clicks the Fetch button or, externally, by bookmarklet/addons calls
     */
    function fetch_readable_text_version(url) {
        resetControls(true);

        if (isValidUrl(url)) {
            $("#btn-fetch-img")
                .removeClass()
                .addClass("spinner-border spinner-border-sm text-warning");
                
            $.ajax({
                type: "GET",
                url: "ajax/fetchurl.php",
                data: { url: url }
            })
                .done(function(data) {
                    if (data.error_msg != null) {
                        showMessage(data.error_msg, "alert-danger");
                    } else if (typeof data !== "undefined" && data.length != 0) {
                        const doc = document.implementation.createHTMLDocument(
                            "New Document"
                        );
                        doc.body.parentElement.innerHTML = DOMPurify.sanitize(data.file_contents);
                        const article = new Readability(doc).parse();

                        if (article == null) {
                            $("#url").val(data.url);
                            showMessage(
                                "It was not possible to extract the text from the URL you provided. " +
                                "Try doing it manually.",
                                "alert-danger"
                            );
                            return;
                        }

                        $("#title").val($("<input>").html(article.title).text());
                        $("#author").val($("<input>").html(article.byline).text());
                        $("#url").val(data.url);
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

                        txt = separateParagraphsWithTwoLineBreaks(txt);
                        txt = txt.replace(/\t/g, ""); // remove tabs

                        $("#text").val($.trim(txt));
                        updateCharsLeft();
                    } else {
                        showMessage(
                            "There was an unexpected error trying to fetch this text.",
                            "alert-danger"
                        );
                    }
                })
                .fail(function(xhr, ajaxOptions, thrownError) {
                    showMessage(
                        "There was an unexpected error trying to fetch this text.",
                        "alert-danger"
                    );
                })
                .always(function() {
                    $("#btn-fetch-img")
                        .removeClass()
                        .addClass("bi bi-arrow-down-right-square text-warning");
                });
        }
    } // end fetch_readable_text_version

    // when page loads, check if text was imported from RSS feed
    // in that case, refresh the amount of chars left
    if ($("#text").text().length > 0) {
        updateCharsLeft();
    }

    function isWikiArticle(textURI) {
        const text_lang =  document.documentElement.lang;
        const wiki_uri = 'https://' + text_lang + '.wikipedia.org/wiki/';
        return textURI.startsWith(wiki_uri);
    }

    function fetch_wikipedia_article(title) {
        resetControls(true);

        let text_lang =  document.documentElement.lang;
        const url = 'https://' + text_lang + '.wikipedia.org/w/api.php';
        const params = {
            action: 'query',
            prop: 'extracts',
            titles: title,
            format: 'json',
            explaintext: true,
            origin: '*'  // This is for CORS
        };
    
        $.ajax({
            url: url,
            method: 'GET',
            data: params
        })
        .done(function(data) {
            const page = data.query.pages[Object.keys(data.query.pages)[0]];
            let modifiedpage = removeNumbersInBrackets(page.extract);
            modifiedpage = addLineBreaksAfterDots(modifiedpage);
            modifiedpage = separateParagraphsWithTwoLineBreaks(modifiedpage)

            const readable_title = decodeURIComponent(title.replace(/_/g, ' '));    

            $("#title").val(readable_title);
            $("#text").val(modifiedpage);
            updateCharsLeft();
        })
        .fail(function(xhr, ajaxOptions, thrownError) {
            showMessage(
                "There was an unexpected error trying to fetch this Wikipedia article.",
                "alert-danger"
            );
        });
    }
    
    /**
     * Removes all numbers in brackets commonly used in Wikipedia articles to indicate references
     * @param {string} text 
     * @returns string
     */
    function removeNumbersInBrackets(text) {
        return text.replace(/\[\d+\]/g, '');
    }

    /**
     * Replace a dot followed by a word character with a dot plus two line breaks, 
     * but only if it's not followed by another dot (common in acronyms)
     * @param {string} text 
     * @returns string
     */
    function addLineBreaksAfterDots(text) {
        return text.replace(/\.(?=\p{L})(?!\p{L}\.)/gu, '.\n\n');
    }

    /**
     * Separates all paragraphs with two line breaks
     * @param {string} text 
     * @returns string
     */
    function separateParagraphsWithTwoLineBreaks(text) {
        return text.replace(/(\r\n|\r|\n)+/g, "\n\n");
    }
});
