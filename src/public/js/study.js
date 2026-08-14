// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function () {

    let $selword = $();             // jQuery object used to open dictionary modal
    const answers = [
        ["0", 0, "bg-success", "Excellent"],
        ["1", 0, "bg-warning", "Partial"],
        ["2", 0, "bg-primary", "Fuzzy"],
        ["3", 0, "bg-danger", "No recall"],
        ["4", 0, "text-warning bg-dark", "No example sentence found!"],
    ];
    let session;

    Dictionaries.fetchURIs(); // get dictionary & translator URIs

    /**
     * Shows the loading placeholder state in the study card header.
     */
    function showStudyCardHeaderLoading() {
        $("#study-card-word-title")
            .addClass("placeholder w-50 rounded")
            .html("&nbsp;");

        $("#study-card-freq-badge")
            .removeClass("d-none border border-light")
            .addClass("placeholder w-25")
            .html("&nbsp;");
    }

    /**
     * Shows the requested answer-card page.
     * @param {number} page_number - Page number to display
     */
    function showAnswerCardPage(page_number) {
        $("#answer-card-page-1").toggleClass("d-none", page_number !== 1);
        $("#answer-card-page-2").toggleClass("d-none", page_number !== 2);
    }

    /**
     * Shows the placeholder version of the answer card while examples are loading.
     */
    function showAnswerCardLoading() {
        $("#answer-card-placeholder").removeClass("d-none");
        $("#answer-card-page-1").addClass("d-none");
        session.setAnswerButtonsDisabled(true);
    }

    /**
     * Shows the real answer card once the current study item is ready.
     */
    function showAnswerCard() {
        $("#answer-card-placeholder").addClass("d-none");
        $("#answer-card-page-1").removeClass("d-none");
        session.setAnswerButtonsDisabled(false);
    }

    /**
     * Renders the example sentences for the current card.
     * @param {Array} examples_array - Examples to render
     * @param {string} word - Word being studied
     */
    function renderExamples(examples_array, word) {
        let examples_html = '';

        examples_array.forEach(example => {
            examples_html += buildExampleHTML(example, word);
        });

        $("#study-card-examples").html(examples_html);
    }

    /**
     * Hides the controls that only apply while a study card is active.
     */
    function hideStudyControls() {
        $("#examples-placeholder").addClass("d-none");
        $("#live-progress").addClass("d-none");
    }

    /**
     * Gets the current study language used for locale-sensitive string operations.
     * @returns {(string|undefined)} Study language ISO code, or undefined if unavailable
     */
    function getStudyLanguage() {
        const study_lang = $("#study-card").data("lang");

        if (typeof study_lang !== "string" || study_lang.trim() === "") {
            return undefined;
        }

        return study_lang;
    }

    /**
     * Fetches examples sentences for a specific word
     * @param {string} word
     */
    async function getExampleSentencesforCard(word) {
        // if deck is empty or last card is reached, exit
        showAnswerCardPage(1);
        showAnswerCardLoading();
        showStudyCardHeaderLoading();

        // empty card and show spinner
        $("#examples-placeholder").removeClass('d-none');
        $("#study-card-examples").empty();

        try {
            const form_data = new URLSearchParams({ word: word });

            const response = await fetch("/ajax/getcards.php", {
                method: "POST",
                body: form_data,
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
            
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to fetch example sentences.');
            }

            let examples_array = [];
            const lang_iso = getStudyLanguage();

            const word_boundary = '(?<![\\p{L}])' + escapeRegex(word) + '(?![\\p{L}])';
            const sentence_start = '([^\\n.?!]|[\\d][.][\\d]|[A-Z][.](?:[A-Z][.])+)*';
            const sentence_end = "([^\\n.?!]|[.][\\d]|[.](?:[A-Z][.])+)*[\\n.?!][\"'”’»\\)\\]]*";
            let sentence_regex = new RegExp(sentence_start + word_boundary + sentence_end, 'gmiu');

            // different sentence separator for Japanese and Chinese, as
            // they don't separate words and finish sentences with 。
            if (lang_iso == "ja" || lang_iso == "zh") {
                sentence_regex = new RegExp(
                    '[^\n?!。]*' + escapeRegex(word) + '[^\n?!。]*[\n?!。]',
                    'gmiu'
                );
            }
            
            const texts = Array.isArray(data.payload) ? data.payload : (data.payload ? [data.payload] : []);

            texts.forEach(text => {
                // extract example sentences from text
                let m;
                while ((m = sentence_regex.exec(text.text)) !== null) {
                    // This is necessary to avoid infinite loops with zero-width matches
                    if (m.index === sentence_regex.lastIndex) {
                        sentence_regex.lastIndex++;
                    }

                    if (examples_array.length < 3) {
                        // create html for each example sentence, max 3 examples
                        const match = normalizeExtractedSentence(m[0]);

                        // check that match is not the only word in current example sentence
                        if (match !== word) {
                            // make sure example sentence is unique, then add to the list
                            const example_text = doubleQuotesNotClosed(match) ? text.text : match;
                            examples_array = forceUnique(examples_array, { ...text, text: example_text });
                        }
                    }
                }
            });

            // update card
            $("#study-card").data('word', word);
            session.updateLiveProgressBar();
            $("#card-counter").text((session.getCurrentCardIndex() + 1) + "/" + session.getMaxCards());
            $("#study-card-word-title")
                .removeClass("placeholder w-50 rounded")
                .text(word);
            
            // if example sentence is empty, go to next card, else update example sentences
            if (examples_array.length === 0) {
                if (session.recordAnswer(4)) {
                    session.showCompleteState();
                    return;
                }
                await getExampleSentencesforCard(session.getCurrentCard().word);
            } else {
                examples_array = shuffleExamples(examples_array);

                // only look for word frequency if word has example sentences
                showWordFrequency(session.getCurrentCard().is_phrase);

                $("#examples-placeholder").addClass('d-none');
                renderExamples(examples_array, word);
                showAnswerCard();
            }

        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
    } 

    /**
     * 
     * @param {array} example_list Array including example objects
     * @param {object} new_example Example object
     * @returns array
     */
    function forceUnique(example_list, new_example) {
        if (example_list.length === 0) {
            example_list.push(new_example);
            return example_list;
        }

        for (let i = 0; i < example_list.length; i++) {
            const existing_example = example_list[i];
            if (new_example.text.includes(existing_example.text)) {
                example_list[i] = new_example;
                return example_list;
            } else if (existing_example.text.includes(new_example.text)) {
                return example_list; // no need to proceed, as a similar example already exists
            }
        }

        example_list.push(new_example);
        return example_list;
    }

    /**
     * Builds the example sentence HTML, including formatting and its source information
     * @param {object} text Text object that includes the following properties: author, title, text & source_uri
     * @param {string} word Word being studied
     * @returns string
     */
    function buildExampleHTML(text, word) {
        const word_regex = new RegExp('(?<![\\p{L}|\\d])' + escapeRegex(word) + '(?![\\p{L}|\\d])', 'gmiu');
        let example_html = '';
        let example_text_html = '';

        // make the word user is studying clickable
        example_text_html = text.text.replace(word_regex, function (match) {
            return "<a class='word fw-bold bg-warning-subtle border-bottom p-1'>" + StudySession.encodeHtml(match.replace(/\s\s+/g, ' ')) + "</a>";
        });

        example_html = `<blockquote cite='${text.source_uri}'>`;
        example_html += `<p class='mb-0'>${example_text_html}</p>`;
        example_html += `<cite style='font-size:.85rem' class='text-secondary fw-medium'>${text.author == "" ? "Anonymous" : text.author}`;
        if (!text.source_uri || text.source_uri.endsWith(".epub")) {
            example_html += ", " + text.title;
        } else {
            example_html += `, <a href='${text.source_uri}' target='_blank'>${text.title}</a>`
        }
        example_html += "</cite></blockquote>"

        return example_html;
    }

    /**
     * Using the traditional sentence delimiters (.?!) does not work if they are used inside quotes, 
     * like in this example: '"This is not the way!", he screamed'. If the word being studied is before '!'
     * you would get '"This is not the way!'; and if it's after, '", he screamed.'. Either way, it's incomplete
     * and we get only one (or an uneven nr. of) quote symbol(s). 
     * This function is used to detect these cases, discard them and use the whole paragraph (which contains the
     * example sentece), returned by getcards.php.
     * @param {string} text Example sentence extracted from example paragraph returned by getcards.php
     * @returns boolean
     */
    function doubleQuotesNotClosed(text) {
        // Count the number of double quotes
        const doubleQuotesCount = (text.match(/"/g) || []).length;

        // Count the number of opening and closing curly double quotes
        const openingCurlyQuotesCount = (text.match(/“/g) || []).length;
        const closingCurlyQuotesCount = (text.match(/”/g) || []).length;

        // If nr. of double quotes is uneven or opening and closing curly quotes don't match, return true
        return (doubleQuotesCount % 2 !== 0 || openingCurlyQuotesCount !== closingCurlyQuotesCount);
    }

    /**
     * Normalizes an extracted sentence by trimming whitespace and removing stray closing punctuation
     * that belongs to the previous sentence, such as a leading curly quote in `.” Next sentence`.
     * @param {string} text Example sentence extracted from example paragraph returned by getcards.php
     * @returns {string}
     */
    function normalizeExtractedSentence(text) {
        return text
            .trim()
            .replace(/^[”’»\)\]\}]+\s*/u, '')
            .trim();
    }

    /**
     * 
     * @param {array} examples array
     * @returns array
     */
    function shuffleExamples(examples) {
        let current_index = examples.length, random_index;

        // While there remain elements to shuffle
        while (current_index > 0) {
            // Pick a remaining element
            random_index = Math.floor(Math.random() * current_index);
            current_index--;

            // And swap it with the current element
            [examples[current_index], examples[random_index]] = [examples[random_index], examples[current_index]];
        }
        return examples;
    }

    /**
     * Open dictionary modal
     * Triggers when user clicks word
     * @param {event object} e
     */
    $("body").on("click", ".word", function (e) {
        $selword = $(this);
        StudyActionBtns.show($selword);
    }); 

    /**
     * Triggers when user clicks on answer buttons
     * @param {event object} e
     */
    $(".btn-answer").on("click", async function (e) {
        e.preventDefault();
        const answer = $(this).attr("value");

        if (typeof(answer) === 'undefined') { return; }

        await session.saveAnswer(answer);
    }); 

    /**
     * Triggers when user clicks on "Back to previous question" answer button
     * @param {event object} e
    */
    $('#btn-answer-prev').on('click', function(e) {
        e.preventDefault();
        showAnswerCardPage(1);
    });

    /**
     * Triggers when user clicks on "Answer more" button
     * This button is used to show the second page of the answer card, 
     * which contains more answer options.
     * @param {event object} e
     */
    $('#btn-answer-more').on('click', function(e) {
        e.preventDefault();
        showAnswerCardPage(2);
    });

    /**
     * Updates the frequency badge on the study card to display the frequency level of the current word.
     */
    function showWordFrequency(is_phrase) {
        const $freq_badge = $("#study-card-freq-badge");

        if (is_phrase) {
            $freq_badge
                .removeClass('placeholder w-25')
                .addClass('border border-light')
                .text('Phrase/Expression');
        } else {
            const freq_level = Dictionaries.getWordFrequency(session.getCurrentCard().frequency_index) + ' frequency';
            $freq_badge
                .removeClass('placeholder w-25')
                .addClass('border border-light')
                .text(freq_level);
        }
    } 

    /**
     * Opens translator in new window. 
     * Triggers when user click in translate button in modal window
     */
    $("#btn-translate").on("click", function () {
        const base_uris = Dictionaries.getURIs();
        openInNewTab(LinkBuilder.forTranslationInStudy(base_uris.translator, $selword));
    }); 

    $("#btn-img-dic").on("click", function () {
        const base_uris = Dictionaries.getURIs();
        openInNewTab(LinkBuilder.forWordInDictionary(base_uris.img_dictionary, $selword.text()));
    }); 

    /**
     * Disables right click context menu
     */
    $(document).on("contextmenu", function (e) {
        // opens dictionary translator in case user right clicked on a word/phrase
        // but only on desktop browsers

        if (!isMobileDevice() && $(e.target).is(".word")) {
            const base_uris = Dictionaries.getURIs();
            openInNewTab(LinkBuilder.forTranslationInStudy(base_uris.translator, $(e.target)));
        }
        return false;
    }); 

    /**
     * Implements shortcuts for buttons
     * @param {event object} e
     */
    $(document).on( "keypress", function (e) {
        // only allow shortcuts if buttons are enabled
        if (!$(".btn-answer").prop('disabled')) {
            switch (e.which) {
                case 49: // 49 is the keycode for "1" key
                    $("#btn-answer-no-recall").trigger("click");
                    break;
                case 50: // 50 is the keycode for "2" key
                    $("#btn-answer-fuzzy").trigger("click");
                    break;
                case 51: // 51 is the keycode for "3" key
                    $("#btn-answer-partial").trigger("click");
                    break;
                case 52: // 52 is the keycode for "4" key
                    $("#btn-answer-excellent").trigger("click");
                    break;
                default:
                    break;
            }
        }
    }); 

    /**
     * Escapes special regex characters in a string.
     * @param {string} s - String to escape
     * @returns {string} Escaped string safe for regex
     */
    function escapeRegex(s) {
        return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    /**
     * Removes selection when user clicks in white-space
     */
    $(document).on("mouseup touchend", function (e) {
        if ($(e.target).is(".word") === false && !$(e.target).closest('#action-buttons').length > 0) {
            e.stopPropagation();
            ActionBtns.hide();
        }
    }); 

    session = StudySession.create({
        answers,
        on_card_ready: async (word) => {
            session.adaptCardStyleToWordStatus(word.status);
            await getExampleSentencesforCard(word.word);
        },
        on_empty: () => {
            $("#study-card-examples").html(
                `<div class='bi bi-exclamation-circle text-danger display-3'></div>
                <div class='mt-3'>It seems there are no cards in your deck.
                Add some words to your library and try again.</div>`
            );
            hideStudyControls();
        },
        on_complete: () => {
            $("#study-card-examples").html(`
                <img src="/img/gamification/finished.gif" style="max-width: 300px;" alt="Finished!">
                <div class="mt-3">You have reached the end of your study.</div>
            `);
            hideStudyControls();
        }
    });
    session.load();
});
