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

    let words = [];                 // array containing all words user is learning
    let max_cards = 10;             // maximum nr. of cards
    let cur_card_index = 0;         // current card/word index

    // nr. of words recalled during practice
    let answers = [
        ["0", 0, "bg-success", "Excellent"],
        ["1", 0, "bg-warning", "Partial"],
        ["2", 0, "bg-primary", "Fuzzy"],
        ["3", 0, "bg-danger", "No recall"]
    ];

    $(".btn-answer").prop('disabled', true); // disable answer buttons
    Dictionaries.fetchURIs(); // get dictionary & translator URIs
    getListofCards();

    /**
     * Fetches list of words user is learning
     */
    async function getListofCards() {
        try {
            const form_data = new URLSearchParams({ limit: max_cards, status: 0 });
            const response = await fetch("/ajax/getcards.php", {
                method: "POST",
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
            
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to fetch list of cards.');
            }

            if (data.payload.length == 0) {
                showNoMoreCardsMsg();
                return true;
            }

            words = data.payload.map(item => {
                return {
                    ...item, // Preserve the original properties
                    word: item.word.replace(/\r?\n|\r/g, " ") // Replace line breaks with spaces
                };
            });

            max_cards = words.length > max_cards ? max_cards : words.length;

            updateCard(words[0]);
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
    } // end getListofCards()

    /**
     * Updates the study card with the given word object.
     * @param {*} wordObj 
     */
    function updateCard(wordObj) {
        $("#study-card").data('word', wordObj.word);
        updateLiveProgressBar(); // update live progress bar
        $("#card-counter").text((cur_card_index + 1) + "/" + max_cards);
        $("#study-card-word-title").removeClass('placeholder').text(wordObj.word);
        updatePromptSelect(wordObj.word);
        showWordFrequency(words[cur_card_index].is_phrase);
        adaptCardStyleToWordStatus(wordObj.status);
    } // end updateCard()

    /**
     * Updates the prompt select options to include the current word.
     * @param {string} word 
     */
    function updatePromptSelect(word) {
        $('#select-prompt option').each(function() {
            const $option = $(this);
            const template = $option.data('template') || $option.text();

            if (!$option.data('template')) {
                $option.data('template', template);
            }

            const updated_text = template.replace(/{word}/, `"${word}"`);
            $option.text(updated_text);
        });
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
            $("#study-card-freq-badge").addClass('d-none');
            adaptCardStyleToWordStatus();

            let progress_html = "";
            for (const answer of answers) {
                let subtotal = answer[1];
                let percentage = subtotal / max_cards * 100;
                let bg_class = answer[2];
                let title = answer[3];
                
                progress_html += `
                    <div class="progress-bar ${bg_class}" 
                        role="progressbar" 
                        aria-valuenow="${percentage}" 
                        aria-valuemin="0" 
                        aria-valuemax="100" 
                        style="width: ${percentage}%" 
                        title="${title}: ${subtotal} answer(s)">
                        ${Math.round(percentage)} %
                    </div>`;
            }

            $("#ai-card").html(`
                <div class="bi bi-trophy text-warning display-3 mt-3"></div>
                <div class="mt-3">You have reached the end of your study.</div>
                <div class="mt-3">These were your results:</div>
                <div class="progress mx-auto mt-3 fw-bold" style="height: 25px; max-width: 550px">
                    ${progress_html}
                </div>
                ${buildResultsTable()}
                <div class="small mt-4">
                    If you want to continue, you can refresh this page (F5).<br>
                    However, we strongly recommend that you keep your study sessions short 
                    and take rest intervals.
                </div>
            `);
            $("#study-card-footer").addClass("d-none");
            $("#live-progress").addClass("d-none");
            scrollToPageTop();
            return true;
        }
        return false;
    } // end lastCardReached()

    /**
     * Displays a message indicating that there are no more cards available for practice.
     * Updates the card header and body to reflect the lack of cards, encouraging the user
     * to add more words to their library. Hides the footer.
     * @returns {void}
     */
    function showNoMoreCardsMsg() {
        $("#study-card-header").text("Sorry, no cards to practice");
        adaptCardStyleToWordStatus(3); // title in red
        $("#ai-card").html(`
            <div class='bi bi-exclamation-circle text-danger display-3'></div>
            <div class='mt-3'>It seems there are no cards in your deck. Add
            some words to your library and try again.</div>
        `);
        $("#study-card-footer").addClass("d-none");
        $("#live-progress").addClass("d-none");
    } // end showNoMoreCardsMsg()

    /**
     * Updates the progress bar to reflect the current study progress.
     */
    function updateLiveProgressBar() {
        const percentage = Math.round((cur_card_index + 1) / max_cards * 100);
        $("#live-progress-bar")
            .css("width", percentage + "%")
            .attr("aria-valuenow", percentage);
    } // end updateLiveProgressBar()

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
    } // end adaptCardStyleToWordStatus()

    /**
     * Triggers when user clicks submit button to get AI evaluation of user answer
     */
    $('#btn-submit-user-answer').on("click", function () {
        if ($('#text-user-answer').val().trim() !== '') {
            const answer_format = "Evaluate user response in two lines: first line with one of the four ratings (1) Completely incorrect (major mistakes or confused meaning); (2) Incorrect, but close (made some significant usage mistakes, but I was on the right track); (3) Mostly correct (made minor errors (missing details, awkward phrasing, etc.); or (4) Correct & comprehensive (perfect answer). Second line with a brief explanation and, if useful, a corrected version. Keep it concise.";
            const prompt = answer_format + '\n Question:' + $('#select-prompt').val() + '\n Answer: ' + $('#text-user-answer').val();
            if (!prompt) return;

            const converter = new showdown.Converter();
            
            $('#text-ai-answer').html('Lingobot is thinking...');

            AIBot.streamReply(prompt, {
                onUpdate(markdownSoFar) {
                    const html = converter.makeHtml(markdownSoFar);
                    $('#text-ai-answer').html(html);
                },
                onError() {
                    $('#text-ai-answer').html('<p>Failed to get response from AI. Please try again.</p>');
                }
            });
        } else {
            $('#text-ai-answer').html("(1) Completely incorrect — couldn't provide an answer.");
        }

        $(".btn-answer").prop('disabled', false); // enable answer buttons
    }); // end #btn-submit-user-answer.on.click()

    /**
     * Triggers when user clicks on answer buttons
     * @param {event object} e
     */
    $(".btn-answer").on("click", async function (e) {
        e.preventDefault();
        const word = $("#study-card").data('word');
        const answer = $(this).attr("value");

        answers[answer][1] = answers[answer][1] + 1;
        words[cur_card_index].status = answer;

        $(".btn-answer").prop('disabled', true); // disable answer buttons
        $("#text-ai-answer").text(''); // clear AI answer box
        $("#text-user-answer").val('').trigger("focus"); // clear user answer box and focus it

        try {
            const form_data = new URLSearchParams({ word: word, answer: answer });
            const response = await fetch("/ajax/updatecard.php", {
                method: "POST",
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to update word status.');
            }

            cur_card_index++;
    
            if (lastCardReached()) {
                return;
            }

            updateCard(words[cur_card_index]);
            scrollToPageTop();
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
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
     * Updates the frequency badge on the study card to display the frequency level of the current word.
     */
    function showWordFrequency(is_phrase) {
        const $freq_badge = $("#study-card-freq-badge");

        if (is_phrase) {
            $freq_badge
                .removeClass('placeholder')
                .addClass('border border-light')
                .text('Phrase/Expression');
        } else {
            const freq_level = Dictionaries.getWordFrequency(words[cur_card_index].frequency_index) + ' frequency';
            $freq_badge
                .removeClass('placeholder')
                .addClass('border border-light')
                .text(freq_level);
        }
    } // end showWordFrequency()

    /**
     * Triggered when clicking on a revealed word (has "word" class).
     * Opens the dictionary modal for the selected word.
     */
    $("body").on("click", ".word", function () {
        StudyActionBtns.show($(this));
    });

    /**
     * Triggered when clicking or tapping outside of word and action buttons.
     * Hides the action buttons modal.
     */
    $(document).on("mouseup touchend", function (e) {
        if ($(e.target).is(".word") === false && !$(e.target).closest('#action-buttons').length > 0) {
            e.stopPropagation();
            ActionBtns.hide();
        }
    });

    /**
     * Disables right click context menu
     */
    $(document).on("contextmenu", function (e) {
        return false;
    }); // end document.contextmenu

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
    }); // end $document.on.keypress()
});
