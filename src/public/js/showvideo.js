// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function () {
    let gems_earned = 0;

    // HTML selectors
    const doclang = $("html").attr("lang");

    // configuration to show confirmation dialog on close
    let show_confirmation_dialog = true; // confirmation dialog that shows when closing window before saving data

    // Fix for mobile devices where vh includes hidden address bar
    // First we get the viewport height and we multiple it by 1% to get a value for a vh unit
    let vh = window.innerHeight * 0.01;
    // Then we set the value in the --vh custom property to the root of the document
    document.documentElement.style.setProperty('--vh', `${vh}px`);

    // initialize video player
    initializeVideoPlayer(0); // start at 0 seconds

    // initial AJAX calls
    underlineText(); // underline text with user words/phrases

    /**
     * Fetches user words/phrases from the server and underlines them in the text, but only if this
     * is a simple text, not an ebook
     */
    async function underlineText() {
        try {
            $('#text').html(await ReaderHelpers.annotateText($('#text').html(), doclang));
            TextProcessor.updateAnchorsList();
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
    }

    // *************************************************************
    // ****************** AUDIO/VIDEO CONTROLLER ******************* 
    // *************************************************************

    ReaderHelpers.initializeReaderActions({
        action_btns: VideoActionBtns,
        controller: VideoController,
        source: "video"
    });

    // *************************************************************
    // **** ACTION BUTTONS (ADD, DELETE, FORGOT & DICTIONARIES) **** 
    // *************************************************************

    ReaderHelpers.bindWordActionButtons({
        doclang: doclang,
        action_btns: VideoActionBtns,
        controller: VideoController,
        get_source_id: function () {
            return $('[data-idtext]').attr('data-idtext');
        },
        text_is_shared: true,
        sentence_with_context: false,
        get_word_anchors: function () {
            return $("a.word");
        },
        word_value_mode: "lowercase"
    });

    // *************************************************************
    // ******************* MAIN MENU BUTTONS ***********************
    // *************************************************************

    /**
     * Finished studying this text. Archives text & saves new status of words/phrases
     * Executes when the user presses the big green button at the end
     */
    $(document).on("click", "#btn-save-ytvideo", async function () {
        const text_ids = [$("#text-container").attr("data-IdText")];

        try {
            const save_data = await ReaderHelpers.saveReviewProgress({
                words_selector: ".learning",
                text_ids: text_ids
            });

            gems_earned = save_data.gems_earned;
            show_confirmation_dialog = false;
            ReaderHelpers.submitTextStatsForm({
                gems_earned: gems_earned,
                is_shared: 1
            });
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
    }); 

    /**
     * Updates vh value on window resize
     * Fix for mobile devices where vh includes hidden address bar
     */
    $(window).on('resize', function () {
        vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    });

    /**
     * Shows dialog message reminding users to save changes before leaving
     */
    ReaderHelpers.bindBeforeUnloadWarning(function () {
        return show_confirmation_dialog;
    }); 
});
