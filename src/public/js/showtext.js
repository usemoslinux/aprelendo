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
    let highlighting = false; // selection/highlighting mode
    let $sel_start, $sel_end; // Jquery object with the first & last elements of the selection 
    let start_sel_time, end_sel_time; // used in mobile devices to activate "word/phrase selection mode"
    let start_sel_pos_top; // used in mobile devices to activate "word/phrase selection mode"
    let swiping = false; // used in mobile devices to activate "word/phrase selection mode"
    let $selword = null; // jQuery object with selected word/phrase
    let dictionary_URI = "";
    let img_dictionary_URI = "";
    let translator_URI = "";
    let next_phase = 2; // next phase of the learning cycle
    let playing_audio = false;
    window.parent.show_confirmation_dialog = true; // confirmation dialog that shows when closing window
    let doclang = $("html").attr("lang");

    // $doc & $pagereader are used to make this JS code work when showing simple texts &
    // ebooks (which are displayed inside an iframe)
    let $doc = $(parent.document);
    let $pagereader = $doc.find('iframe[id^="epubjs"]');
    $pagereader = $pagereader.length > 0 ? $pagereader : $("html");
    
    loadAudio();

    // underline text
    if ($('#text-container').data('type') == 'text') {
        $.ajax({
            type: "POST",
            url: "/ajax/getuserwords.php",
            data: { txt: $('#text').text() },
            dataType: "json"
        })
        .done(function(data) {
            $('#text').html(underlineWords(data, doclang, false));
        })
        .fail(function(xhr, ajaxOptions, thrownError) {
            console.log("There was an unexpected error trying to underline words in this text");  
        })
        .always(function() {
            skipAudioPhases();
        }); // end $.ajax
    }

    /**
     * Disables right click context menu
     */
    $(document).on("contextmenu",function(e){
        e.preventDefault();
        return false;
     }); // end document.contextmenu

    /**
     * Word/Phrase selection start
     * @param {event object} e
     */
    $(document).on("mousedown touchstart", ".word", function(e) {
        e.stopPropagation();

        hideActionButtonsPopUpToolbar(false);

        if (e.which < 2) {
            // if left mouse button (e.which = 1) / touch (e.which = 0)...
            highlighting = true;
            $sel_start = $sel_end = $(this);
            if (e.type == "touchstart") {
                start_sel_time = new Date();
                start_sel_pos_top = $sel_start.offset().top - $(window).scrollTop();
            }
        } else if (e.which == 3) {
            if ($("#audioplayer").length) {
                pauseAudio();
                playing_audio = false;
            }
            $selword = $(this);
            window.open(buildTextTranslationLink(translator_URI, $selword), '_blank', 'noopener,noreferrer');
        }
    }); // end .word.on.mousedown/touchstart

    /**
     * Word/Phrase selection end
     * @param {event object} e
     */
    $(document).on("mouseup touchend", ".word", function(e) {
        e.stopPropagation();

        end_sel_time = new Date();
        
        if (e.type == "touchend") {
            if (!swiping) {
                highlighting = (end_sel_time - start_sel_time) > 1000;
            }
            $("html").enableScroll(); 
            swiping = false;
        }

        if (highlighting) {
            if (e.which < 2) {
                // if left mouse button / touch...
                highlighting = false;
                
                if ($sel_start === $sel_end) {
                    let $closest = $(this).closest('.learning, .learned, .forgotten');
                    if ($closest.length) {
                        $selword = $closest;
                    } else {
                        $selword = $(this);
                    }
                    $(".highlighted").removeClass("highlighted"); // remove previous highlighting
                    $selword.addClass("highlighted");
                }
                showActionButtonsPopUpToolbar();
            }
        }
        
        start_sel_time = end_sel_time = new Date();
    }); // end .word.mouseup/touchend

    /**
     * Word/Phrase selection
     * While user drags the mouse without releasing the mouse button
     * or while touches an elements an moves the pointer without releasing
     * Here we build the selected phrase & change its background color to gray
     * @param {event object} e
     */
    $(document).on("mouseover touchmove", ".word", function(e) {
        e.stopPropagation();

        end_sel_time = new Date();

        if (e.type == "touchmove") {
            const cur_sel_pos_top = $(this).offset().top - $(window).scrollTop();
            swiping = swiping || Math.abs(start_sel_pos_top - cur_sel_pos_top) > 0;

            if (!swiping) {
                highlighting = (end_sel_time - start_sel_time) > 1000;
            }
        } 

        if (highlighting) {
            if (e.type == "touchmove") {
                $("html").disableScroll();
            }
            
            $(".highlighted").removeClass("highlighted"); // remove previous highlighting

            $sel_end =
                e.type === "mouseover" ? $(this) : $(
                    document.elementFromPoint(
                        e.originalEvent.touches[0].clientX,
                        e.originalEvent.touches[0].clientY
                    )
                );

            if ($sel_end.isAfter($sel_start)) {
                $sel_start
                    .nextUntil($sel_end.next(), ".word")
                    .addBack()
                    .addClass("highlighted");
                $selword = $sel_start.nextUntil($sel_end.next()).addBack();
            } else {
                $sel_start
                    .prevUntil($sel_end.prev(), ".word")
                    .addBack()
                    .addClass("highlighted");
                $selword = $sel_end.nextUntil($sel_start.next()).addBack();
            }
        }
    }); // end .word.on.mouseover/touchmove

    // ajax call to get dictionary & translator URIs
    $.ajax({
        url: "/ajax/getdicuris.php",
        type: "GET",
        dataType: "json"
    }).done(function(data) {
        if (data.error_msg == null) {
            dictionary_URI     = data.dictionary_uri;
            img_dictionary_URI = data.img_dictionary_uri
            translator_URI     = data.translator_uri;
        }
    }); // end $.ajax

    /**
     * Adds word to user db
     * Triggered when user clicks the "Add" button in the action popup
     */
    $doc.on("click", "#btn-add, #btn-forgot", function() {
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
                    let $filterphrase = $pagereader
                        .contents()
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
                    });
                } else {
                    // if it's a word
                    // build filter with all the instances of the word in the text
                    let $filterword = $pagereader
                        .contents()
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

        hideActionButtonsPopUpToolbar(true);
        resumeAudio();
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
                let $filter = $pagereader
                    .contents()
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
                    let $result = $(underlineWords(data, doclang, hide_elem));
                    let $cur_filter = {};
                    let cur_word = /""/;

                    $filter.each(function() {
                        $cur_filter = $(this);

                        $result.filter(".word").each(function(key) {
                            if (langs_with_no_word_separator.includes(doclang)) {
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
                });
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert(
                    "Oops! There was an error removing the word from the database."
                );
            });
        
        hideActionButtonsPopUpToolbar(true);
        resumeAudio();
    }); // end #btn-remove.on.click

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

                playAudioFromBeginning();
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

                playAudioFromBeginning();
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

    /**
     * Changes the position of audio player controls (sticky or initial)
     */
    $("#btn-toggle-audio-player-controls").on("click", function () {
        let $audio_player_container = $('#audioplayer-container');
        if ($audio_player_container.css('position') == 'static') {
            $audio_player_container.css({
                'position': 'sticky'
            });
        } else {
            $audio_player_container.css({
                'position': 'static'
            });
        }
    }); // end #btn-toggle-audio-player-controls

    /**
     * Triggered when the action popup is closed
     */
    function resumeAudio() {
        let $audioplayer = $("#audioplayer");

        // Resumes playing if audio was paused when clicking on a word
        if (playing_audio && $audioplayer.length) {
            playAudio();
        }
    } // end resumeAudio()

    /**
     * Changes playback speed when user moves slider
     */
    $("body").on("input change", "#range-speed", function(e, data) {
        const cpbr = data !== undefined ? data.cpbr : parseFloat($(this).val()).toFixed(1);
        $(this).val(cpbr);
        $("#currentpbr").text(cpbr);    
        $("#audioplayer").prop("playbackRate", cpbr);
    }); // end #pbr.on.input/change

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

    /**
     * Removes selection when user clicks in white-space
     */
    $(document).on("mouseup touchend", "#text-container", function(e) {
        if ($(e.target).is(".word") === false &&
            !$(e.target).closest('#action-buttons').length &&
            !$(e.target).closest('#ap-play-btn').length) {
            e.stopPropagation();

            let $text_container = $("#text-container").length ? $("#text-container") : $pagereader.contents();

            highlighting = false;
            $text_container.find(".highlighted").removeClass("highlighted");
            hideActionButtonsPopUpToolbar(true);
            resumeAudio();
        }
    }); // end $pagereader.on.mouseup

    /**
     * Shows pop up toolbar when user clicks a word
     */
    function showActionButtonsPopUpToolbar() {
        let $audioplayer = $("#audioplayer");

        if ($audioplayer.length) {
            // if there is audio playing
            if (
                !$audioplayer.prop("paused") &&
                $audioplayer.prop("currentTime")
            ) {
                pauseAudio();
                playing_audio = true;
            } else {
                playing_audio = false;
            }
        }
        // TODO: IS WORD FREQUENCY STILL NECESSARY? HOW CAN I REINVENT THIS?
        getWordFrequency($selword.text(), doclang);
        setWordActionButtons($selword);

        const base_uris = {
            dictionary: dictionary_URI,
            img_dictionary: img_dictionary_URI,
            translator: translator_URI
        };

        setDicActionButtonsClick($selword, base_uris);
        showActionButtons($selword);
    } // end showActionButtonsPopUpToolbar

    /**
     * Hides actions pop up toolbar
     */
    function hideActionButtonsPopUpToolbar(renable_scroll) {
        if (renable_scroll) {
            $("#text-container").enableScroll();
        }
        hideActionButtons();
    } // end hideActionButtonsPopUpToolbar
});
