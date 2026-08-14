// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function () {

    const answers = [
        ["0", 0, "bg-success", "Excellent"],
        ["1", 0, "bg-warning", "Partial"],
        ["2", 0, "bg-primary", "Fuzzy"],
        ["3", 0, "bg-danger", "No recall"]
    ];
    let session;

    Dictionaries.fetchURIs(); // get dictionary & translator URIs

    /**
     * Shows or hides the AI feedback box.
     * @param {boolean} is_visible - Whether the feedback box should be visible
     */
    function setStudyAiAnswerVisible(is_visible) {
        $("#studyai-answer").toggleClass("d-none", !is_visible);
    }

    /**
     * Shows the pre-feedback prompt in the right column.
     */
    function showAnswerPrompt() {
        $("#answer-card-prompt").removeClass("d-none");
        $("#answer-card-body").addClass("d-none");
        session.setAnswerButtonsDisabled(true);
    }

    /**
     * Shows the answer rating card in the right column.
     */
    function showAnswerCard() {
        $("#answer-card-prompt").addClass("d-none");
        $("#answer-card-body").removeClass("d-none");
        session.setAnswerButtonsDisabled(false);
    }

    /**
     * Hides the controls that only apply while a study card is active.
     */
    function hideStudyControls() {
        $("#answer-card-prompt").addClass("d-none");
        $("#live-progress").addClass("d-none");
    }

    /**
     * Resets the current card UI to its pre-feedback state.
     */
    function resetExerciseState() {
        showAnswerPrompt();
        setStudyAiAnswerVisible(false);
        $("#text-user-answer").val("").trigger("focus");
        $("#text-studyai-answer").val("");
    }

    /**
     * Updates the study card with the given word object.
     * @param {*} wordObj 
     */
    function updateCard(wordObj) {
        $("#study-card").data('word', wordObj.word);
        session.updateLiveProgressBar();
        $("#card-counter").text((session.getCurrentCardIndex() + 1) + "/" + session.getMaxCards());
        $("#study-card-word-title")
            .removeClass("placeholder w-50 rounded")
            .text(wordObj.word);
        updateAnswerLabel(wordObj.word);
        showWordFrequency(wordObj.is_phrase);
        session.adaptCardStyleToWordStatus(wordObj.status);
        resetExerciseState();
    } 

    /**
     * Updates the answer label to include the current word.
     * @param {string} word 
     */
    function updateAnswerLabel(word) {
        $('#label-user-answer').text(`Write one or more sentences using "${word}":`);
    }

    /**
     * Triggers when user clicks submit button to get AI evaluation of user answer
     */
    $('#btn-submit-user-answer').on("click", function () {
        const user_answer = $('#text-user-answer').val().trim();
        const vocab_piece = session.getCurrentCard().word;
        const is_vocab_piece_present = user_answer.toLowerCase().includes(vocab_piece.toLowerCase());

        showAnswerCard();
        setStudyAiAnswerVisible(true);

        if (user_answer === '') {
            return $('#text-studyai-answer').val("(1) Completely incorrect — couldn't provide an answer.");
        }

        if (!is_vocab_piece_present) {
            return $('#text-studyai-answer').val(`(1) Completely incorrect - "${vocab_piece}" is missing from your sentence.`);
        }

        const prompt = buildEvalPrompt(vocab_piece, user_answer);
        $('#text-studyai-answer').val('Lingobot is thinking...');

        AIBot.streamReply(prompt, {
            onUpdate(markdown_so_far) {
                $('#text-studyai-answer').val(markdown_so_far);
            },
            onError() {
                $('#text-studyai-answer').val('Failed to get response from AI. Please try again.');
            }
        });
    }); 

    /**
     *  Constructs prompt to pass to the AI
     * @param {string} vocab_piece 
     * @param {string} user_answer 
     * @returns 
     */
    function buildEvalPrompt(vocab_piece, user_answer) {
        const answer_format = `Evaluate the user's example sentence. The primary focus is whether "${vocab_piece}" itself is used correctly. Rate on this scale — choose the one that best fits: (1) Completely incorrect — "${vocab_piece}" is absent, used with the wrong meaning, or the sentence is too broken to judge its usage; (2) Incorrect — "${vocab_piece}" is present and its intent is recognizable, but it is grammatically or semantically incorrect (e.g. wrong form, wrong preposition required by this word, wrong register); (3) Mostly Correct — "${vocab_piece}" is used correctly and naturally. The only issues are in other parts of the sentence (e.g. agreement of an unrelated word, spelling of another word, a small grammar slip unrelated to "${vocab_piece}"); (4) Perfect — "${vocab_piece}" and the rest of the sentence are both correct, or any remaining imperfections are too trivial to mention. Do not penalize a short sentence for limited context. Output format — two lines only: Line 1: the rating, e.g. (3) Mostly Correct; Line 2: one concise sentence of feedback; include a corrected version only if something needs fixing.`;
        
        return `${answer_format}\nAnswer: ${user_answer}`;
    }

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
     * Implements keyboard shortcuts for answer buttons.
     * @param {JQuery.KeyDownEvent} e - Keyboard event triggered on the document.
     */
    $(document).on("keydown", function (e) {
        let $button = null;

        if (
            (e.ctrlKey || e.metaKey)
            && e.key === "Enter"
            && $(e.target).is("#text-user-answer")
        ) {
            e.preventDefault();
            e.stopPropagation();
            $("#btn-submit-user-answer").trigger("click");
            return;
        }

        if ($(".btn-answer").prop('disabled')) {
            return;
        }

        switch (e.key) {
            case "1":
                $button = $("#btn-answer-no-recall");
                break;
            case "2":
                $button = $("#btn-answer-fuzzy");
                break;
            case "3":
                $button = $("#btn-answer-partial");
                break;
            case "4":
                $button = $("#btn-answer-excellent");
                break;
            default:
                return;
        }

        e.preventDefault();
        e.stopPropagation();
        $button.trigger("click");
    }); 

    session = StudySession.create({
        answers,
        card_filter: { status: 0 },
        on_card_ready: (word) => updateCard(word),
        on_empty: () => {
            $("#ai-card").html(`
                <div class='bi bi-exclamation-circle text-danger display-3'></div>
                <div class='mt-3'>It seems there are no cards in your deck. Add
                some words to your library and try again.</div>
            `);
            hideStudyControls();
        },
        on_complete: () => {
            $("#ai-card").html(`
                <img src="/img/gamification/finished.gif" style="max-width: 300px;" alt="Finished!">
                <div class="mt-3">You have reached the end of your study.</div>
            `);
            $("#answer-card-prompt").addClass("d-none");
            $("#answer-card-body").removeClass("d-none");
            hideStudyControls();
        }
    });
    session.load();
});
