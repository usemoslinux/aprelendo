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
    let dictionary_URI = "";              // user dictionary URI
    let img_dictionary_URI = "";              // user image dictionary URI
    let translator_URI = "";              // user translator URI
    let $selword = $();             // jQuery object used to open dictionary modal
    let words = [];              // array containing all words user is learning
    let max_cards = 10;              // maximum nr. of cards
    let cur_card_index = 0;               // current card/word index

    // nr. of words recalled during practice
    let answers = [
        ["0", 0, "bg-success", "Excellent"],
        ["1", 0, "bg-warning", "Partial"],
        ["2", 0, "bg-primary", "Fuzzy"],
        ["3", 0, "bg-danger", "No recall"],
        ["4", 0, "text-warning bg-dark", "No example sentence found!"],
    ];

    // initialize modal dictionary window buttons
    // $("#btn-translate").hide();
    $("#btn-translate").removeClass("ps-0");
    $("#btn-remove").hide();
    $("#btn-add").hide();
    $("#btn-cancel").removeClass().addClass("btn-close me-1").html('');
    $(".modal-header").addClass("p-0");

    // disable Yes/No buttons
    $(".btn-answer").prop('disabled', true);

    // ajax call to get dictionary URI
    $.ajax({
        url: "/ajax/getdicuris.php",
        type: "GET",
        dataType: "json"
    }).done(function (data) {
        if (data.error_msg == null) {
            dictionary_URI = data.dictionary_uri;
            img_dictionary_URI = data.img_dictionary_uri;
            translator_URI = data.translator_uri;
        }
    }); // end $.ajax 

    getListofCards();

    /**
     * Fetches list of words user is learning
     */
    function getListofCards() {
        $.ajax({
            type: "POST",
            url: "ajax/getcards.php",
            data: { limit: max_cards },
            dataType: "json"
        })
            .done(function (data) {
                if (data.error_msg) {
                    showMessage("Oops! There was an unexpected error trying to fetch the list of words you are learning "
                        + "in this language.", "alert-danger");
                    return;
                }

                if (data.length == 0) {
                    showNoMoreCardsMsg();
                    return true;
                }

                words = data.map(item => {
                    return {
                        ...item, // Preserve the original properties
                        word: item.word.replace(/\r?\n|\r/g, " ") // Replace line breaks with spaces
                    };
                });

                max_cards = words.length > max_cards ? max_cards : words.length;

                $("#card-counter").text("1" + "/" + max_cards);
                adaptCardStyleToWordStatus(words[0].status);
                getExampleSentencesforCard(words[0].word);
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                showMessage("Oops! There was an unexpected error trying to fetch the list of words you are learning "
                    + "in this language.", "alert-danger");
            }); // end $.ajax
    } // end getListofCards()

    /**
     * Fetches examples sentences for a specific word
     * @param {string} word
     */
    function getExampleSentencesforCard(word) {
        // if deck is empty or last card is reached, exit
        if (lastCardReached()) {
            return;
        }

        // empty card and show spinner
        $("#examples-placeholder").removeClass('d-none');
        $("#study-card-examples").empty();

        $.ajax({
            type: "POST",
            url: "ajax/getcards.php",
            data: { word: word },
            dataType: "json"
        })
            .done(function (data) {
                let examples_array = [];
                let examples_html = '';
                const lang_iso = $("#card").data("lang");

                let sentence_regex = new RegExp(
                    '([^\\n.?!]|[\\d][.][\\d]|[A-Z][.](?:[A-Z][.])+)*(?<![\\p{L}])'
                    + word
                    + '(?![\\p{L}])([^\\n.?!]|[.][\\d]|[.](?:[A-Z][.])+)*[\\n.?!]',
                    'gmiu'
                );

                // different sentence separator for Japanese and Chinese, as
                // they don't separate words and finish sentences with 。
                if (lang_iso == "ja" || lang_iso == "zh") {
                    sentence_regex = new RegExp(
                        '[^\n?!。]*' + word + '[^\n?!。]*[\n?!。]',
                        'gmiu'
                    );
                }

                data.forEach(text => {
                    // extract example sentences from text
                    let m;
                    while ((m = sentence_regex.exec(text.text)) !== null) {
                        // This is necessary to avoid infinite loops with zero-width matches
                        if (m.index === sentence_regex.lastIndex) {
                            sentence_regex.lastIndex++;
                        }

                        if (examples_array.length < 3) {
                            // create html for each example sentence, max 3 examples
                            let match = m[0];

                            // check that match is not the only word in current example sentence
                            if (match !== word) {
                                // make sure example sentence is unique, then add to the list
                                text.text = doubleQuotesNotClosed(match) ? text.text : match;
                                examples_array = forceUnique(examples_array, text);
                            }
                        }
                    }
                });

                // if example sentence is empty, go to next card
                if (examples_array.length === 0) {
                    $("#study-card-word-title").text("Skipped. No examples found.");
                    words[cur_card_index].status = 4;
                    cur_card_index++;
                    if (lastCardReached()) {
                        return;
                    }
                    getExampleSentencesforCard(words[cur_card_index].word);
                } else {
                    examples_array = shuffleExamples(examples_array);
                    examples_array.forEach(example => {
                        examples_html += buildExampleHTML(example, word);
                    });
                }

                // show card
                $("#study-card").data('word', word);
                $("#card-counter").text((cur_card_index + 1) + "/" + max_cards);
                $("#study-card-word-title").removeClass('placeholder').text(word);
                $("#examples-placeholder").addClass('d-none');
                $("#study-card-examples").append(examples_html);
                $(".btn-answer").prop('disabled', false);

                const doclang = $("#study-card").data("lang");
                glowIfHighOrMedFreq(word, doclang);
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                showMessage("Oops! There was an unexpected error trying to fetch example sentences for this word.",
                    "alert-danger");
            }); // end $.ajax
    } // end getExampleSentencesforCard()

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
        const word_regex = new RegExp('(?<![\\p{L}|\\d])' + word + '(?![\\p{L}|\\d])', 'gmiu');
        let example_html = '';
        let example_text_html = '';
        let result = [];

        // make the word user is studying clickable
        example_text_html = text.text.replace(word_regex, function (match, g1) {
            return g1 === undefined
                ? match
                : "<a class='word fw-bold'>" + match.replace(/\s\s+/g, ' ') + "</a>";
        });

        example_html = "<blockquote cite='" + text.source_uri + "'>";
        example_html += "<p>" + example_text_html + "</p>";
        example_html += "<cite>" + (text.author == "" ? "Anonymous" : text.author);
        if (text.source_uri == '' || text.source_uri.endsWith(".epub")) {
            example_html += ", " + text.title;
        } else {
            example_html += ", <a href='" + text.source_uri + "' target='_blank'>" + text.title + "</a>"
        }
        example_html += "</cite></blockquote>"

        result.push(example_html)
        return result;
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
     * Checks if the last card in the study session has been reached.
     * If no cards are available or the current card index exceeds the total number of cards,
     * it displays a message indicating the end of the study session, including a progress summary
     * and a recommendation to take breaks. If the last card is not yet reached, the function returns false.
     * @returns {boolean} True if the last card has been reached, otherwise false.
     */
    function lastCardReached() {
        if (max_cards == 0) {
            showNoMoreCardsMsg();
            return true;
        } else if (cur_card_index >= max_cards) {
            $("#study-card-word-title").text("Congratulations!");
            adaptCardStyleToWordStatus();

            glowIfHighOrMedFreq();

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
                + "<div class='small mt-4'>If you want to continue, you can "
                + "refresh this page (F5).<br>However, we strongly recommend that you keep your study sessions short "
                + "and take rest intervals.</div>");
            $("#study-card-footer").addClass("d-none");
            $("#examples-placeholder").addClass("d-none");
            scrollToPageTop();
            return true;
        }
        return false;
    } // end lastCardReached()

    /**
     * Displays a message indicating that there are no more cards available for practice.
     * Updates the card header and body to reflect the lack of cards, encouraging the user
     * to add more words to their library. Hides the footer and example placeholders.
     * @returns {void}
     */
    function showNoMoreCardsMsg() {
        $("#study-card-header").text("Sorry, no cards to practice");
        adaptCardStyleToWordStatus(3); // title in red
        $("#study-card-examples").html("<div class='bi bi-exclamation-circle text-danger display-3'>"
            + "</div><div class='mt-3'>It seems there are no cards in your deck. "
            + "Add some words to your library and try again.</div>");
        $("#study-card-footer").addClass("d-none");
        $("#examples-placeholder").addClass("d-none");
    } // end showNoMoreCardsMsg()

    /**
     * Adjusts the style of the study card based on the given word status.
     * @param {number} status - The status of the word, which determines the styling of the card.
     * @returns {void}
     */
    function adaptCardStyleToWordStatus(status) {
        const $card = $("#study-card");
        const $card_header = $("#study-card-header");

        // remove "border-*" classes from #study-card
        $card.removeClass(function (index, className) {
            return (className.match(/\bborder-\S+/g) || []).join(' ');
        });

        // remove "border-*" classes from #study-card-header
        $card_header.removeClass(function (index, className) {
            return (className.match(/\bborder-\S+|\btext-bg-\S+/g) || []).join(' ');
        });

        switch (status) {
            case 0:
                $card.addClass('border-success');
                $card_header.addClass('text-bg-success border-success');
                break;
            case 1:
                $card.addClass('border-warning');
                $card_header.addClass('text-bg-warning border-warning');
                break;
            case 2:
                $card.addClass('border-primary');
                $card_header.addClass('text-bg-primary border-primary');
                break;
            case 3:
                $card.addClass('border-danger');
                $card_header.addClass('text-bg-danger border-danger');
                break;
            default:
                $card.addClass('border-secondary');
                $card_header.addClass('text-bg-secondary border-secondary');
                break;
        }
    } // end adaptCardStyleToWordStatus()

    /**
     * Open dictionary modal
     * Triggers when user clicks word
     * @param {event object} e
     */
    $("body").on("click", ".word", function (e) {
        $selword = $(this);
        showActionButtonsPopUpToolbar();
    }); // end #.word.on.click

    /**
     * Triggers when user clicks on answer buttons
     * @param {event object} e
     */
    $(".btn-answer").click(function (e) {
        e.preventDefault();
        const word = $("#study-card").data('word');
        const answer = $(this).attr("value");

        answers[answer][1] = answers[answer][1] + 1;
        words[cur_card_index].status = answer;

        // disable answer buttons
        $(".btn-answer").prop('disabled', true);

        // update card status
        $.ajax({
            type: "POST",
            url: "ajax/updatecard.php",
            data: { word: word, answer: answer }
            // dataType: "json"
        })
            .done(function (data) {
                // go to next card
                cur_card_index++;

                if (lastCardReached()) {
                    return;
                }

                getExampleSentencesforCard(words[cur_card_index].word);
                adaptCardStyleToWordStatus(words[cur_card_index].status);
                scrollToPageTop();
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                showMessage("There was an unexpected error updating this word's status", "alert-danger");
            });
    }); // end .btn-answer.on.click()

    /**
     * Generates an HTML table displaying a summary of the study, showing each word and its recall level.
     * @returns {string} The complete HTML string for the table.
     */
    function buildResultsTable() {
        const table_header =
            `<table class="table table-bordered table-striped text-end small mx-auto mt-3"
            aria-describedby="" style="max-width: 550px">
        <thead>
            <tr class="table-light">
                <th>Word</th>
                <th>Recall level</th>
            </tr>
        </thead>
        <tbody>`;

        let table_rows = '';
        const table_footer = '</tbody></table>';

        words.forEach(word => {
            table_rows += '<tr>'
                + '<td><a class="word bw-bold">' + word.word + '</a></td>'
                + '<td><span class="word-description ' + answers[word.status][2] + '">' + answers[word.status][3]
                + '</span></td>'
                + '</tr>';
        });

        return table_header + table_rows + table_footer;
    } // end buildResultsTable()

    /**
     * Updates the styling of a study card based on the frequency level of a word.
     * The function first waits for an asynchronous call to retrieve the word's frequency.
     * Depending on the frequency level, the function modifies the card's appearance to reflect
     * whether the word is high, medium, or low frequency.
     * @param {string} word - The word whose frequency level is being checked.
     * @param {string} doclang - ISO code for the language of the document in which the word appears.
     * @returns {void}
     */
    async function glowIfHighOrMedFreq(word, doclang) {
        // Wait until ajax call finishes
        await getWordFrequency(word, doclang);

        // Glow card if neccesary
        if ($("#bdgfreqlvl").hasClass("text-bg-danger")) {
            $("#study-card")
                .removeClass("card-medium-freq")
                .addClass("card-high-freq");
        } else if ($("#bdgfreqlvl").hasClass("text-bg-warning")) {
            $("#study-card")
                .removeClass("card-high-freq")
                .addClass("card-medium-freq");
        } else {
            $("#study-card").removeClass("card-medium-freq card-high-freq");
        }
    } // end glowIfHighOrMedFreq()

    /**
     * Opens translator in new window. 
     * Triggers when user click in translate button in modal window
     */
    $("#btn-translate").on("click", function () {
        openInNewTab(buildStudyTranslationLink(translator_URI, $selword));
    }); // end #btn-translate.on.click()

    $("#btn-img-dic").on("click", function () {
        openInNewTab(buildDictionaryLink(img_dictionary_URI, $selword.text()));
    }); // end #btn-img-dic.on.click()

    /**
     * Disables right click context menu
     */
    $(document).on("contextmenu", function (e) {
        // opens dictionary translator in case user right clicked on a word/phrase
        // but only on desktop browsers
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

        if (!isMobile && $(e.target).is(".word")) {
            openInNewTab(buildStudyTranslationLink(translator_URI, $(e.target)));
        }
        return false;
    }); // end document.contextmenu

    /**
     * Implements shortcuts for buttons
     * @param {event object} e
     */
    $(document).keypress(function (e) {
        // only allow shortcuts if buttons are enabled
        if (!$(".btn-answer").prop('disabled')) {
            switch (e.which) {
                case 49: // 49 is the keycode for "1" key
                    $("#btn-answer-no-recall").click();
                    break;
                case 50: // 50 is the keycode for "2" key
                    $("#btn-answer-fuzzy").click();
                    break;
                case 51: // 51 is the keycode for "3" key
                    $("#btn-answer-partial").click();
                    break;
                case 52: // 52 is the keycode for "4" key
                    $("#btn-answer-excellent").click();
                    break;
                default:
                    break;
            }
        }
    }); // end document.keypress()

    /**
     * Removes selection when user clicks in white-space
     */
    $(document).on("mouseup touchend", function (e) {
        if ($(e.target).is(".word") === false && !$(e.target).closest('#action-buttons').length > 0) {
            e.stopPropagation();
            hideActionButtons();
        }
    }); // end $document.on.mouseup

    /**
     * Shows pop up toolbar when user clicks a word
     */
    function showActionButtonsPopUpToolbar() {
        setWordActionButtons($selword);

        const base_uris = {
            dictionary: dictionary_URI,
            img_dictionary: img_dictionary_URI,
            translator: translator_URI
        };

        setDicActionButtonsClick($selword, base_uris, 'study');
        showActionButtons($selword);
    } // end showActionButtonsPopUpToolbar
});