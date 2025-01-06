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
    // word selection variables
    let long_press_timer;
    let has_long_pressed = false;
    let start_x, start_y;
    let start_index = -1;
    let current_index = -1;
    let $selword = null; // jQuery object with selected word/phrase

    // dictionary/translator variables
    let dictionary_URI = "";
    let img_dictionary_URI = "";
    let translator_URI = "";

    // assisted learning
    let next_phase = 2; // next phase of the learning cycle

    // HTML selectors
    const doclang = $("html").attr("lang");
    const $doc = $(parent.document);
    const $text_container = $doc.find('#text');
    
    // configuration to show confirmation dialog on close
    window.parent.show_confirmation_dialog = true;

    // initial AJAX calls
    // loadAudio();
    underlineText(); // underline text with user words/phrases
    fetchDictionaryURIs(); // get dictionary & translator URIs

    /**
     * Fetches user words/phrases from the server and underlines them in the text, but only if this
     * is a simple text, not an ebook
     */
    function underlineText() {
        if ($('#text-container').data('type') == 'text') {
            $.ajax({
                type: "POST",
                url: "/ajax/getuserwords.php",
                data: { txt: $('#text').text() },
                dataType: "json"
            })
            .done(function(data) {
                $('#text').html(TextProcessor.underlineWords(data, doclang, false));
                TextProcessor.updateAnchorsList();
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                console.log("There was an unexpected error trying to underline words in this text");  
            })
            .always(function() {
                skipAudioPhases();
            }); // end $.ajax
        }
    } // end underlineText()

    // *************************************************************
    // ******************* WORD/PHRASE SELECTION ******************* 
    // *************************************************************

    /**
     * Disables right click context menu
     */
    $doc.on("contextmenu",function(e){
        e.preventDefault();
        return false;
     }); // end document.contextmenu

    /**
     * On word click, assume single word selection
     */
    $doc.on('click', '#text .word', function (e) {
        if (!has_long_pressed) {
            $selword = $(this);
            TextProcessor.removeAllHighlighted();
            $selword.addClass('highlighted');
            hideActionButtonsPopUpToolbar();
            AudioController.pause(true);
            showActionButtonsPopUpToolbar();
        }
    });

    /**
     * On word mouse/touch down, potential start of selection
     */
    $doc.on('mousedown touchstart', '#text .word', function (e) {
        start_index = TextProcessor.getAnchorIndex($(this));
    });

    // Bind container mouse word selection events

    $doc.on('mousedown', '#text', function (e) {
        // only if selection starts with left mouse button
        if (e.originalEvent.which < 2) {
            startLongPress(e);
        } else if (e.originalEvent.which == 3) { // right click, open translator
            AudioController.pause(false);
            $selword = $(e.target);
            openInNewTab(buildTextTranslationLink(translator_URI, $selword));
        }
    });
    $doc.on('mousemove', '#text', function (e) {
        onPointerMove(e);
    });
    $doc.on('mouseup', '#text', function (e) {
        onPointerUp();
    });

    // Bind container touch word selection events

    $doc.on('touchstart', '#text', function (e) {
        const touch = e.originalEvent.touches[0];
        startLongPress(touch);
    });
    $doc.on('touchmove', '#text', function (e) {
        const touch = e.originalEvent.touches[0];
        onPointerMove(touch);
    });
    $doc.on('touchend', '#text', function (e) {
        onPointerUp();
    });

    /**
     * Starts the long-press timer. If the timer completes without interruption, the multi-selection mode is activated.
     * @param {Event} e - The pointer event that triggered the long-press action.
     */
    function startLongPress(e) {
        has_long_pressed = false;
        start_x = e.pageX;
        start_y = e.pageY;

        long_press_timer = setTimeout(function () {
            has_long_pressed = true;
            AudioController.pause(true);
        }, 500);
    } // end startLongPress()

    /**
     * Cancels the long-press timer and resets relevant state variables.
     */
    function cancelLongPress() {
        clearTimeout(long_press_timer);
        start_x = null;
        start_y = null;
    } // end cancelLongPress()

    /**
     * Checks whether the pointer has moved significantly enough to be considered scrolling rather than pressing.
     * @param {Event} e - The pointer event to evaluate.
     * @returns {boolean} - True if the pointer has moved beyond a predefined threshold, false otherwise.
     */
    function pointerMovedEnough(e) {
        if (start_x == null || start_y == null) return false;
        const threshold = 10;
        const dx = e.pageX - start_x;
        const dy = e.pageY - start_y;
        return (Math.abs(dx) > threshold || Math.abs(dy) > threshold);
    } // end pointerMovedEnough()

    /**
     * Continuously called during pointer movement. If multi-selection mode is active, highlights text from 
     * the starting index to the anchor element currently under the pointer.
     * @param {Event} e - The pointer event containing movement details.
     */
    function highlightCurrent(e) {
        if (!has_long_pressed || start_index < 0) return;

        // Determine which anchor we are over
        const el = document.elementFromPoint(e.clientX, e.clientY);
        // If the element is text node or something else, climb up to 'a'
        const $target_anchor = $(el).closest('a');
        if ($target_anchor.length) {
            current_index = TextProcessor.getAnchorIndex($target_anchor);

            // First clear existing highlights
            TextProcessor.removeAllHighlighted();

            // Then highlight from start_index to current_index
            TextProcessor.addHighlightToSelection(start_index, current_index);
        }
    } // end highlightCurrent()

    /**
     * Finalizes the selection when the pointer is released, if multi-selection mode is active.
     * Also resets relevant state variables.
     */
    function onPointerUp() {
        if (has_long_pressed && start_index >= 0 && current_index >= 0) {
            $selword = TextProcessor.getHighlightedTextObj(start_index, current_index);
            showActionButtonsPopUpToolbar();
        }

        // Clear state
        cancelLongPress();
        has_long_pressed = false;
        start_index = -1;
        current_index = -1;
    } // end onPointerUp()

    /**
     * Handles pointer movement within the container. Cancels the long-press timer if scrolling is detected.
     * If multi-selection mode is active, updates the highlighted text selection.
     * @param {Event} e - The pointer event to process.
     */
    function onPointerMove(e) {
        if (pointerMovedEnough(e)) {
            cancelLongPress();
        }
        if (has_long_pressed) {
            highlightCurrent(e);
        }
    } // end onPointerMove()

    /**
     * Removes selection when user clicks in white-space
     * @param {Event} e - The pointer event to process.
     */
    $doc.on("mouseup touchend", function (e) {
        let $action_btns = $("#action-buttons");

        // Only proceed if action buttons are visible
        if ($action_btns.is(':visible')) {
            let is_word_clicked = $(e.target).is(".word");
            let is_btn_clicked = $(e.target).closest('.btn').length > 0;
            let is_navigation = $(e.target).closest('.offcanvas').length > 0;
            let is_modal = $(e.target).closest('.modal').length > 0;

            // Check if click is not on a word and outside action buttons
            if (!is_word_clicked && !is_btn_clicked && !is_navigation && !is_modal) {
                e.stopPropagation();
                TextProcessor.removeAllHighlighted(); // Remove highlight

                // Hide toolbar and resume audio
                hideActionButtonsPopUpToolbar();
                AudioController.resume();
            }
        }
    }); // end $document.on.mouseup

    // *************************************************************
    // **** ACTION BUTTONS (ADD, DELETE, FORGOT & DICTIONARIES) **** 
    // *************************************************************

    /**
     * Fetches dictionary and translator URIs from the server via an AJAX GET request.
     * @returns {jqXHR} A jQuery promise object that resolves with the JSON response or rejects with error information.
     */
    function fetchDictionaryURIs() {
        
        $.ajax({
            url: "/ajax/getdicuris.php",
            type: "GET",
            dataType: "json"
        }).done(function(data) {
            if (data.error_msg == null) {
                dictionary_URI     = data.dictionary_uri;
                img_dictionary_URI = data.img_dictionary_uri;
                translator_URI     = data.translator_uri;
            }
        }); // end $.ajax
    }
    
    /**
     * Shows pop up toolbar when user clicks a word
     */
    function showActionButtonsPopUpToolbar() {
        setWordActionButtons($selword, false);

        const base_uris = {
            dictionary: dictionary_URI,
            img_dictionary: img_dictionary_URI,
            translator: translator_URI
        };

        $("body").disableScroll();
        setDicActionButtonsClick($selword, base_uris, 'text');
        showActionButtons($selword);
    } // end showActionButtonsPopUpToolbar()

    /**
     * Hides actions pop up toolbar
     */
    function hideActionButtonsPopUpToolbar() {
        $("body").enableScroll();
        hideActionButtons();
    } // end hideActionButtonsPopUpToolbar()

    /**
     * Adds word to user db
     * Triggered when user clicks the "Add" button in the action popup
     */
    $doc.on("click", "#btn-add, #btn-forgot", function(e) {
        const is_phrase = $selword.length > 1 ? 1: 0;
        const sel_text = $selword.text();
        const audio_is_loaded = $("#audioplayer").find("source").attr("src") != "";
        const url_params = new URLSearchParams(window.location.search);
        const text_is_shared = url_params.get('sh');

        // add selection to "words" table
        $.ajax({
            type: "POST",
            url: "/ajax/addword.php",
            data: {
                word: sel_text,
                is_phrase: is_phrase,
                source_id: $('[data-idtext]').attr('data-idtext'),
                text_is_shared: text_is_shared,
                sentence: getTextSentence($selword)
            }
        })
            .done(function() {
                const no_prev_underlined_words = $(".learning, .new, .forgotten").length == 0;
                const there_is_audio = $("#audioplayer")[0]?.readyState > 0;
                const hide_elem_if_dictation_is_on = there_is_audio && next_phase == 5
                    ? "style='display: none;'"
                    : "";

                // if successful, underline word or phrase
                if (is_phrase) {
                    // if it's a phrase
                    const word_count = $selword.filter(".word").length;

                    // build filter based on first word of the phrase
                    let $filterphrase = $text_container
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
                                "<a class='word reviewing new' " +
                                hide_elem_if_dictation_is_on + "></a>"
                            );

                            $phrase.contents().unwrap();
                        }

                        if ($(e.target).is("#btn-add")) {
                            TextProcessor.updateAnchorsList();
                        }
                    });
                } else {
                    // if it's a word
                    // build filter with all the instances of the word in the text
                    let $filterword = $text_container
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
                                "<a class='word reviewing forgotten' " +
                                hide_elem_if_dictation_is_on + "></a>"
                            );
                        } else {
                            $word.wrap(
                                "<a class='word reviewing new' " +
                                hide_elem_if_dictation_is_on + "></a>"
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
                        let title = 'Go to phase 4: Writing (be patient, may take a while to load depending on text length)';
                        setNewTooltip(elem, title);

                        next_phase = 4;
                    }
                }
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert(
                    "Oops! There was an error adding this word or phrase to the database."
                );
            });

        hideActionButtonsPopUpToolbar();
        AudioController.resume();
    }); // end #btn-add.on.click

    /**
     * Removes word from db
     * Triggered when user clicks the "Remove" button in the action popup
     */
    $doc.on("click", "#btn-remove", function() {
        $.ajax({
            type: "POST",
            url: "/ajax/removeword.php",
            data: {
                word: $selword.text()
            }
        })
            .done(function() {
                let $filter = $text_container
                    .find("a.word")
                    .filter(function() {
                        return (
                            $(this)
                                .text()
                                .toLowerCase() === $selword.text().toLowerCase()
                        );
                    });

                // ajax call to underline text
                $.ajax({
                    type: "POST",
                    url: "/ajax/getuserwords.php",
                    data: { txt: $selword.text() },
                    dataType: "json"
                })
                .done(function(data) {
                    // if everything went fine, remove the underlining and underline once again the whole selection
                    // also, the case of the word/phrase in the text has to be respected
                    // for phrases, we need to make sure that new underlining is added for each word
                    const there_is_audio = $("#audioplayer")[0]?.readyState > 0;
                    const hide_elem = there_is_audio > 0 && next_phase == 5;
                    let $result = $(TextProcessor.underlineWords(data, doclang, hide_elem));                    
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
                            const user_word = data.user_words.find(function (element) {
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
                });
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert(
                    "Oops! There was an error removing the word from the database."
                );
            });
        
        hideActionButtonsPopUpToolbar();
        AudioController.resume();
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
        const audio_is_loaded = $("#audioplayer").find("source").attr("src") != "";
        const btn_next_phase = document.getElementById('btn-next-phase');
        let $msg_phase = $("#alert-box-phase");

        if (next_phase < 6 && !audio_is_loaded) {
            skipAudioPhases();
        }

        switch (next_phase) {
            case 2:
                scrollToPageTop();

                next_phase++;

                $msg_phase
                    .html(
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                            + '<h5 class="alert-heading">Assisted learning - Phase 2: Listening</h5>'
                            + '<span class="small">Pay attention to the pronunciation of each word. You can slow down '
                            + 'the audio if necessary.</span>'
                    );

                setNewTooltip(document.getElementById('btn-next-phase'), 'Go to phase 3: Speaking');

                AudioController.playFromBeginning();
                break;
            case 3:
                scrollToPageTop();

                next_phase++;
                
                setNewTooltip(btn_next_phase, 
                    'Go to phase 4: Writing (be patient, may take a while to load depending on text length)');

                $msg_phase.html(
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                    + '<h5 class="alert-heading">Assisted learning - Phase 3: Speaking</h5>'
                    + '<span class="small">Read the text out loud and try to emulate the pronunciation of each word as '
                    + 'you listen to the audio. You can slow it down if necessary.</span>'
                );

                AudioController.playFromBeginning();
                break;
            case 4:
                scrollToPageTop();

                if ($(".learning, .new, .forgotten").length == 0) {
                    setNewTooltip(btn_next_phase, 
                        'Finish & Save - Will skip phase 5 (reviewing): no underlined words');
                    
                    next_phase = 6;
                } else {
                    next_phase++;
                    setNewTooltip(btn_next_phase, 'Go to phase 5: Reviewing');
                }

                $msg_phase
                    .html(
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                        + '<h5 class="alert-heading">Assisted learning - Phase 4: Writing</h5><span class="small">'
                        + 'Fill in the blanks as you listen to the dictation. To toggle audio playback press '
                        + '<kbd>2</kbd>. To rewind or fast-forward 5 seconds, use <kbd>1</kbd> and <kbd>3</kbd>. '
                        + 'You can also click on the hint beside any misspelled word to include it in '
                        + 'your word list. We recommend you do this once the dictation is complete and you are '
                        + 'reviewing your mistakes.</span>'
                    );

                toggleDictation();
                break;
            case 5:
                scrollToPageTop();

                next_phase++;

                setNewTooltip(btn_next_phase, 'Finish & Save');

                $msg_phase
                    .html(
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                        + '<h5 class="alert-heading">Assisted learning - Phase 5: Reviewing</h5><span class="small"><u>'
                        + 'This is the most <a href="https://en.wikipedia.org/wiki/Testing_effect" class="alert-link" '
                        + 'target="_blank" rel="noopener noreferrer">critical phase</a> for long-term language '
                        + 'acquisition.</u><br>Review all the underlined words, even the ones with green underlining. '
                        + 'Make an effort to remember their meaning and pronunciation, while also paying attention to '
                        + 'their spelling. Speak out alternative sentences using these words. The latter is essential '
                        + 'to turn your <a href="https://en.wiktionary.org/wiki/passive_vocabulary" class="alert-link" '
                        + 'target="_blank" rel="noopener noreferrer">passive vocabulary</a> into '
                        + '<a href="https://en.wiktionary.org/wiki/active_vocabulary" class="alert-link" '
                        + 'target="_blank" rel="noopener noreferrer">active vocabulary</a>.</span>'
                    );

                toggleDictation();
                break;
            case 6:
                archiveTextAndSaveWords();
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
    $("body").on("click", "#btn-save-text", archiveTextAndSaveWords);

    /**
     * Archives text (only if necessary) and updates status of all underlined words & phrases
     */
    function archiveTextAndSaveWords() {
        // build array with underlined words
        let unique_words = [];
        let id = [];
        let word = "";
        let archive_text = true;
        const is_shared = $("#is_shared").length > 0;
        let gems_earned = 0;

        $(".learned, .learning").each(function() {
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

        $.ajax({
            type: "POST",
            url: "/ajax/archivetext.php",
            data: {
                words: unique_words,
                textIDs: JSON.stringify(id),
                archivetext: archive_text
            }
        })
            .done(function(data) {
                if (data.error_msg == null) {
                    // update user score (gems)
                    const review_data = {
                        words: {
                            new: getUniqueElements('.reviewing.new'),
                            learning: getUniqueElements('.reviewing.learning'),
                            forgotten: getUniqueElements('.reviewing.forgotten')
                        },
                        texts: { reviewed: 1 }
                    };

                    $.ajax({
                        type: "post",
                        url: "ajax/updateuserscore.php",
                        data: review_data
                    })
                    .done(function(data) {
                        // show text review stats
                        if (data.error_msg == null) {
                            gems_earned = data.gems_earned;
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
                                    '<input type="hidden" name="reviewed" value="' +
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
                            form.submit();
                        } else {
                            alert("Oops! There was an unexpected error updating user score.");
                        }
                    })
                    .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("Oops! There was an unexpected error updating user score.");
                    });
                } else {
                    alert("Oops! There was an error unexpected error saving this text.");
                }
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Oops! There was an error unexpected error saving this text.");
            });
    } // end #archiveTextAndSaveWords


    // *************************************************************
    // ************************* AUDIO ***************************** 
    // *************************************************************

    /**
     * Changes the position of audio player controls (sticky or initial)
     */
    $("#btn-toggle-audio-player-controls").on("click", function () {
        let audio_player_container = document.getElementById('audioplayer-container');
        let toggle_sticky_btn = document.getElementById('btn-toggle-audio-player-controls');
        const sticky_on_title = "Hide audio controls while scrolling";
        const sticky_off_title = "Keep audio controls visible while scrolling";
        
        if (audio_player_container.style.position === 'static') {
            audio_player_container.style.position = 'sticky';
            toggle_sticky_btn.style.backgroundColor = 'var(--bs-btn-bg)';
            setNewTooltip(toggle_sticky_btn, sticky_on_title);
        } else {
            audio_player_container.style.position = 'static';
            toggle_sticky_btn.style.backgroundColor = 'transparent';
            toggle_sticky_btn.style.borderColor = 'var(--bs-btn-border-color)';
            setNewTooltip(toggle_sticky_btn, sticky_off_title);
        }        
    }); // end #btn-toggle-audio-player-controls

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
        const audio_is_loaded = $("#audioplayer").find("source").attr("src") != "";
        const btn_next_phase = document.getElementById('btn-next-phase');

        if (!audio_is_loaded) {
            if ($(".learning, .new, .forgotten").length == 0) {
                setNewTooltip(btn_next_phase, 
                    'Finish & Save - Will skip some phases: no audio detected & no underlined words');
        
                next_phase = 6;    
            } else {
                setNewTooltip(btn_next_phase, 
                    'Go to phase 5: Reviewing - Will skip some phases: no audio detected');
        
                next_phase = 5;
            }    
        }
    } // end skipAudioPhases

    /**
     * Loads audio for text
     */
    function loadAudio() {
        let $audio_player = $("#audioplayer");
        let audio_player_src = $("#audio-source").attr('src');
        // if audio player is found, src is empty and not an ebook...
        if ($audio_player.length > 0 && audio_player_src === '' 
            && !$('#readerpage > :first').is('#navigation')) {
            const txt = $("#text").text();

            $.ajax({
                type: "POST",
                url: "/ajax/fetchaudiostream.php",
                data: { text: txt, langiso: doclang },
                dataType: "json"
            })
                .done(function(e) {
                    if (e.error != null || !e.response) {
                        $("#audioplayer-loader").addClass("d-none");
                        $("#alert-box-audio")
                            .removeClass("d-none")
                            .empty()
                            .append('There was an unexpected error trying to create audio from this text. '
                                + '<a class="alert-link" href="#" id="retry-audio-load">Try again</a> later.');
                        skipAudioPhases();
                        return false;
                    }
                    $("#audio-source").attr("src", e.response);
                    $audio_player[0].load();
                    $("#audioplayer-loader").addClass("d-none");
                    $("#audioplayer-container").removeClass("d-none");

                    setNewTooltip(document.getElementById('btn-next-phase'), 
                        'Go to phase 2: Listening');

                    next_phase = 2;
                    
                    return true;
                })
                .fail(function(xhr) {
                    if (xhr.status == 403) {
                        $("#audioplayer-loader").addClass("d-none");
                        $("#alert-box-audio")
                            .removeClass("d-none")
                            .empty()
                            .append('You have reached your audio streaming limit. Try again tomorrow.');
                    } else {
                        $("#audioplayer-loader").addClass("d-none");
                        $("#alert-box-audio")
                            .removeClass("d-none")
                            .empty()
                            .append('There was an unexpected error trying to create audio from this text. '
                                + '<a class="alert-link" href="#" id="retry-audio-load">Try again</a> later.');
                    }

                    skipAudioPhases();
                    return false;
                });
        } else {
            return false;
        }
    } // end loadAudio
});
