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

    let $selword = $();             // jQuery object used to open dictionary modal
    let words = [];                 // array containing all words user is learning
    let max_cards = 10;             // maximum nr. of cards
    let cur_card_index = 0;         // current card/word index

    // cache of current examples (so we can re-render on reveal without changing order)
    let current_examples_array = [];

    // nr. of words recalled during practice
    let answers = [
        ["0", 0, "bg-success", "Excellent"],
        ["1", 0, "bg-warning", "Partial"],
        ["2", 0, "bg-primary", "Fuzzy"],
        ["3", 0, "bg-danger", "No recall"],
        ["4", 0, "text-warning bg-dark", "No example sentence found!"],
    ];

    // initial UI state for the scramble game
    $(".btn-answer").prop('disabled', true);
    $("#answer-card").addClass("d-none"); // hide answer buttons until reveal
    Dictionaries.fetchURIs();
    ensureGuessUI(); // inject guess UI into existing HTML
    getListofCards();

    /**
     * Injects the guess UI (input field and buttons) into the card body if not already present.
     */
    function ensureGuessUI() {
        if ($("#guess-ui").length) return;

        const guessUI = `
            <div id="guess-ui" class="mb-3" style="max-width: 550px; margin: 0 auto;">
                <div class="input-group">
                    <input id="guess-input" type="text" class="form-control"
                           placeholder="Type the correct word…" aria-label="Your guess">
                    <button id="guess-submit" class="btn btn-outline-primary" type="button">Check</button>
                    <button id="guess-reveal" class="btn btn-secondary" type="button">Show answer</button>
                </div>
                <div id="guess-feedback" class="form-text mt-2 text-secondary">
                    The word above is shuffled; first letter is a hint.
                </div>
            </div>
        `;
        $("#study-card-body").prepend(guessUI);

        // events
        $("#guess-submit").on("click", onGuessSubmit);
        $("#guess-reveal").on("click", revealAnswer);
        $("#guess-input").on("keypress", function (e) {
            if (e.which === 13) {
                e.preventDefault();
                onGuessSubmit();
            }
        });
    }

    /**
     * Fetches the list of words to study from the server.
     */
    function getListofCards() {
        $.ajax({
            type: "POST",
            url: "ajax/getcards.php",
            data: { limit: max_cards },
            dataType: "json"
        })
            .done(function (data) {
                if (data && data.error_msg) {
                    showMessage(data.error_msg, "alert-danger");
                    return;
                }

                if (data.length == 0) {
                    showNoMoreCardsMsg();
                    return true;
                }

                words = data.map(item => {
                    return {
                        ...item,
                        word: item.word.replace(/\r?\n|\r/g, " ")
                    };
                });

                max_cards = words.length > max_cards ? max_cards : words.length;

                $("#card-counter").text("1" + "/" + max_cards);
                adaptCardStyleToWordStatus(words[0].status);
                startCard(words[0].word);
            })
            .fail(function () {
                showMessage("Oops! There was an unexpected error trying to fetch study cards for this language.",
                        "alert-danger");
            });
    }

    /**
     * Initializes a new card with scrambled word display and fetches example sentences.
     * @param {string} original_word - The word to study
     */
    function startCard(original_word) {
        // reset UI to scramble mode
        $("#answer-card").addClass("d-none");
        $(".btn-answer").prop('disabled', true);
        $("#guess-ui").removeClass("d-none");
        $("#guess-input").val("").focus();
        $("#guess-feedback")
            .removeClass("text-danger text-success")
            .addClass("text-secondary")
            .text("The word above is shuffled; first letter is a hint.");

        // compute and set scrambled display (first letter preserved)
        const scrambled = shuffleKeepFirst(original_word);
        $("#study-card").data('word', original_word);
        $("#study-card").data('scrambled', scrambled);
        $("#study-card").data('revealed', false);

        // update title to scrambled
        $("#study-card-word-title").removeClass('placeholder').text(scrambled);

        // fetch examples for this card
        getExampleSentencesforCard(original_word, scrambled);
    }

    /**
     * Fetches and displays example sentences for the current word in scrambled mode.
     * @param {string} original_word - The word to find in example sentences
     * @param {string} scrambled_word - The scrambled version to display
     */
    function getExampleSentencesforCard(original_word, scrambled_word) {
        if (lastCardReached()) return;

        $("#examples-placeholder").removeClass('d-none');
        $("#study-card-examples").empty();

        $.ajax({
            type: "POST",
            url: "ajax/getcards.php",
            data: { word: original_word },
            dataType: "json"
        })
            .done(function (data) {
                if (data && data.error_msg) {
                    showMessage(data.error_msg, "alert-danger");
                    return;
                }

                let examples_array = [];
                let examples_html = '';
                const lang_iso = $("#card").data("lang");

                const word_boundary = '(?<![\\p{L}])' + escapeRegex(original_word) + '(?![\\p{L}])';
                const sentence_start = '([^\\n.?!]|[\\d][.][\\d]|[A-Z][.](?:[A-Z][.])+)*';
                const sentence_end = '([^\\n.?!]|[.][\\d]|[.](?:[A-Z][.])+)*[\\n.?!]';
                let sentence_regex = new RegExp(sentence_start + word_boundary + sentence_end, 'gmiu');

                if (lang_iso == "ja" || lang_iso == "zh") {
                    sentence_regex = new RegExp('[^\\n?!。]*' + escapeRegex(original_word) + '[^\\n?!。]*[\\n?!。]', 'gmiu');
                }

                const texts = Array.isArray(data) ? data : (data ? [data] : []);

                texts.forEach(text => {
                    let m;
                    while ((m = sentence_regex.exec(text.text)) !== null) {
                        if (m.index === sentence_regex.lastIndex) sentence_regex.lastIndex++;
                        if (examples_array.length < 3) {
                            let match = m[0];
                            if (match !== original_word) {
                                text.text = doubleQuotesNotClosed(match) ? text.text : match;
                                examples_array = forceUnique(examples_array, text);
                            }
                        }
                    }
                });

                $("#study-card").data('word', original_word);
                updateLiveProgressBar();
                $("#card-counter").text((cur_card_index + 1) + "/" + max_cards);

                // shuffle once and store that order for both scrambled and revealed display
                examples_array = shuffleExamples(examples_array);
                current_examples_array = examples_array;

                if (examples_array.length === 0) {
                    words[cur_card_index].status = 4;
                    answers[4][1] = answers[4][1] + 1;
                    cur_card_index++;
                    if (lastCardReached()) return;
                    startCard(words[cur_card_index].word);
                } else {
                    examples_array.forEach(example => {
                        examples_html += buildExampleHTML(example, original_word, scrambled_word, true);
                    });

                    showWordFrequency(words[cur_card_index].is_phrase);

                    $("#examples-placeholder").addClass('d-none');
                    $("#study-card-examples").html(examples_html);
                }
            })
            .fail(function () {
                showMessage("Oops! There was an unexpected error trying to fetch example sentences for this word.",
                        "alert-danger");
            });
    }

    /**
     * Ensures only unique examples are kept, preferring longer versions.
     * @param {Array} example_list - Current list of examples
     * @param {Object} new_example - New example to add
     * @returns {Array} Updated example list
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
                return example_list;
            }
        }
        example_list.push(new_example);
        return example_list;
    }

    /**
     * Builds the HTML for a single example sentence with highlighted word.
     * @param {Object} text - Example object with text, source_uri, author, title
     * @param {string} original_word - The original word
     * @param {string} display_word - The word to display (scrambled or original)
     * @param {boolean} scrambled_display - If true, show scrambled version without "word" class
     * @returns {string} HTML string for the example
     */
    function buildExampleHTML(text, original_word, display_word, scrambled_display) {
        const word_regex = new RegExp('(?<![\\p{L}|\\d])' + escapeRegex(original_word) + '(?![\\p{L}|\\d])', 'gmiu');
        let example_html = '';
        let example_text_html = '';

        example_text_html = text.text.replace(word_regex, function () {
            const shown = scrambled_display ? display_word : original_word;
            const is_unscrambled = !scrambled_display;

            const base_classes = 'fw-bold bg-warning-subtle border-bottom p-1';
            const anchor_class = (is_unscrambled ? 'word ' : '') + base_classes; // "word" only when revealed
            const hint_title = scrambled_display ? "title='scrambled – guess the original word'" : "";

            return `<a class='${anchor_class}' ${hint_title}>${encodeHtml(shown)}</a>`;
        });

        example_html = "<blockquote cite='" + text.source_uri + "'>";
        example_html += "<p class='mb-0'>" + example_text_html + "</p>";
        example_html += `<cite style='font-size:.85rem' class='text-secondary fw-medium'>${text.author == "" ? "Anonymous" : text.author}`;
        if (text.source_uri == '' || text.source_uri.endsWith(".epub")) {
            example_html += ", " + text.title;
        } else {
            example_html += ", <a href='" + text.source_uri + "' target='_blank'>" + text.title + "</a>";
        }
        example_html += "</cite></blockquote>";

        return example_html;
    }

    /**
     * Checks if double quotes in text are properly closed.
     * @param {string} text - Text to check
     * @returns {boolean} True if quotes are not properly closed
     */
    function doubleQuotesNotClosed(text) {
        const double_quotes_count = (text.match(/"/g) || []).length;
        const opening_curly_quotes_count = (text.match(/"/g) || []).length;
        const closing_curly_quotes_count = (text.match(/"/g) || []).length;
        return (double_quotes_count % 2 !== 0 || opening_curly_quotes_count !== closing_curly_quotes_count);
    }

    /**
     * Randomizes the order of examples using Fisher-Yates shuffle.
     * @param {Array} examples - Array of example objects
     * @returns {Array} Shuffled array
     */
    function shuffleExamples(examples) {
        let current_index = examples.length, random_index;
        while (current_index > 0) {
            random_index = Math.floor(Math.random() * current_index);
            current_index--;
            [examples[current_index], examples[random_index]] = [examples[random_index], examples[current_index]];
        }
        return examples;
    }

    /**
     * Checks if all cards have been studied and displays appropriate end screen.
     * @returns {boolean} True if last card was reached
     */
    function lastCardReached() {
        if (max_cards == 0) {
            showNoMoreCardsMsg();
            return true;
        } else if (cur_card_index >= max_cards) {
            $("#study-card-word-title").text("Congratulations!");
            $("#study-card-freq-badge").hide();
            adaptCardStyleToWordStatus();

            let progress_html = "";
            for (const answer of answers) {
                let subtotal = answer[1];
                let percentage = subtotal / max_cards * 100;
                let bg_class = answer[2];
                let title = answer[3];

                progress_html += "<div class='progress-bar " + bg_class + "' role='progressbar' aria-valuenow='" +
                    percentage + "' aria-valuemin='0' aria-valuemax='100' style='width: " + percentage +
                    "%' title='" + title + ": " + subtotal + " answer(s)'>" + Math.round(percentage) + " %</div>";
            }

            $("#study-card-examples").html("<div class='bi bi-trophy text-warning display-3 mt-3'></div>"
                + "<div class='mt-3'>You have reached the end of your study.</div>"
                + "<div class='mt-3'>These were your results:</div>"
                + "<div class='progress mx-auto mt-3 fw-bold' style='height: 25px;max-width: 550px'>" + progress_html + "</div>"
                + buildResultsTable()
                + "<div class='small mt-4'>If you want to continue, you can refresh this page (F5).<br>However, we strongly recommend that you keep your study sessions short and take rest intervals.</div>");
            $("#study-card-footer").addClass("d-none");
            $("#examples-placeholder").addClass("d-none");
            $("#live-progress").addClass("d-none");
            $("#guess-ui").addClass("d-none");
            scrollToPageTop();
            return true;
        }
        return false;
    }

    /**
     * Displays message when there are no cards available to study.
     */
    function showNoMoreCardsMsg() {
        $("#study-card-header").text("Sorry, no cards to practice");
        adaptCardStyleToWordStatus(3);
        $("#study-card-examples").html("<div class='bi bi-exclamation-circle text-danger display-3'></div><div class='mt-3'>It seems there are no cards in your deck. Add some words to your library and try again.</div>");
        $("#study-card-footer").addClass("d-none");
        $("#examples-placeholder").addClass("d-none");
        $("#live-progress").addClass("d-none");
        $("#guess-ui").addClass("d-none");
    }

    /**
     * Updates the progress bar showing study session completion.
     */
    function updateLiveProgressBar() {
        const percentage = Math.round((cur_card_index + 1) / max_cards * 100);
        $("#live-progress-bar").css("width", percentage + "%").attr("aria-valuenow", percentage);
    }

    /**
     * Updates card styling based on word status (recall level).
     * @param {number} status - Status code (0=excellent, 1=partial, 2=fuzzy, 3=no recall)
     */
    function adaptCardStyleToWordStatus(status) {
        const $card = $("#study-card");
        const $card_header = $("#study-card-header");

        $card.removeClass(function (index, className) {
            return (className.match(/\bborder-\S+/g) || []).join(' ');
        });
        $card_header.removeClass(function (index, className) {
            return (className.match(/\bborder-\S+|\bbg-\S+/g) || []).join(' ');
        });

        switch (status) {
            case 0:
                $card.addClass('border-success');
                $card_header.addClass('bg-gradient bg-success border-success');
                break;
            case 1:
                $card.addClass('border-warning');
                $card_header.addClass('bg-gradient bg-warning border-warning');
                break;
            case 2:
                $card.addClass('border-primary');
                $card_header.addClass('bg-gradient bg-primary border-primary');
                break;
            case 3:
                $card.addClass('border-danger');
                $card_header.addClass('bg-gradient bg-danger border-danger');
                break;
            default:
                $card.addClass('border-secondary');
                $card_header.addClass('bg-gradient bg-secondary border-secondary');
                break;
        }
    }

    /**
     * Event: Triggered when clicking on a revealed word (has "word" class).
     * Opens the dictionary modal for the selected word.
     */
    $("body").on("click", ".word", function () {
        $selword = $(this);
        StudyActionBtns.show($selword);
    });

    /**
     * Event: Triggered when clicking any answer button (1-4).
     * Saves the user's recall level and loads the next card.
     */
    $(".btn-answer").click(function (e) {
        e.preventDefault();
        const word = $("#study-card").data('word');
        const answer = $(this).attr("value");

        answers[answer][1] = answers[answer][1] + 1;
        words[cur_card_index].status = answer;

        $(".btn-answer").prop('disabled', true);

        $.ajax({
            type: "POST",
            url: "ajax/updatecard.php",
            data: { word: word, answer: answer }
        })
            .done(function () {
                cur_card_index++;
                if (lastCardReached()) return;
                adaptCardStyleToWordStatus(words[cur_card_index].status);
                scrollToPageTop();
                startCard(words[cur_card_index].word);
            })
            .fail(function () {
                showMessage("There was an unexpected error updating this word's status", "alert-danger");
            });

        $('#btn-answer-prev').trigger('click'); // hide answer card page 2
    });

    /**
     * Event: Triggered when clicking "Back" button on answer card page 2.
     * Returns to page 1 of the answer card.
     */
    $('#btn-answer-prev').on('click', function (e) {
        e.preventDefault();
        $('#answer-card-page-1').removeClass('d-none');
        $('#answer-card-page-2').addClass('d-none');
    });

    /**
     * Event: Triggered when clicking "More" button on answer card page 1.
     * Shows page 2 of the answer card with additional options.
     */
    $('#btn-answer-more').on('click', function (e) {
        e.preventDefault();
        $('#answer-card-page-1').addClass('d-none');
        $('#answer-card-page-2').removeClass('d-none');
    });

    /**
     * Builds the HTML table showing all studied words and their recall levels.
     * @returns {string} HTML table string
     */
    function buildResultsTable() {
        const table_header =
            `<table class="table table-bordered table-striped text-end small mx-auto mt-3" style="max-width: 550px">
                <thead>
                    <tr class="table-light">
                        <th>Word</th>
                        <th>Recall level</th>
                    </tr>
                </thead>
                <tbody>`;

        let table_rows = '';
        const table_footer = '</tbody></table>';

        words.forEach(w => {
            table_rows += '<tr>'
                + '<td><a class="word fw-bold bg-warning-subtle border-bottom p-1">' + encodeHtml(w.word) + '</a></td>'
                + '<td><span class="word-description ' + answers[w.status][2] + '">' + answers[w.status][3] + '</span></td>'
                + '</tr>';
        });

        return table_header + table_rows + table_footer;
    }

    /**
     * Displays the frequency badge for the current word.
     * @param {boolean} is_phrase - Whether the current item is a phrase or single word
     */
    function showWordFrequency(is_phrase) {
        const $freq_badge = $("#study-card-freq-badge");
        if (is_phrase) {
            $freq_badge.removeClass('placeholder').addClass('border border-light').text('Phrase/Expression');
        } else {
            const freq_level = Dictionaries.getWordFrequency(words[cur_card_index].frequency_index) + ' frequency';
            $freq_badge.removeClass('placeholder').addClass('border border-light').text(freq_level);
        }
    }

    /**
     * Event: Triggered when clicking the translate button.
     * Opens the selected word in the translator.
     */
    $("#btn-translate").on("click", function () {
        const base_uris = Dictionaries.getURIs();
        if ($selword.length) {
            openInNewTab(LinkBuilder.forTranslationInStudy(base_uris.translator, $selword));
        }
    });

    /**
     * Event: Triggered when clicking the image dictionary button.
     * Opens the selected word in the image dictionary.
     */
    $("#btn-img-dic").on("click", function () {
        const base_uris = Dictionaries.getURIs();
        if ($selword.length) {
            openInNewTab(LinkBuilder.forWordInDictionary(base_uris.img_dictionary, $selword.text()));
        }
    });

    /**
     * Event: Triggered on right-click (non-mobile) on a revealed word.
     * Opens the word in the translator in a new tab.
     */
    $(document).on("contextmenu", function (e) {
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        if (!isMobile && $(e.target).is(".word")) {
            const base_uris = Dictionaries.getURIs();
            openInNewTab(LinkBuilder.forTranslationInStudy(base_uris.translator, $(e.target)));
        }
        return false;
    });

    /**
     * Event: Triggered when pressing keys 1-4 after answer is revealed.
     * Allows keyboard shortcuts for answer buttons.
     */
    $(document).keypress(function (e) {
        if (!$(".btn-answer").prop('disabled')) {
            switch (e.which) {
                case 49: $("#btn-answer-no-recall").click(); break; // 1
                case 50: $("#btn-answer-fuzzy").click(); break;     // 2
                case 51: $("#btn-answer-partial").click(); break;   // 3
                case 52: $("#btn-answer-excellent").click(); break; // 4
                default: break;
            }
        }
    });

    /**
     * Event: Triggered when clicking or tapping outside of word and action buttons.
     * Hides the action buttons modal.
     */
    $(document).on("mouseup touchend", function (e) {
        if ($(e.target).is(".word") === false && !$(e.target).closest('#action-buttons').length > 0) {
            e.stopPropagation();
            ActionBtns.hide();
        }
    });

    // -------- SCRAMBLE GAME HELPERS --------

    /**
     * Validates user's guess against the original word and provides feedback.
     */
    function onGuessSubmit() {
        const guess = ($("#guess-input").val() || "").trim();
        const original = $("#study-card").data('word');
        if (!guess) return;

        if (normalize(guess) === normalize(original)) {
            $("#guess-feedback").removeClass("text-secondary text-danger").addClass("text-success").text("Correct! Showing the original.");
            revealAnswer();
        } else {
            $("#guess-feedback").removeClass("text-secondary text-success").addClass("text-danger").text("Not quite. Try again or show the answer.");
        }
    }

    /**
     * Reveals the answer by showing the original word and updating examples.
     * Also displays and enables the answer buttons.
     */
    function revealAnswer() {
        const original = $("#study-card").data('word');
        if ($("#study-card").data('revealed')) return;

        $("#study-card").data('revealed', true);
        $("#study-card-word-title").text(original);

        // rebuild examples with original word shown, preserving order
        let examples_html = '';
        const scrambled = $("#study-card").data('scrambled');
        for (const example of current_examples_array) {
            examples_html += buildExampleHTML(example, original, scrambled, false); // false => show original (adds class "word")
        }
        $("#study-card-examples").html(examples_html);

        // show answer card and enable buttons
        $("#answer-card").removeClass("d-none");
        $(".btn-answer").prop('disabled', false);

        // hide guess UI after reveal
        $("#guess-ui").addClass("d-none");
    }

    /**
     * Shuffles a word while keeping the first letter in place.
     * @param {string} word - Word to shuffle
     * @returns {string} Shuffled word with first letter preserved
     */
    function shuffleKeepFirst(word) {
        const arr = Array.from(word);
        if (arr.length < 3) return word;
        const head = arr[0];
        const tail = arr.slice(1);

        let shuffled;
        for (let attempts = 0; attempts < 10; attempts++) {
            const temp = tail.slice();
            for (let i = temp.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [temp[i], temp[j]] = [temp[j], temp[i]];
            }
            shuffled = head + temp.join('');
            if (shuffled !== word) break;
        }
        return shuffled;
    }

    /**
     * Normalizes a string for comparison (lowercase, NFKC normalized, trimmed).
     * @param {string} s - String to normalize
     * @returns {string} Normalized string
     */
    function normalize(s) {
        return s.toLocaleLowerCase().normalize("NFKC").trim();
    }

    /**
     * Escapes special regex characters in a string.
     * @param {string} s - String to escape
     * @returns {string} Escaped string safe for regex
     */
    function escapeRegex(s) {
        return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    /**
     * Encodes HTML special characters to prevent XSS.
     * @param {string} s - String to encode
     * @returns {string} HTML-safe string
     */
    function encodeHtml(s) {
        return String(s)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

});