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
    Dictionaries.fetchURIs(); // get dictionary & translator URIs

    /**
     * Fetches user words/phrases from the server and underlines them in the text, but only if this
     * is a simple text, not an ebook
     */
    async function underlineText() {
        if ($('#text-container').data('type') == 'text') {
            try {
                const form_data = new URLSearchParams({ txt: $('#text').text() });
                const response = await fetch("/ajax/getuserwords.php", {
                    method: "POST",
                    body: form_data
                });

                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.error_msg || 'Failed to get user words for underlining');
                } 
                
                $('#text').html(TextUnderliner.apply(data.payload, doclang));
                TextProcessor.updateAnchorsList();
            } catch (error) {
                console.error(error);
                alert(`Oops! ${error.message}`);
            } finally {
                skipAudioPhases();
            }
        }
    } // end underlineText()

    // *************************************************************
    // ****************** AUDIO/VIDEO CONTROLLER ******************* 
    // *************************************************************
    let MediaController;

    if (typeof AudioController !== 'undefined') {
        MediaController = AudioController;
    } else if (typeof VideoController !== 'undefined') {
        MediaController = VideoController;
    } else {
        // Handle the case where neither controller is available
        console.log('No suitable media controller found.');
    }

    if (MediaController) {
        WordSelection.setupEvents({
            actionBtns: TextActionBtns,
            controller: MediaController,
            linkBuilder: LinkBuilder.forTranslationInText
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

    /**
     * Adds word to user db
     * Triggered when user clicks the "Add" button in the action popup
     */
    $doc.on("click", "#btn-add, #btn-forgot", async function(e) {
        const $action_button = $(this);
        ActionBtns.setActionMenuLoading($action_button);
        const $selword = WordSelection.get();
        const is_phrase = $selword.length > 1 ? 1: 0;
        const sel_text = $selword.text();
        const audio_is_loaded = hasAudioSource();
        const url_params = new URLSearchParams(window.location.search);
        const text_is_shared = url_params.get('sh');

        try {
            const form_data = new URLSearchParams({
                word: sel_text,
                is_phrase: is_phrase,
                source_id: $('[data-idtext]').attr('data-idtext'),
                text_is_shared: text_is_shared,
                sentence: SentenceExtractor.extractSentence($selword, true)
            });

            const response = await fetch("/ajax/addword.php", {
                method: "POST",
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to add word.');
            }

            const no_prev_underlined_words = $(".learning, .new, .forgotten").length == 0;
            const there_is_audio = $("#audioplayer")[0]?.readyState > 0;
            const hide_elem_if_dictation_is_on = there_is_audio && next_phase == 5
                ? "style='display: none;'"
                : "";

            // underline word or phrase
            if (is_phrase) {
                // if it's a phrase
                const word_count = $selword.filter(".word").length;

                // build filter based on first word of the phrase
                let $filterphrase = TextProcessor.getTextContainer()
                    .find("a.word")
                    .filter(function() {
                        return (
                            $(this)
                                .text()
                                .toLowerCase() ===
                            $selword
                                .eq(0)
                                .text()
                                .toLowerCase()
                        );
                    });

                // loop through the filter and underline all instances of the phrase
                $filterphrase.each(function() {
                    let $lastword = $(this)
                        .nextAll("a.word")
                        .slice(0, word_count - 1)
                        .last();
                    let $phrase = $(this)
                        .nextUntil($lastword)
                        .addBack()
                        .next("a.word")
                        .addBack();

                    if (
                        $phrase.text().toLowerCase() ===
                        sel_text.toLowerCase()
                    ) {
                        $phrase.wrapAll(
                            `<a class="word reviewing new" ${hide_elem_if_dictation_is_on}></a>`
                        );

                        $phrase.contents().unwrap();
                    }
                });
            } else {
                // if it's a word
                // build filter with all the instances of the word in the text
                let $filterword = TextProcessor.getTextContainer()
                    .find("a.word")
                    .filter(function() {
                        return (
                            $(this)
                                .text()
                                .toLowerCase() === sel_text.toLowerCase()
                        );
                    });

                // loop through the filter and underline all instances of the word
                $filterword.each(function() {
                    let $word = $(this);
                    if ($word.is(".new, .learning, .learned, .forgotten")) {
                        $word.wrap(
                            `<a class='word reviewing forgotten' ${hide_elem_if_dictation_is_on}></a>`
                        );
                    } else {
                        $word.wrap(
                            `<a class='word reviewing new' ${hide_elem_if_dictation_is_on}></a>`
                        );
                    }
                });

                $filterword.contents().unwrap();
            }

            // if there were no previous words underlined, therefore phases 2 & 3 were off,
            // when user adds his first new word, activate these phases
            if (next_phase == 6 && no_prev_underlined_words) {
                if (!audio_is_loaded) {
                    skipAudioPhases();
                } else {
                    let elem = document.getElementById('btn-next-phase');
                    let title = 'Go to phase 4: Dictation';
                    setNewTooltip(elem, title);

                    next_phase = 4;
                }
            }

            TextProcessor.updateAnchorsList();
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        } finally {
            ActionBtns.clearActionMenuLoading($action_button);
            TextActionBtns.hide();
            MediaController.resume();
        }
    }); // end #btn-add.on.click

    /**
     * Removes word from db
     * Triggered when user clicks the "Remove" button in the action popup
     */
    $doc.on("click", "#btn-remove", async function() {
        const $action_button = $(this);
        ActionBtns.setActionMenuLoading($action_button);
        const $selword = WordSelection.get();
        
        try {
            // First ajax call to remove the word
            const remove_word_response = await fetch("/ajax/removeword.php", {
                method: "POST",
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ word: $selword.text().toLowerCase() })
            });

            if (!remove_word_response.ok) { throw new Error(`HTTP error: ${remove_word_response.status}`); }

            const remove_word_data = await remove_word_response.json();
            
            if (!remove_word_data.success) {
                throw new Error(remove_word_data.error_msg || 'Failed to remove word.');
            }
            
                            let $filter = TextProcessor.getTextContainer()
                    .find("a.word")
                    .filter(function() {
                        return (
                            $(this)
                                .text()
                                .toLowerCase() === $selword.text().toLowerCase()
                        );
                    });
    
                // Second ajax call to get user words and re-underline
                const get_user_words_response = await fetch("/ajax/getuserwords.php", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ txt: $selword.text() })
                });
    
                if (!get_user_words_response.ok) { throw new Error(`HTTP error: ${get_user_words_response.status}`); }
                
                const get_user_words_data = await get_user_words_response.json();
                
                if (!get_user_words_data.success) {
                    throw new Error(get_user_words_data.error_msg || 'Failed to get user words for re-underlining.');
                }
                
                let $result = $(TextUnderliner.apply(get_user_words_data.payload, doclang));                    
                let $cur_filter = {};
                let cur_word = /""/;

                $filter.each(function() {
                    $cur_filter = $(this);

                    $result.filter(".word").each(function(key) {
                        if (TextProcessor.langHasNoWordSeparators(doclang)) {
                            cur_word = new RegExp(
                                "(?<![^])" + $(this).text() + "(?![$])",
                                "iug"
                            ).exec($cur_filter.text());                            } 
                        else {
                            cur_word = new RegExp(
                                "(?<![\\p{L}|^])" + $(this).text() + "(?![\\p{L}|$])",
                                "iug"
                            ).exec($cur_filter.text());
                        }

                        $(this).text(cur_word);
                        
                        // check if any word marked by PHP as .learning should be marked as .new instead
                        const word = $(this).text().toLowerCase();
                        const user_word = get_user_words_data.payload.user_words.find(function (element) {
                            return element.word == word;    
                        });

                        if (user_word !== undefined) {
                            if (user_word.status == 2) {
                                $(this).removeClass("learning").addClass("new");                                    
                            } else if (user_word.status == 3) {
                                $(this).removeClass("learning").addClass("forgotten");
                            }
                        }
                    });
                    
                    $cur_filter.replaceWith($result.clone());
                });
                TextProcessor.updateAnchorsList();

        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        } finally {
            ActionBtns.clearActionMenuLoading($action_button);
            TextActionBtns.hide();
            MediaController.resume();
        }
    }); // end #btn-remove.on.click


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
    }); // end #btn-next-phase.on.click


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
        // build array with underlined words
        let unique_words = [];
        let id = [];
        let word = "";
        let archive_text = true;
        const is_shared = $("#is_shared").length > 0;
        let gems_earned = 0;

        $("#text").find(".reviewing").each(function() {
            word = $(this)
                .text()
                .toLowerCase();
            if ($.inArray(word, unique_words) == -1) {
                unique_words.push(word);
            }
        });

        id.push($("#text-container").attr("data-IdText")); // get text ID

        if (is_shared) {
            id = undefined;
            archive_text = undefined;
        }

        try {
            const update_words_response = await fetch("/ajax/updatewords.php", {
                method: "POST",
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    words: JSON.stringify(unique_words),
                    textIDs: JSON.stringify(id),
                    archivetext: archive_text
                })
            });

            if (!update_words_response.ok) { throw new Error(`HTTP error: ${update_words_response.status}`); }

            const update_words_data = await update_words_response.json();

            if (!update_words_data.success) {
                throw new Error(update_words_data.error_msg || 'Failed to update words status.');
            }
            
            // update user score (gems)
                const review_data = {
                    words: {
                        new: getUniqueElements('.reviewing.new'),
                        learning: getUniqueElements('.reviewing.learning'),
                        forgotten: getUniqueElements('.reviewing.forgotten')
                    },
                    texts: { reviewed: 1 }
                };

                const update_user_score_response = await fetch("ajax/updateuserscore.php", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        'review_data': JSON.stringify(review_data)
                    })
                });

                if (!update_user_score_response.ok) { throw new Error(`HTTP error: ${update_user_score_response.status}`); }

                const update_user_score_data = await update_user_score_response.json();

                if (!update_user_score_data.success) {
                    throw new Error(update_user_score_data.error_msg || 'Failed to update user score.');
                }

                gems_earned = update_user_score_data.gems_earned;
                window.parent.show_confirmation_dialog = false;
                const url = "/textstats";
                const total_words =
                    Number($(".word").length) + Number($(".phrase").length);
                const form = $(
                    '<form action="' +
                        url +
                        '" method="post">' +
                        '<input type="hidden" name="created" value="' +
                        $(".reviewing.new").length +
                        '" />' +
                        '<input type="hidden" name="learning" value="' +
                        $(".reviewing.learning").length +
                        '" />' +
                        '<input type="hidden" name="learned" value="' +
                        $(".learned").length +
                        '" />' +
                        '<input type="hidden" name="forgotten" value="' +
                        $(".reviewing.forgotten").length +
                        '" />' +
                        '<input type="hidden" name="total" value="' +
                        total_words +
                        '" />' +
                        '<input type="hidden" name="gems_earned" value="' +
                        gems_earned +
                        '" />' +
                        '<input type="hidden" name="is_shared" value="' +
                        $("#is_shared").length +
                        '" />' +
                        "</form>"
                );
                $("body").append(form);
                form.trigger( "submit" );
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
    } // end #updateWordsLearningStatus


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
    }); // end #retry-audio-load.on.click

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
    } // end skipAudioPhases

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
    } // end loadAudio

    /**
     * Shows dialog message reminding users to save changes before leaving
     */
    $(window).on("beforeunload", function () {
        if (window.parent.show_confirmation_dialog) {
            return 'Press Save before you go or your changes will be lost.';
        }
    }); // end window.on.beforeunload
});
