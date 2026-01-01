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
    // Check for an external api call. If detected, try to fetch text using Mozilla's readability parser
    if ($("#external_call").length) {
        fetch_text($("#url").val());
    }

    /**
     * Adds text to database
     * This is triggered when user presses the "Save" button & submits the form
     */
    $("#form-addtext").on("submit", function (e) {
        e.preventDefault();

        const form_data = new FormData(document.getElementById("form-addtext"));
        const get_params = new URLSearchParams(window.location.search);
        const id = get_params.get("id");
        const url = !id ? "ajax/addtext.php" : "ajax/edittext.php";

        if (id) {
            form_data.append("id", id);
        }

        $.ajax({
            type: "POST",
            url: url,
            data: form_data,
            dataType: "json",
            contentType: false,
            processData: false
        })
            .done(function (data) {
                if (typeof data != "undefined") {
                    showMessage(data.error_msg, "alert-danger");
                } else if (form_data.get("shared-text") == "on") {
                    window.location.replace("/sharedtexts");
                } else {
                    window.location.replace("/texts");
                }
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                showMessage(
                    "Oops! There was an unexpected error processing this text.",
                    "alert-danger"
                );
            });
    }); // end #form-addtext.on.submit

    /**
     * Checks how many characters are left for user input
     */
    $("#text").on("input", function () {
        updateCharsLeft();
    }); // end #text.on.input

    /**
     * Inserts text from file in textarea
     * This is triggered when the user clicks the "upload" text button
     */
    $("#upload-text").on("change", function () {
        const file = $(this)[0].files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
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
    $("#text").on("paste", function (e) {
        updateCharsLeft();
    });

    /**
     * Fetch text from URL
     * This is triggered when user clicks the Fetch button
     */
    $("#btn-fetch").on("click", function (e) {
        fetch_text($("#url").val());
    });

    /**
     * Automatically fetch text from URL
     * This is triggered when user pastes a URL 
     */
    $("#url").on("paste", function (e) {
        const text = $("#text").val();
        // only auto-fetch text if textarea is empty
        if (!text || /^\s*$/.test(text)) {
            const url = e.originalEvent.clipboardData.getData('text');
            fetch_text(url);
        }
    });

    /**
     * Checks if audio URL is a Google Drive link
     */
    $('#audio-url').on('input', function () {
        const audio_url = $(this).val();
        const help_text = $('#audio-url-helptext');

        if (audio_url.includes('drive.google.com')) {
            help_text.html('<i class="bi bi-cloud-fill"></i> Remember to <a href="https://support.google.com/drive/answer/2494822?hl=en&co=GENIE.Platform%3DDesktop#zippy=%2Cshare-a-file-publicly" target="_blank" rel="noopener noreferrer" class="alert-link">share this file publicly</a>, allowing access to anyone with the link.');
        } else {
            help_text.text('Accepts URLs from Google Drive or any standard audio source.');
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
    function isValidUrl(url) {
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

            fetch_wikipedia_article(decodeURIComponent(title));
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

        function HTMLToPlainText(html) {
            return $("<input>").html(html).text();
        }

        if (isValidUrl(url)) {
            $("#btn-fetch-img")
                .removeClass()
                .addClass("spinner-border spinner-border-sm text-warning");

            $.ajax({
                type: "GET",
                url: "ajax/fetchurl.php",
                data: { url: url }
            })
                .done(function (data) {
                    if (data.error_msg != null) {
                        showMessage(
                            "It was not possible to extract the text from the URL you provided. " +
                            "Try doing it manually.",
                            "alert-danger"
                        );
                    } else if (typeof data !== "undefined" && data.length != 0) {
                        const text_lang = $('#text').data('text-lang');
                        if (data.lang !== '' && data.lang !== text_lang) {
                            showMessage("It looks like you might be adding a text in a different language " +
                                "than the one currently active in Aprelendo. Please double-check to ensure it " +
                                "matches your selected language before proceeding. This helps keep your learning " +
                                "experience focused and organized!", "alert-warning");
                        }

                        const doc = document.implementation.createHTMLDocument(
                            "New Document"
                        );

                        doc.documentElement.innerHTML = data.file_contents;

                        // Parse with Readability
                        const reader = new Readability(doc, { keepClasses: false });
                        const article = reader.parse();

                        if (article == null) {
                            $("#url").val(data.url);
                            showMessage(
                                "It was not possible to extract the text from the URL you provided. " +
                                "Try doing it manually.",
                                "alert-danger"
                            );
                            return;
                        }

                        $("#title").val(HTMLToPlainText(article.title));
                        $("#author").val(HTMLToPlainText(article.byline));
                        $("#url").val(decodeURIComponent(data.url));

                        // Now sanitise the extracted article HTML before deriving plain text
                        const clean_HTML = DOMPurify.sanitize(article.content || "", {
                            USE_PROFILES: { html: true },
                            FORBID_TAGS: ['figure', 'figcaption', 'img', 'picture', 'video', 'audio', 'iframe', 'form', 'button', 'input'],
                            KEEP_CONTENT: false
                        });

                        // Derive plain text from headings/paragraphs
                        const $tmp_DOM = $("<output>").append($.parseHTML(clean_HTML, document, false));
                        let txt = $("p, h1, h2, h3, h4, h5, h6, li, blockquote", $tmp_DOM)
                            .map(function () {
                                return $(this).text().replace(/\s+/g, " ").trim();
                            })
                            .get()
                            .filter(Boolean)
                            .join("\n\n");

                        txt = normalizeParagraphBreaks(txt).replace(/\t/g, "");

                        $("#text").val($.trim(txt));
                        updateCharsLeft();
                    } else {
                        showMessage(
                            "There was an unexpected error trying to fetch this text.",
                            "alert-danger"
                        );
                    }
                })
                .fail(function (xhr, ajaxOptions, thrownError) {
                    showMessage(
                        "There was an unexpected error trying to fetch this text.",
                        "alert-danger"
                    );
                })
                .always(function () {
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
        const text_lang = $('#text').data('text-lang');
        const standard_uri = `https://${text_lang}.wikipedia.org/wiki/`;
        const mobile_uri = `https://${text_lang}.m.wikipedia.org/wiki/`;

        return textURI.startsWith(standard_uri) || textURI.startsWith(mobile_uri);
    }

    function fetch_wikipedia_article(title) {
        resetControls(true);

        let text_lang = $('#text').data('text-lang');
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
            .done(function (data) {
                const page = data.query.pages[Object.keys(data.query.pages)[0]];
                let raw_text = page.extract;

                let clean_text = stripTrailingSections(raw_text, text_lang);
                clean_text = removeNumbersInBrackets(clean_text);
                // clean_text = addLineBreaksAfterDots(clean_text);
                clean_text = normalizeParagraphBreaks(clean_text);
                clean_text = cleanWikipediaTitles(clean_text);

                const readable_title = decodeURIComponent(title.replace(/_/g, ' '));

                $("#title").val(readable_title);
                $("#text").val(clean_text);
                updateCharsLeft();
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
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
     * Cleans Wikipedia-style headings by removing all '=' characters
     * and any surrounding whitespace.
     * @param {string} text - Raw text that may contain Wikipedia heading markers
     * @returns {string} Cleaned text without '=' markers or extra whitespace
     */
    function cleanWikipediaTitles(text) {
        return text.replace(/[ \t]*=+[ \t]*/g, '');
    }

    /**
     * Separates all paragraphs with two line breaks.
     * Recognizes paragraphs separated by multiple newlines, even if those 
     * lines contain spaces or tabs.
     * @param {string} text 
     * @returns string
     */
    function normalizeParagraphBreaks(text) {
        // This regex matches:
        // 1. A newline (\r\n|\r|\n)
        // 2. Followed by any combination of newlines AND horizontal whitespace (\s matches both, 
        // but we specifically target vertical/horizontal mix)
        // The [ \t\r\n]+ ensures that if there is a "blank" line with just spaces, it is treated as part of the break.
        return text.replace(/(\r\n|\r|\n)([ \t\r\n]*)/g, "\n\n");
    }

    /**
     * Truncates the text at the first occurrence of a "junk" section header
     * based on the current language.
     */
    function stripTrailingSections(text, lang) {
        const sectionTerms = {
            'ar': ['انظر أيضا', 'مراجع', 'وصلات خارجية'],
            'bg': ['Вижте също', 'Източници', 'Външни препратки'],
            'ca': ['Vegeu també', 'Referències', 'Enllaços externs'],
            'cs': ['Viz též', 'Reference', 'Externí odkazy'],
            'da': ['Se også', 'Referencer', 'Eksterne henvisninger'],
            'de': ['Siehe auch', 'Einzelnachweise', 'Weblinks', 'Literatur'],
            'el': ['Δείτε επίσης', 'Παραπομπές', 'Εξωτερικοί σύνδεσμοι'],
            'en': ['See also', 'References', 'Further reading', 'External links'],
            'es': ['Véase también', 'Referencias', 'Bibliografía', 'Enlaces externos'],
            'fr': ['Voir aussi', 'Notes et références', 'Liens externes'],
            'he': ['ראו גם', 'הערות שוליים', 'קישורים חיצוניים'],
            'hi': ['इन्हें भी देखें', 'सन्दर्भ', 'बाहरी कड़ियाँ'],
            'hr': ['Vidi još', 'Izvori', 'Vanjske poveznice'],
            'hu': ['Lásd még', 'Jegyzetek', 'Források', 'További információk'],
            'it': ['Voci correlate', 'Note', 'Bibliografia', 'Collegamenti esterni'],
            'ja': ['関連項目', '脚注', '参考文献', '外部リンク'],
            'ko': ['같이 보기', '각주', '참고 문헌', '외부 링크'],
            'nl': ['Zie ook', 'Bronnen', 'Externe links'],
            'no': ['Se også', 'Referanser', 'Eksterne lenker'],
            'pl': ['Zobacz też', 'Przypisy', 'Bibliografia', 'Linki zewnętrzne'],
            'pt': ['Ver também', 'Referências', 'Ligações externas'],
            'ro': ['Vezi și', 'Note', 'Referințe', 'Legături externe'],
            'ru': ['См. также', 'Примечания', 'Литература', 'Ссылки'],
            'sk': ['Pozri aj', 'Referencie', 'Externé odkazy'],
            'sl': ['Glej tudi', 'Sklici', 'Viri', 'Zunanje povezave'],
            'sv': ['Se även', 'Referenser', 'Externa länkar'],
            'tr': ['Ayrıca bakınız', 'Kaynakça', 'Dış bağlantılar'],
            'vi': ['Xem thêm', 'Tham khảo', 'Liên kết ngoài'],
            'zh': ['参见', '参考文献', '外部链接']
        };

        const terms = sectionTerms[lang] || sectionTerms['en'];

        // Create a regex that looks for:
        // 1. Optional newlines
        // 2. Exactly two or more "=" signs
        // 3. Optional whitespace
        // 4. Any of our language-specific terms
        // 5. Optional whitespace and trailing "=" signs
        const pattern = new RegExp(`(\\n+|^)\\s*={2,}\\s*(${terms.join('|')})\\s*={2,}`, 'i');

        const match = text.search(pattern);
        if (match !== -1) {
            return text.substring(0, match);
        }
        return text;
    }
});
