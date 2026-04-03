// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function () {
    let gems_earned = 0;
    
    // HTML selectors
    const doclang = $("html").attr("lang");
    const player = document.querySelector('video');
    let current_blob_url = null;
    
    // configuration to show confirmation dialog on close
    let show_confirmation_dialog = true; // confirmation dialog that shows when closing window before saving data

    // Fix for mobile devices where vh includes hidden address bar
    // First we get the viewport height and we multiple it by 1% to get a value for a vh unit
    let vh = window.innerHeight * 0.01;
    // Then we set the value in the --vh custom property to the root of the document
    document.documentElement.style.setProperty('--vh', `${vh}px`);

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
            return $('[data-idtext]').attr('data-idtext') || '';
        },
        text_is_shared: false,
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
    $("#btn-save-offline-video").on("click", updateWordsLearningStatus);

    /**
     * Archives text and updates status of all underlined words & phrases
     */
    async function updateWordsLearningStatus() {
        const text_ids = [$("#text-container").attr("data-IdText")];

        try {
            const save_data = await ReaderHelpers.saveReviewProgress({
                words_selector: "#text .reviewing",
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
    } 

    /**
     * Open video selection dialog
     */
    $("#btn-selvideo").on("click", function (e) {
        e.preventDefault();
        $("#video-file-input").trigger('click');
    }); 

    /**
     * After user selects video, load it
     */
    $("#video-file-input").on("change", function () {
        if (this.files[0]) {
            const file = this.files[0];
            const type = file.type;
            if (player.canPlayType(type)) {
                if (current_blob_url) {
                    URL.revokeObjectURL(current_blob_url);
                }

                current_blob_url = URL.createObjectURL(file);
                player.src = current_blob_url;
            }
        }
    }); 

    /**
     * Open subtitle selection dialog
     */
    $("#btn-selsubs").on("click", function (e) {
        e.preventDefault();
        $("#subs-file-input").trigger('click');
    }); 
    
    /**
     * Load subtitles selected by user
     */
    $("#subs-file-input").on("change", function () {
        if (this.files[0]) {
            const file = this.files[0];
            const reader = new FileReader();

            reader.addEventListener('load', async (event) => {
                const srt = event.target.result;
                const data = parser.fromSrt(srt, true);
                let text = '';

                for (const element of data) {
                    let line = '<span';

                    for (let key in element) {
                        let value = element[key];
                        switch (key) {
                            case 'startTime':
                                line += ' data-start="' + value + '"';
                                break;
                            case 'endTime':
                                line += ' data-end="' + value + '"';
                                break;
                            case 'text':
                                line += '>' + value.replace(/(\r\n|\n|\r)/g, " ");
                                break;
                            default:
                                break;
                        }
                    }

                    line += '</span>' + "\r\n";
                    text += line;
                }

                document.getElementById('text').innerHTML = text;

                try {
                    $('#text').html(await ReaderHelpers.annotateText($('#text').html(), doclang));
                    TextProcessor.updateAnchorsList();
                } catch (error) {
                    console.error(error);
                    alert(`Oops! ${error.message}`);
                }
            });

            reader.readAsText(file);
            $("#nosubs").remove(); // remove "no subtitles loaded" message
            $("#btn-save-offline-video").removeClass("disabled"); // enable save button
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

    /**
     * Releases any video blob URL when the page is actually unloading.
     *
     * @returns {void}
     */
    $(window).on("unload", function () {
        if (current_blob_url) {
            URL.revokeObjectURL(current_blob_url);
            current_blob_url = null;
        }
    }); 
});
