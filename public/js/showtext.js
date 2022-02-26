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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function() {
    var highlighting = false; // selection/highlighting mode
    var $sel_start, $sel_end; // Jquery object with the first & last elements of the selection 
    var start_sel_time, end_sel_time; // used in mobile devices to activate "word/phrase selection mode"
    var start_sel_pos_top; // used in mobile devices to activate "word/phrase selection mode"
    var swiping = false; // used in mobile devices to activate "word/phrase selection mode"
    var $selword = null; // jQuery object with selected word/phrase
    var time_handler = null;
    var dictionary_URI = "";
    var translator_URI = "";
    var translate_paragraph_link = "";
    var next_phase = 2; // next phase of the learning cycle
    var playing_audio = false;
    var abloop_start = 0;
    var abloop_end = 0;
    window.parent.show_confirmation_dialog = true; // confirmation dialog that shows when closing window before saving data
    var doclang = $("html").attr("lang");

    // $doc & $pagereader are used to make this JS code work when showing simple texts &
    // ebooks (which are displayed inside an iframe)
    var $doc = $(parent.document);
    var $dic_frame = $doc.find("#dicFrame");
    var $pagereader = $doc.find('iframe[id^="epubjs"]');
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
            $('#text').html(underlineWords(data, doclang));
        })
        .fail(function(xhr, ajaxOptions, thrownError) {
            console.log("There was an unexpected error trying to underline words in this text");
            
        })
        .always(function() {
            skipAudioPhases();
        }); // end $.ajax
    }

    /**
     * Toggles audio player
     */
    function toggleAudio() {
        var $audioplayer = $("#audioplayer");
        var playing = !$audioplayer.prop("paused");
            if (playing) {
                $audioplayer.trigger("pause");
            } else {
                $audioplayer.trigger("play");
            }
    } // end toggleAudio

    /**
     * AB Loop button click
     */
    $("body").on("click", "#btn-abloop", function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (abloop_start == 0 && abloop_end == 0) {
            abloop_start = $("#audioplayer").prop("currentTime");
            $(this).text("B");
        } else if (abloop_start > 0 && abloop_end == 0) {
            abloop_end = $("#audioplayer").prop("currentTime");
            $(this).text("C");
        } else {
            abloop_start = abloop_end = 0;
            $(this).text("A");
        }
    }); // end #btn-abloop.click

    /**
     * AB Loop
     */
    $("#audioplayer").on("timeupdate", function() {
        if (abloop_end > 0) {
            if($(this).prop("currentTime") >= abloop_end) {
                $(this).prop("currentTime", abloop_start);
            }    
        }
    }); // end #audioplayer.timeupdate

    /**
     * Disables right click context menu
     */
    $(document).on("contextmenu",function(e){
        // opens dictionary translator in case user right clicked on a word/phrase
        // but only on desktop browsers
        var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

        if (!isMobile && $(e.target).is(".word")) {
            window.open(buildTranslateParagraphLink());
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
            $("#audioplayer").trigger("pause");
            $selword = $(this);
            // opening the translator was moved to $(document).on("contextmenu") due to a bug in Windows
        }
    }); // end .word.on.mousedown/touchstart

    /**
     * Word/Phrase selection end
     * @param {event object} e
     */
    $(document).on("mouseup touchend", ".word", function(e) {
        e.stopPropagation();
        // e.preventDefault();

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
                    var $closest = $(this).closest('.learning, .learned, .forgotten');
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
            var cur_sel_pos_top = $(this).offset().top - $(window).scrollTop();
            swiping = swiping ? swiping : Math.abs(start_sel_pos_top - cur_sel_pos_top) > 0;

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

    // ajax call to get dictionary & translator URIs
    $.ajax({
        url: "/ajax/getdicuris.php",
        type: "GET",
        dataType: "json"
    }).done(function(data) {
        if (data.error_msg == null) {
            dictionary_URI = data.dictionary_uri;
            translator_URI = data.translator_uri;
        }
    }); // end $.ajax

    /**
     * Builds translator link including the paragraph to translate as a parameter
     */
    function buildTranslateParagraphLink() {
        var $start_obj = $selword.prevUntil(":contains('.')").last();
        $start_obj = $start_obj.length > 0 ? $start_obj : $selword;
        var $end_obj = $selword
            .prev()
            .nextUntil(":contains('.')")
            .last()
            .next();
        $end_obj =
            $end_obj.length > 0 ? $end_obj : $selword.nextAll().last().next();
        var $sentence = $start_obj
            .nextUntil($end_obj)
            .addBack()
            .next()
            .addBack();
        var sentence = $sentence.text().replace(/(\r\n|\n|\r)/gm, " ").trim();

        return translator_URI.replace("%s", encodeURI(sentence));
    } // end buildTranslateParagraphLink

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
     * Sets Add & Delete buttons depending on whether selection exists in database
     */
    function setAddDeleteButtons() {
        var $btnremove = $(parent.document).find("#btnremove");
        var $btnadd = $(parent.document).find("#btnadd");

        var underlined_words_in_selection = $selword.filter(
            ".learning, .new, .forgotten, .learned"
        ).length;
        var words_in_selection = $selword.filter(".word").length;

        if (words_in_selection == underlined_words_in_selection) {
            if ($btnremove.is(":visible") === false) {
                $btnremove.show();
                $btnadd.text("Forgot");
            }
        } else {
            $btnremove.hide();
            $btnadd.text("Add");
        }
    } // end setAddDeleteButtons

    /**
     * Shows message for high & medium frequency words in dictionary modal window
     * @param {string} word
     * @param {string} lg_iso
     */
    function getWordFrequency(word, lg_iso) {
        var $freqlvl = $doc.find("#bdgfreqlvl");

        // ajax call to get word frequency
        $.ajax({
            type: "GET",
            url: "/ajax/getwordfreq.php",
            data: { word: word, lg_iso: lg_iso }
        }).done(function(data) {
            if (data == 0){
                $freqlvl.hide();
            } else if (data < 81) {
                $freqlvl
                    .hide()
                    .text("High frequency word")
                    .removeClass()
                    .addClass("badge badge-danger")
                    .show();
            } else if (data < 97){
                $freqlvl
                    .hide()
                    .text("Medium frequency word")
                    .removeClass()
                    .addClass("badge badge-warning")
                    .show();
            }
        }).fail(function() {
            $freqlvl.hide();
        });
    } // end getWordFrequency

    /**
     * Shows dictionary when user clicks a word
     * All words are enclosed in a.word tags
     */
    function showModal() {
        var $audioplayer = $("#audioplayer");

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
        setAddDeleteButtons();

        $doc.find("#iframe-loader").attr('class','lds-ellipsis m-auto');
        $dic_frame.attr('class','d-none');

        // build translate sentence url
        translate_paragraph_link = buildTranslateParagraphLink();

        // show dictionary
        var search_text = $selword.text().replace(/\r?\n|\r/gm, " ");
        var url = dictionary_URI.replace("%s", encodeURIComponent(search_text));

        $dic_frame.get(0).contentWindow.location.replace(url);
        $("#btnadd").focus();
        // the previous line loads iframe content without adding it to browser history,
        // as this one does: $dic_frame.attr('src', url);

        $doc.find("#myModal").modal("show");
    } // end showModal

    /**
     * Hides loader spinner when dictionary iframe finished loading
     */
    $dic_frame.on("load", function() {
        $doc.find("#iframe-loader").attr('class','d-none');
        $dic_frame.removeClass();
    }); // end $dic_frame.on.load()

    $doc.on("click", "#btn-translate", function() {
        window.open(translate_paragraph_link);
    }); // end #btn-translate.on.click()

    /**
     * Adds word to user db
     * Triggered when user clicks the "Add" button in the dictionary modal window
     */
    $doc.on("click", "#btnadd", function() {
        var is_phrase = $selword.length > 1 ? 1: 0;
        var sel_text = $selword.text();
        var audio_is_loaded = $("#audioplayer").find("source").attr("src") != false;

        // add selection to "words" table
        $.ajax({
            type: "POST",
            url: "/ajax/addword.php",
            data: {
                word: sel_text,
                is_phrase: is_phrase
            }
        })
            .done(function() {
                var no_prev_underlined_words = $(".learning, .new, .forgotten").length == 0;

                // if successful, underline word or phrase
                if (is_phrase) {
                    // if it's a phrase
                    var word_count = $selword.filter(".word").length;

                    // build filter based on first word of the phrase
                    var $filterphrase = $pagereader
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
                        var $lastword = $(this)
                            .nextAll("a.word")
                            .slice(0, word_count - 1)
                            .last();
                        var $phrase = $(this)
                            .nextUntil($lastword)
                            .addBack()
                            .next("a.word")
                            .addBack();

                        if (
                            $phrase.text().toLowerCase() ===
                            sel_text.toLowerCase()
                        ) {
                            $phrase.wrapAll(
                                "<a class='word reviewing new' data-toggle='modal' data-target='#myModal'></a>"
                            );

                            $phrase.contents().unwrap();
                        }
                    });
                } else {
                    // if it's a word
                    // build filter with all the instances of the word in the text
                    var $filterword = $pagereader
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
                        var $word = $(this);
                        if ($word.is(".new, .learning, .learned, .forgotten")) {
                            $word.wrap(
                                "<a class='word reviewing forgotten' data-toggle='modal' data-target='#myModal'></a>"
                            );
                        } else {
                            $word.wrap(
                                "<a class='word reviewing new' data-toggle='modal' data-target='#myModal'></a>"
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
                        $("#btn-next-phase").attr('title',
                            'Go to phase 4: Writing (be patient, may take a while to load depending on text length)'
                        );
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
    }); // end #btnadd.on.click

    /**
     * Removes word from db
     * Triggered when user clicks the "Delete" button in the dictionary modal window
     */
    $doc.on("click", "#btnremove", function() {
        var audio_is_loaded = $("#audioplayer").find("source").attr("src") != false;

        $.ajax({
            type: "POST",
            url: "/ajax/removeword.php",
            data: {
                word: $selword.text()
            }
        })
            .done(function() {
                var $filter = $pagereader
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

                    var $result = $(underlineWords(data, doclang));
                    var $cur_filter = {};
                    var cur_word = /""/;

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
                            var word = $(this).text().toLowerCase();
                            var user_word = data.user_words.find(function (element) {
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
    }); // end #btnremove.on.click

    /**
     * Executes next phase of assisted learning
     * Triggered when the user presses the big blue button at the end
     * Phases: 1 = reading; 2 = listening; 3 = speaking; 4 = writing; 5 = reviewing
     */
    $("body").on("click", "#btn-next-phase", function() {
        var audio_is_loaded = $("#audioplayer").find("source").attr("src") != false;
        var $msg_phase = $("#alert-msg-phase");

        if (next_phase < 6 && !audio_is_loaded) {
            skipAudioPhases();
        }

        switch (next_phase) {
            case 2:
                $("html, body").animate(
                    {
                        scrollTop: 0
                    },
                    "fast"
                );

                next_phase++;

                $msg_phase
                    .html(
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h5>Assisted learning - Phase 2: Listening</h5><span class="small">Pay attention to the pronunciation of each word. You can slow down the audio if necessary.</span>'
                    );

                $(this).attr('title',
                    'Go to phase 3: Speaking'
                );

                playAudioFromBeginning();
                break;
            case 3:
                $("html, body").animate(
                    {
                        scrollTop: 0
                    },
                    "fast"
                );

                next_phase++;

                $(this).attr('title', 
                    'Go to phase 4: Writing (be patient, may take a while to load depending on text length)'
                    );

                $msg_phase.html(
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h5>Assisted learning - Phase 3: Speaking</h5><span class="small">Read the text out loud and try to emulate the pronunciation of each word as you listen to the audio. You can slow it down if necessary.</span>'
                );

                playAudioFromBeginning();
                break;
            case 4:
                $("html, body").animate(
                    {
                        scrollTop: 0
                    },
                    "fast"
                );

                if ($(".learning, .new, .forgotten").length == 0) {
                    $(this).attr('title',
                        'Finish & Save - Skipped phase 5 (reviewing): no underlined words</span>'
                    );
                    next_phase = 6;
                } else {
                    $(this).attr('title',
                        'Go to phase 5: Reviewing'
                    );
                    next_phase++;
                }

                $msg_phase
                    .html(
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h5>Assisted learning - Phase 4: Writing</h5><span class="small">Fill in the blanks as you listen to the dictation. To toggle audio playback press <kbd>spacebar</kbd> (or <kbd>Shift + spacebar</kbd> if the expression to write in the input box contains spaces). To rewind or fast-forward 1 second, use <kbd>,</kbd> and <kbd>.</kbd>. For the moment, these shortcuts work only on desktop devices.</span>'
                    );

                toggleDictation();
                break;
            case 5:
                $("html, body").animate(
                    {
                        scrollTop: 0
                    },
                    "fast"
                );

                next_phase++;

                $(this).attr('title', 'Finish & Save');

                $msg_phase
                    .html(
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h5>Assisted learning - Phase 5: Reviewing</h5><span class="small"><u>This is the most <a href="https://en.wikipedia.org/wiki/Testing_effect" class="alert-link" target="_blank" rel="noopener noreferrer">critical phase</a> for long-term language acquisition.</u><br>Review all the underlined words, even the ones with green underlining. Make an effort to remember their meaning and pronunciation, while also paying attention to their spelling. Speak out alternative sentences using these words. The latter is essential to turn your <a href="https://en.wiktionary.org/wiki/passive_vocabulary" class="alert-link" target="_blank" rel="noopener noreferrer">passive vocabulary</a> into <a href="https://en.wiktionary.org/wiki/active_vocabulary" class="alert-link" target="_blank" rel="noopener noreferrer">active vocabulary</a>.</span>'
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
        var oldwords = [];
        var id = [];
        var word = "";
        var archive_text = true;
        var is_shared = $("#is_shared").length > 0;
        var gems_earned = 0;

        $(".learning").each(function() {
            word = $(this)
                .text()
                .toLowerCase();
            if ($.inArray(word, oldwords) == -1) {
                oldwords.push(word);
            }
        });

        id.push($("#text-container").attr("data-textID")); // get text ID

        if (is_shared) {
            id = undefined;
            archive_text = undefined;
        }

        $.ajax({
            type: "POST",
            url: "/ajax/archivetext.php",
            data: {
                words: oldwords,
                textIDs: JSON.stringify(id),
                archivetext: archive_text
            }
        })
            .done(function(data) {
                if (data.error_msg == null) {
                    // update user score (gems)
                    var review_data = {
                        words : { new:       $(".reviewing.new").length, 
                                  learning:  $(".reviewing.learning").length, 
                                  forgotten: $(".reviewing.forgotten").length },
                        texts : { reviewed:  1 }
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
                            var url = "/textstats.php";
                            var total_words =
                                Number($(".word").length) + Number($(".phrase").length);
                            var form = $(
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
                            alert("Oops! There was an unexpected error.");
                        }
                    })
                    .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("Oops! There was an unexpected error.");
                    });
                } else {
                    alert("Oops! There was an unexpected error.");
                }
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Oops! There was an unexpected error.");
            });
    } // end #archiveTextAndSaveWords

    /**
     * Changes the position of audio player controls (sticky or initial)
     */
    $("#btn-toggle-audio-player-controls").on("click", function () {
        var $audio_player_container = $('#audioplayer-container');
        if ($audio_player_container.css('position') == 'static') {
            $audio_player_container.css({
                'position': '-webkit-sticky',
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
    $doc.on("hidden.bs.modal", "#myModal", function() {
        var $audioplayer = $("#audioplayer");

        // Resumes playing if audio was paused when clicking on a word
        if (playing_audio && $audioplayer.length) {
            $audioplayer.trigger("play");
        }

        // removes word selection
        $selword.removeClass("highlighted");
    }); // end #myModal.on.hidden.bs.modal

    /**
     * Changes playback speed when user moves slider
     */
    $("body").on("input change", "#range-speed", function(e, data) {
        var cpbr = data !== undefined ? data.cpbr : parseFloat($(this).val()).toFixed(1);
        $(this).val(cpbr);
        $("#currentpbr").text(cpbr);    
        $("#audioplayer").prop("playbackRate", cpbr);
    }); // end #pbr.on.input/change

    /**
     * Plays audio from beginning
     */
    function playAudioFromBeginning() {
        var $audioplayer = $("#audioplayer");
        $audioplayer.prop("currentTime", "0");
        $audioplayer.trigger("play");
    } // end playAudioFromBeginning

    /**
     * Toggles dictation on/off
     */
    function toggleDictation() {
        var audio_is_loaded = $("#audioplayer").find("source").attr("src") != false;
        if (audio_is_loaded) {
            if ($(".dict-answer").length == 0) {
                // if user is no words are underlined don't allow phase 5 (reviewing) & go directly to phase 6 (save changes)
                if ($(".learning, .new, .forgotten").length == 0) {
                    $("#btn-next-phase").attr('title',
                        'Finish & Save - 5 (reviewing): no underlined words'
                    );
                    next_phase = 6;
                }

                // toggle dictation on
                // replace all underlined words/phrases with input boxes
                var $container = $("#text").clone();
                var $elems = $container.find(".word");
                var $original_elems = $(".word");

                $elems.each(function(index, value) {
                    var $elem = $(this);
                    var length = $elem.text().length;
                    var width = $original_elems.eq(index).width();
                    var line_height = $original_elems.eq(index).css("font-size");
                    $elem
                        .hide()
                        .after(
                            '<div class="input-group dict-input-group"><input type="text" class="dict" ' +
                            'style="width:' +
                            width +
                            "px; line-height:" +
                            line_height +
                            ';" ' +
                            'maxlength="' +
                            length +
                            '" data-text="' +
                            $elem.text() +
                            '">' +
                            '<span class="input-group-append dict-answer d-none"></span></div>'
                        );
                });

                $("#text").replaceWith($container);

                $("html, body").animate(
                    {
                        scrollTop: 0
                    },
                    "fast"
                ); // go back to the top of the page
    
                // automatically play audio, from the beginning
                $("#range-speed").trigger("change", [{cpbr:0.5}]);
                playAudioFromBeginning();
    
                $(":text:first").focus(); // focus first input
            } else {
                // toggle dictation off
                $(".word").each(function(index, value) {
                    var $elem = $(this);
                    $elem
                        .show()
                        .nextAll(":lt(1)")
                        .remove();
                });
                $("html, body").animate(
                    {
                        scrollTop: 0
                    },
                    "fast"
                );
                $("#audioplayer").trigger("pause");
            }
        }
    } // end toggleDictation

    /**
     * Checks if answer is correct and shows a cue to indicate status when user moves
     * focus out of an input box.
     */
    $("body").on("blur", ".dict", function() {
        var $curinput = $(this);
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
     * Jumps to next input when input's maxlength is reached
     */
    $("body").on("input", ".dict", function() {
        var maxLength = $(this).attr("maxlength");
        // if maxlength reached, switch focus to next input
        if(maxLength == $(this).val().length) {
            var index = $(".dict").index(this) + 1;
            $(".dict")
                .eq(index)
                .focus();
        }
    }); // end .dict.on.input

    /**
     * Implements shortcuts for dictation
     */
    $("body").on("keydown", ".dict", function(e) {
        var keyCode = e.keyCode || e.which;
        
        // IME on mobile devices may not return correct keyCode
        if (keyCode == 0 || keyCode == 229) { 
            return;
        }

        var shifted = e.shiftKey;
        var curTime = $("#audioplayer")[0].currentTime;

        // if space is pressed, toggle audio; if arrow up or "1", rewind 5 secs; 
        // if arrow down or "2" fast-forward 5 secs; if backspace, move focus to previous input
        switch (keyCode) {
            case 32: // space
                // only toggle audio playback if word/phrase does not have spaces 
                // or if shift key is pressed
                if ($(this).data("text").indexOf(" ") > 0 && !shifted) {
                    break; // write space
                }
                toggleAudio();   
                return false; // don't write space
            case 188: // comma
                $("#audioplayer")[0].currentTime = curTime - 1;
                return false; // pretend key was not pressed
            case 190: // dot
                $("#audioplayer")[0].currentTime = curTime + 1;
                return false; // pretend key was not pressed
            case 8: // backspace
                if (!$(this).val()) {
                    var index = $(".dict").index(this) - 1;
                    $(".dict")
                        .eq(index)
                        .focus();    
                }
                break;
            default:
                break;
        }
    }); // end .dict.on.keydown

    /**
     * Tries to reload audio
     * When audio fails to load, an error message is shown with a link to reload audio
     * This event is triggered when the user clicks this link
     */
    $doc.on("click", "#retry-audio-load", function(e) {
        e.preventDefault();
        $("#alert-msg-audio").addClass("d-none");
        $("#audioplayer-loader").removeClass("d-none");
        loadAudio();
    }); // end #retry-audio-load.on.click

    /**
     * Helper function to skip audio phases
     */
    function skipAudioPhases() {
        var audio_is_loaded = $("#audioplayer").find("source").attr("src") != false;

        if (!audio_is_loaded) {
            if ($(".learning, .new, .forgotten").length == 0) {
                $("#btn-next-phase").attr('title',
                    'Finish & Save - Skipped some phases: no audio detected & no underlined words'
                );
        
                next_phase = 6;    
            } else {
                $("#btn-next-phase").attr('title',
                    'Go to phase 5: Reviewing - Skipped some phases: no audio detected'
                );
        
                next_phase = 5;
            }    
        }
    } // end skipAudioPhases

    /**
     * Loads audio for text
     */
    function loadAudio() {
        var $audio_player = $("#audioplayer");
        if ($audio_player.length > 0) {
            var txt = $("#text").text();

            $.ajax({
                type: "POST",
                url: "/ajax/fetchaudiostream.php",
                data: { text: txt, langiso: doclang },
                dataType: "json"
            })
                .done(function(e) {
                    if (e.error != null || !e.response) {
                        $("#audioplayer-loader").addClass("d-none");
                        $("#alert-msg-audio").removeClass("d-none").empty().append('There was an unexpected error trying to create audio from this text. <a class="alert-link" href="#" id="retry-audio-load">Try again</a> later.');
                        skipAudioPhases();
                        return false;
                    }
                    $("#audio-mp3").attr("src", e.response);
                    $audio_player[0].load();
                    $("#audioplayer-loader").addClass("d-none");
                    $("#audioplayer").removeClass("d-none");
                    $("#audioplayer-speedbar").removeClass("d-none");

                    $("#btn-next-phase").attr('title',
                        'Go to phase 2: Listening'
                    );

                    next_phase = 2;
                    
                    return true;
                })
                .fail(function(xhr) {
                    if (xhr.status == 403) {
                        $("#audioplayer-loader").addClass("d-none");
                        $("#alert-msg-audio").removeClass("d-none").empty().append('You have reached your audio streaming limit. Try again tomorrow or <a class="alert-link" href="gopremium.php">improve your plan</a> to increase your daily audio streaming limit.');
                    } else {
                        $("#audioplayer-loader").addClass("d-none");
                        $("#alert-msg-audio").removeClass("d-none").empty().append('There was an unexpected error trying to create audio from this text. <a class="alert-link" href="#" id="retry-audio-load">Try again</a> later.');
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

            var $text_container = $("#text-container").length ? $("#text-container") : $pagereader.contents();

            highlighting = false;
            $("html").enableScroll();
            $text_container.find(".highlighted").removeClass("highlighted");
        }
    }); // end $pagereader.on.click

    /**
     * Shows confirmation message before closing/unloading tab/window
     */
    $(window.parent).on("beforeunload", function() {
        if (window.parent.show_confirmation_dialog) {
            return "To save your progress, please click the Save button before you go. Otherwise, your changes will be lost. Are you sure you want to exit this page?";
        }
    }); // end window.parent.on.beforeunload
});
