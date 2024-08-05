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
    let translate_paragraph_link = "";
    let next_phase = 2; // next phase of the learning cycle
    let playing_audio = false;
    window.parent.show_confirmation_dialog = true; // confirmation dialog that shows when closing window
    let doclang = $("html").attr("lang");

    // $doc & $pagereader are used to make this JS code work when showing simple texts &
    // ebooks (which are displayed inside an iframe)
    let $doc = $(parent.document);
    let $dic_frame = $doc.find("#dicFrame");
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
        // opens dictionary translator in case user right clicked on a word/phrase
        // but only on desktop browsers
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

        if (!isMobile && $(e.target).is(".word")) {
            window.open(buildTextTranslationLink(translator_URI, $selword), '_blank', 'noopener,noreferrer');
        }
        return false;
     }); // end document.contextmenu

    /**
     * Word/Phrase selection start
     * @param {event object} e
     */
    $(document).on("mousedown touchstart", ".word", function(e) {
        e.stopPropagation();
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
                togglePlayPause(); // audioplayer.js
                $("#audioplayer").trigger("pause");
            }
            $selword = $(this);
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
                }
                showModal();
            }
        }
        
        start_sel_time = end_sel_time = new Date();
    }); // end .word.mouseup/touchend

    /**
     * Determines if an element is after another one
     * @param {Jquery object} sel
     */
    $.fn.isAfter = function(sel) {
        return this.prevUntil(sel).length !== this.prevAll().length;
    }; // end $.fn.isAfter

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
            
            $(".word").removeClass("highlighted");

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

    $(document).on("click", ".dict-answer", function(e) {
        e.stopPropagation();
        $selword = $(this).parent().prev();
        showModal();
    });

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
     * Disables scrolling without making text jump around
     */
    $.fn.disableScroll = function() {
        window.oldScrollPos = $(window).scrollTop();

        $(window).on('scroll.scrolldisabler',function ( event ) {
            $(window).scrollTop( window.oldScrollPos );
            event.preventDefault();
        });
    };

    /**
     * Renables scrolling without making text jump around
     */

    $.fn.enableScroll = function() {
        $(window).off('scroll.scrolldisabler');
    };

    /**
     * Shows dictionary when user clicks a word
     * All words are enclosed in a.word tags
     */
    function showModal() {
        let $audioplayer = $("#audioplayer");

        if ($audioplayer.length) {
            // if there is audio playing
            if (
                !$audioplayer.prop("paused") &&
                $audioplayer.prop("currentTime")
            ) {
                $audioplayer.trigger("pause"); // pause audio
                playing_audio = true;
            } else {
                playing_audio = false;
            }
        }
        getWordFrequency($selword.text(), doclang);
        setAddDeleteButtons($selword);

        $doc.find("#loading-spinner").attr('class','lds-ellipsis m-auto');
        $dic_frame.attr('class','d-none');

        // build translate sentence url
        translate_paragraph_link = buildTextTranslationLink(translator_URI, $selword);

        // show dictionary
        $dic_frame.get(0).contentWindow.location.replace(buildDictionaryLink(dictionary_URI, $selword.text()));
        // the previous line loads iframe content without adding it to browser history,
        // as this one does: $dic_frame.attr('src', dic_url);
        $("#btn-add").focus();

        $doc.find("#dic-modal").modal("show");
    } // end showModal

    /**
     * Hides loader spinner when dictionary iframe finished loading
     */
    $dic_frame.on("load", function() {
        $doc.find("#loading-spinner").attr('class','d-none');
        $dic_frame.removeClass();
    }); // end $dic_frame.on.load()

    $doc.on("click", "#btn-translate", function() {
        window.open(translate_paragraph_link, '_blank', 'noopener,noreferrer');
    }); // end #btn-translate.on.click()

    $doc.on("click", "#btn-img-dic", function() {
        window.open(buildDictionaryLink(img_dictionary_URI, $selword.text()), '_blank', 'noopener,noreferrer');
    }); // end #btn-img-dic.on.click()

    /**
     * Adds word to user db
     * Triggered when user clicks the "Add" button in the dictionary modal window
     */
    $doc.on("click", "#btn-add", function() {
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
                const hide_elem_if_dictation_is_on = next_phase == 5 ? "style='display: none;'" : "";

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
                                "<a class='word reviewing new' data-toggle='modal' data-bs-target='#dic-modal' " +
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
                                "<a class='word reviewing forgotten' data-toggle='modal' data-bs-target='#dic-modal' " +
                                hide_elem_if_dictation_is_on + "></a>"
                            );
                        } else {
                            $word.wrap(
                                "<a class='word reviewing new' data-toggle='modal' data-bs-target='#dic-modal' " +
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

        $selword.removeClass("highlighted");
    }); // end #btn-add.on.click

    /**
     * Removes word from db
     * Triggered when user clicks the "Delete" button in the dictionary modal window
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

                    const hide_elem =  next_phase == 5;
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

                playAudioFromBeginning(); // from audioplayer.js
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

                playAudioFromBeginning(); // from audioplayer.js
                break;
            case 4:
                scrollToPageTop();

                if ($(".learning, .new, .forgotten").length == 0) {
                    setNewTooltip(btn_next_phase, 
                        'Finish & Save - Skipped phase 5 (reviewing): no underlined words</span>');
                    
                    next_phase = 6;
                } else {
                    setNewTooltip(btn_next_phase, 'Go to phase 5: Reviewing');
                    
                    next_phase++;
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
     * Triggered when modal dictionary window is closed
     */
    $doc.on("hidden.bs.modal", "#dic-modal", function() {
        let $audioplayer = $("#audioplayer");

        // Resumes playing if audio was paused when clicking on a word
        if (playing_audio && $audioplayer.length) {
            $audioplayer.trigger("play");
        }

        // removes word selection
        $selword.removeClass("highlighted");
    }); // end #dic-modal.on.hidden.bs.modal

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
     * Toggles dictation on/off
     */
    function toggleDictation() {
        const audio_is_loaded = $("#audioplayer").find("source").attr("src") != "";

        if (audio_is_loaded) {
            let $container = $("#text").clone();
            let $elems = $container.find(".word");
            let $original_elems = $(".word");

            if ($(".dict-answer").length == 0) {
                // if no words are underlined don't allow phase 5 (reviewing) & jump to phase 6 (save changes)
                if ($(".learning, .new, .forgotten").length == 0) {
                    setNewTooltip(document.getElementById('btn-next-phase'), 
                        'Finish & Save - 5 (reviewing): no underlined words');
                    
                    next_phase = 6;
                }

                // toggle dictation on
                // replace all underlined words/phrases with input boxes
                $elems.each(function(index, value) {
                    let $elem = $(this);
                    const length = $elem.text().length;
                    const width = $original_elems.eq(index).width();
                    const line_height = $original_elems.eq(index).css("font-size");
                    let border_color = '';
                    
                    if ($elem.hasClass('learned')) {
                        border_color = 'green'
                    } else if ($elem.hasClass('learning')) {
                        border_color = 'orange'
                    } else if ($elem.hasClass('new')) {
                        border_color = 'DodgerBlue'
                    } else if ($elem.hasClass('forgotten')) {
                        border_color = 'crimson'
                    }

                    $elem
                        .hide()
                        .after(
                            '<div class="input-group dict-input-group"><input type="text" class="dict" ' +
                            'style="width:' + width + "px; line-height:" + line_height + "; border-color:" + 
                            border_color + ';" ' + 'maxlength="' + length + '" data-text="' + $elem.text() + '">' +
                            '<span data-toggle="modal" data-bs-target="#dic-modal" class="dict-answer d-none"></span></div>'
                        );
                });

                $("#text").replaceWith($container);

                scrollToPageTop();
    
                // automatically play audio, from the beginning
                $("#range-speed").trigger("change", [{cpbr:0.5}]);
                playAudioFromBeginning(); // from audioplayer.js
    
                $(":text:first").focus(); // focus first input
            } else {
                // toggle dictation off
                $elems.each(function(index, value) {
                    let $elem = $(this);
                    $elem
                        .show()
                        .nextAll(":lt(1)")
                        .remove();
                });

                $("#text").replaceWith($container);
                
                scrollToPageTop();
                
                $("#audioplayer").trigger("pause");
            }
        }
    } // end toggleDictation

    /**
     * Checks if answer is correct and shows a cue to indicate status when user moves
     * focus out of an input box.
     */
    $("body").on("blur", ".dict", function() {
        let $curinput = $(this);
        if (
            $curinput.val().toLowerCase() ==
            $curinput.attr("data-text").toLowerCase()
        ) {
            $curinput.css("border-color", "green");
            $curinput
                .next("span")
                .not(".d-none")
                .addClass("d-none");
        } else if ($.trim($curinput.val()) != "") {
            $curinput.css("border-color", "crimson");
            $curinput
                .next("span")
                .removeClass("d-none")
                .addClass("dict-wronganswer")
                .text("[ " + $curinput.attr("data-text") + " ]");
        }
    }); // end .dict.on.blur

    /**
     * Implements shortcuts for dictation
     */
    $("body").on("keydown", ".dict", function(e) {
        const keyCode = e.keyCode || e.which;
        
        // IME on mobile devices may not return correct keyCode
        if (e.isComposing || keyCode == 0 || keyCode == 229) { 
            return;
        }

        // if input is empty...
        if (!$(this).val()) {
            if (keyCode == 8) {
                // if backspace is pressed, move focus to previous input
                const index = $(".dict").index(this) - 1;
                e.preventDefault();
                $(".dict")
                    .eq(index)
                    .focus();    
            } else if (keyCode == 32) {
                // if space key is pressed, prevent default behavior
                e.preventDefault();
            }    
        }
        
    }); // end .dict.on.keydown

    /**
     * Implements shortcuts for dictation
     */
    $("body").on("input", ".dict", function(e) {
        let keyCode = e.keyCode || e.which;
        const maxLength = $(this).attr("maxlength");
        const curTime = $("#audioplayer")[0].currentTime;

        // make sure keycode is correct (fix for IME on mobile devices)
        if (keyCode == 0 || keyCode == 229) { 
            keyCode = e.target.value.charAt(e.target.selectionStart - 1).charCodeAt();             
        }

        // if "1", rewind 1 sec; if "2", toggle audio; if "3" fast-forward 1 sec
        switch (keyCode) {
            case 8: // backspace
                if (!$(this).val()) {
                    const index = $(".dict").index(this) - 1;
                    $(".dict")
                        .eq(index)
                        .focus();    
                }
                break;
            case 49: // 1
                $("#audioplayer")[0].currentTime = curTime - 5;
                break;
            case 50: // 2
                togglePlayPause();  // found in audioplayer.js 
                break;
            case 51: // 3
                $("#audioplayer")[0].currentTime = curTime + 5;
                break;
            default:
                break;
        }
        $(this).val($(this).val().replace(/\d/gi, '')); // don't allow digits to get printed

        // if maxlength reached, switch focus to next input
        if(maxLength == $(this).val().length && !e.originalEvent.isComposing) {
            const index = $(".dict").index(this) + 1;
            $(".dict")
                .eq(index)
                .focus();
        }
    }); // end .dict.on.input

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
                    'Finish & Save - Skipped some phases: no audio detected & no underlined words');
        
                next_phase = 6;    
            } else {
                setNewTooltip(btn_next_phase, 
                    'Go to phase 5: Reviewing - Skipped some phases: no audio detected');
        
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
    $(document).on("click", $pagereader, function(e) {
        if ($(e.target).is(".word") === false) {
            e.stopPropagation();

            let $text_container = $("#text-container").length ? $("#text-container") : $pagereader.contents();

            highlighting = false;
            $("html").enableScroll();
            $text_container.find(".highlighted").removeClass("highlighted");
        }
    }); // end $pagereader.on.click

    /**
     * Shows confirmation message before closing/unloading tab/window
     */
    $(window.parent).on("beforeunload", function(e) {

        if (window.parent.show_confirmation_dialog) {
            e.preventDefault(); // To show a dialog we need this preventDefault()
            return "To save your progress, please click the Save button before you go.";
        }
    }); // end window.parent.on.beforeunload
});
