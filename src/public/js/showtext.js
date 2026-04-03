// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function() {
    // assisted learning
    let next_phase = 2; // next phase of the learning cycle

    // HTML selectors
    const doclang = $("html").attr("lang");
    const $doc = $(parent.document);
    
    // configuration to show confirmation dialog on close
    window.parent.show_confirmation_dialog = true;

    // initial AJAX calls
    loadAudio();
    underlineText(); // underline text with user words/phrases

    /**
     * Fetches user words/phrases from the server and underlines them in the text, but only if this
     * is a simple text, not an ebook
     */
    async function underlineText() {
        if ($('#text-container').data('type') == 'text') {
            try {
                $('#text').html(await ReaderHelpers.annotateText($('#text').text(), doclang));
                TextProcessor.updateAnchorsList();
            } catch (error) {
                console.error(error);
                alert(`Oops! ${error.message}`);
            } finally {
                skipAudioPhases();
            }
        }
    } 

    // *************************************************************
    // ****************** AUDIO/VIDEO CONTROLLER ******************* 
    // *************************************************************
    let MediaController;

    MediaController = ReaderHelpers.resolveMediaController();

    if (!MediaController) {
        // Handle the case where neither controller is available
        console.log('No suitable media controller found.');
    } else {
        ReaderHelpers.initializeReaderActions({
            action_btns: TextActionBtns,
            controller: MediaController,
            source: "text"
        });
    }

    function hasAudioSource() {
        const $audio = $("#audioplayer");
        if ($audio.length === 0) {
            return false;
        }

        const playlist_src = $audio.data("playlistSrc");
        if (playlist_src && String(playlist_src).trim() !== "") {
            return true;
        }

        return $("#audioplayer").find("source").attr("src") != "";
    }

    // *************************************************************
    // **** ACTION BUTTONS (ADD, DELETE, FORGOT & DICTIONARIES) **** 
    // *************************************************************

    ReaderHelpers.bindWordActionButtons({
        doclang: doclang,
        action_btns: TextActionBtns,
        controller: MediaController,
        get_source_id: function () {
            return $('[data-idtext]').attr('data-idtext');
        },
        text_is_shared: function () {
            const url_params = new URLSearchParams(window.location.search);
            return url_params.get('sh');
        },
        sentence_with_context: true,
        get_word_anchors: function () {
            return TextProcessor.getTextContainer().find("a.word");
        },
        get_new_word_attributes: function () {
            const there_is_audio = $("#audioplayer")[0]?.readyState > 0;
            return there_is_audio && next_phase == 5
                ? "style='display: none;'"
                : "";
        },
        on_add_success: function () {
            if (next_phase == 6 && $(".learning, .new, .forgotten").length > 0) {
                if (!hasAudioSource()) {
                    skipAudioPhases();
                } else {
                    setNewTooltip(document.getElementById('btn-next-phase'), 'Go to phase 4: Dictation');
                    next_phase = 4;
                }
            }
        }
    });


    // *************************************************************
    // ******************** ASSISTED LEARNING **********************
    // *************************************************************

    /**
     * Executes next phase of assisted learning
     * Triggered when the user presses the big blue button at the end
     * Phases: 1 = reading; 2 = listening; 3 = speaking; 4 = writing; 5 = reviewing
     */
    $("body").on("click", "#btn-next-phase", function() {
        const audio_is_loaded = hasAudioSource();
        const btn_next_phase = document.getElementById('btn-next-phase');
        let $msg_phase = $("#alert-box-phase");

        if (next_phase < 6 && !audio_is_loaded) {
            skipAudioPhases();
        }

        switch (next_phase) {
            case 2:
                scrollToPageTop();

                next_phase++;

                $msg_phase.html(`
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <h5 class="alert-heading">🎧 Phase 2: Listening</h5>
                    <p class="small mb-2">Shift your focus from meaning to <strong>auditory patterns</strong> 
                    and phonetic details.</p>
                    <ul class="small ps-3">
                        <li><strong>Analyze:</strong> Focus on how individual words blend together in natural speech.</li>
                        <li><strong>Slow Down:</strong> If the pace feels too fast, reduce the playback speed to
                        hear distinct sounds.</li>
                        <li><strong>Identify:</strong> Try to spot the difference between the written spelling and
                        the actual <a href="https://en.wikipedia.org/wiki/Phonology" class="alert-link">phonetic</a>
                        output.</li>
                    </ul>
                `);

                setNewTooltip(document.getElementById('btn-next-phase'), 'Go to phase 3: Speaking');

                MediaController.playFromBeginning();
                break;
            case 3:
                scrollToPageTop();

                next_phase++;
                
                setNewTooltip(btn_next_phase, 
                    'Go to phase 4: Dictation');

                $msg_phase.html(`
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <h5 class="alert-heading">🎙️ Phase 3: Speaking</h5>
                    <p class="small mb-2">Focus on <strong>vocalizing</strong> the language to build muscle memory
                    and fluency.</p>
                    <ul class="small ps-3">
                        <li><strong>Listen:</strong> Pay close attention to the native rhythm, intonation, and stress
                        patterns.</li>
                        <li><strong>Shadow:</strong> Read the text aloud while trying to <strong>emulate</strong> the
                        exact pronunciation.</li>
                        <li><strong>Adjust:</strong> Use the speed controls to slow down the audio if you need to
                        catch complex sounds.</li>
                    </ul>
                `);

                MediaController.playFromBeginning();
                break;
            case 4:
                scrollToPageTop();

                if ($(".learning, .new, .forgotten").length == 0) {
                    setNewTooltip(btn_next_phase, 
                        'Finish & Save - Will skip phase 5 (Review): no underlined words');
                    
                    next_phase = 6;
                } else {
                    next_phase++;
                    setNewTooltip(btn_next_phase, 'Go to phase 5: Review');
                }

                $msg_phase.html(`
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <h5 class="alert-heading">✍️ Phase 4: Dictation</h5>
                    <p class="small">Listen to the audio and fill in the blanks as accurately as possible.</p>
                    <p class="small mb-2">Auto-pause while typing is on by default. You can turn it off in the audio controls.</p>
                    <div class="small bg-light p-2 rounded border">
                        <strong>Controls:</strong> 
                        <kbd>1</kbd> Back 3s | 
                        <kbd>2</kbd> Play/Pause | 
                        <kbd>3</kbd> Forward 3s
                    </div>
                `);

                MediaController.setSpeed(0.75);
                toggleDictation();
                break;
            case 5:
                scrollToPageTop();

                next_phase++;

                setNewTooltip(btn_next_phase, 'Finish & Save');

                $msg_phase.html(`
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <h5 class="alert-heading">🚀 Phase 5: Review</h5>
                    <p class="small mb-2">This is the most <strong>critical step</strong> for long-term memory.</p>
                    <ul class="small ps-3">
                        <li><strong>Review:</strong> Review all underlined words. Try to recall their meanings and
                        pronunciations.</li>
                        <li><strong>Correct:</strong> Pay attention to misspellings shown in 
                        <span class="text-danger">[red brackets]</span> (if any).</li>
                        <li><strong>Practice:</strong> Try to create original sentences using words underlined in green
                        to turn <a href="https://en.wiktionary.org/wiki/passive_vocabulary"
                        class="alert-link">passive</a> knowledge into <a href="https://en.wiktionary.org/wiki/active_vocabulary" class="alert-link">active</a> skill.</li>
                    </ul>
                `);

                toggleDictation();
                break;
            case 6:
                updateWordsLearningStatus();
                break;
            default:
                break;
        }
    }); 


    // *************************************************************
    // ****************** SAVE TEXT & FINISH ***********************
    // *************************************************************

    /**
     * Finished studying this text. Archives text & saves new status of words/phrases
     * Triggered when the user presses the big green button at the end of the review
     */
    $("body").on("click", "#btn-save-text", updateWordsLearningStatus);

    /**
     * Archives text (only if necessary) and updates status of all underlined words & phrases
     */
    async function updateWordsLearningStatus() {
        let archive_text = true;
        const is_shared = $("#is_shared").length > 0;
        let text_ids = [$("#text-container").attr("data-IdText")];

        if (is_shared) {
            text_ids = undefined;
            archive_text = undefined;
        }

        try {
            const save_data = await ReaderHelpers.saveReviewProgress({
                words_selector: "#text .reviewing",
                text_ids: text_ids,
                archive_text: archive_text
            });

            window.parent.show_confirmation_dialog = false;
            ReaderHelpers.submitTextStatsForm({
                gems_earned: save_data.gems_earned,
                is_shared: $("#is_shared").length
            });
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
    } 


    // *************************************************************
    // ************************* AUDIO ***************************** 
    // *************************************************************

    /**
     * Changes the position of audio player controls (sticky or initial)
     */
    $("#btn-toggle-audio-player-controls").on("click", function () {
        const $container = $("#audioplayer-container");
        const $btn = $(this);
        const onTitle = "Hide audio controls while scrolling";
        const offTitle = "Keep audio controls visible while scrolling";

        const isStatic = $container.css("position") === "static";

        if (isStatic) {
            $container.css("position", "sticky");
            // $btn.removeClass("btn-outline-primary")
                // .addClass("btn-primary");
            $btn.addClass("active");
            setNewTooltip($btn[0], onTitle);
        } else {
            $container.css("position", "static");
            // $btn.removeClass("btn-primary")
            //     .addClass("btn-outline-primary");
            $btn.removeClass("active");
            setNewTooltip($btn[0], offTitle);
        }
    });

    /**
     * Tries to reload audio
     * When audio fails to load, an error message is shown with a link to reload audio
     * This event is triggered when the user clicks this link
     */
    $doc.on("click", "#retry-audio-load", function(e) {
        e.preventDefault();
        $("#alert-box-audio").addClass("d-none");
        $("#audioplayer-loader").removeClass("d-none");
        loadAudio();
    }); 

    /**
     * Helper function to skip audio phases
     */
    function skipAudioPhases() {
        const btn_next_phase = document.getElementById('btn-next-phase');

        if (!hasAudioSource()) {
            if ($(".learning, .new, .forgotten").length == 0) {
                setNewTooltip(btn_next_phase, 
                    'Finish & Save - Will skip some phases: no audio detected & no underlined words');
        
                next_phase = 6;
            } else {
                setNewTooltip(btn_next_phase, 
                    'Go to phase 5: Review - Will skip some phases: no audio detected');
        
                next_phase = 5;
            }    
        }
    } 

    /**
     * Loads audio for text
     */
    async function loadAudio() {
        let $audio_player = $("#audioplayer");
        let audio_player_src = $("#audio-source").attr('src');
        const playlist_src = $audio_player.data("playlistSrc");
        // if audio player is found, src is empty and not an ebook...
        if ($audio_player.length > 0 && audio_player_src === '' 
            && !$('#readerpage > :first').is('#navigation')
            && (!playlist_src || String(playlist_src).trim() === '')) {
            const txt = $("#text").text();

            try {
                const response = await fetch("/ajax/fetchaudiostream.php", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ text: txt, langiso: doclang })
                });

                if (!response.ok) {
                     if (response.status == 403) {
                        throw new Error('Audio streaming limit reached. Try again tomorrow.');
                    } 
                    throw new Error(`HTTP error: ${response.status}`);
                }

                const data = await response.json();

                if (data.payload.error != null || !data.payload.response) {
                    throw new Error(data.payload.error || 'No audio response received');
                }

                $("#audio-source").attr("src", data.payload.response);
                $audio_player[0].load();
                $("#audioplayer-loader").addClass("d-none");
                $("#audioplayer-container").removeClass("d-none");

                setNewTooltip(document.getElementById('btn-next-phase'), 
                    'Go to phase 2: Listening');

                next_phase = 2;
            } catch (error) {
                console.error(error);
                 $("#audioplayer-loader").addClass("d-none");
                 $("#alert-box-audio")
                    .removeClass("d-none")
                    .empty()
                    .append(error.message || `Failed to load audio. <a href="#"
                        class="alert-link" id="retry-audio-load">Try again?</a>`);
                skipAudioPhases();
            }
        }
    } 

    /**
     * Shows dialog message reminding users to save changes before leaving
     */
    ReaderHelpers.bindBeforeUnloadWarning(function () {
        return window.parent.show_confirmation_dialog;
    }); 
});
