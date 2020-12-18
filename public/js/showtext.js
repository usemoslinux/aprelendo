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
    var phase = 1; // first phase of the learning cycle
    var playing_audio = false;
    var abloop_start = 0;
    var abloop_end = 0;
    window.parent.show_confirmation_dialog = true; // confirmation dialog that shows when closing window before saving data

    // $doc & $pagereader are used to make this JS code work when showing simple texts &
    // ebooks (which are displayed inside an iframe)
    var $doc = $(parent.document);
    var $dic_frame = $doc.find("#dicFrame");
    var $pagereader = $doc.find('iframe[id^="epubjs"]');
    var $pagereader = $pagereader.length > 0 ? $pagereader : $("html");
      
    loadAudio();

    /**
     * Sets keyboard shortcuts for media player
     * @param {event object} e Used to get keycodes
     */
    $(window).on("keydown", function(e) {
        var $audioplayer = $("#audioplayer");
        if ($audioplayer.length && e.ctrlKey) {
            switch (e.keyCode) {
                case 32: // "spacebar" keyCode
                    if ($audioplayer.prop("paused")) {
                        $audioplayer.trigger("play");
                    } else {
                        $audioplayer.trigger("pause");
                    }

                    break;
            }
        }
    }); // end window.on.keydown

    /**
     * Pauses dictation audio when user is typing an answer inside an input
     * @param {event object} e Used to get keycodes
     */
    $("body").on("input", "input:text", function(e) {
        var lastkeypress = new Date().getTime();
        var keyCode = e.keyCode || e.which;

        if (keyCode != 9) {
            clearTimeout(time_handler);
            toggleAudio(lastkeypress);
        }
    }); // end input:text.on.input

    /**
     * Pauses audio for some secs when user is typing answer in dictation mode
     * @param {Date} lastkeypress
     */
    function toggleAudio(lastkeypress) {
        var currentTime = new Date().getTime();
        var $audioplayer = $("#audioplayer");

        if ((currentTime - lastkeypress > 1000) && !$audioplayer.prop("ended")) {
            $audioplayer.trigger("play");
        } else {
            var playing = !$audioplayer.prop("paused");
            if (playing) {
                $audioplayer.trigger("pause");
            }
            time_handler = setTimeout(() => {
                toggleAudio(lastkeypress);
            }, 1000);
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
        e.preventDefault();
     }); // end document.contextmenu

    /**
     * Word/Phrase selection start
     * @param {event object} e
     */
    $(document).on("mousedown touchstart", ".word", function(e) {
        e.stopPropagation();
        if (e.which < 2) {
            // if left mouse button / touch...
            highlighting = true;
            $sel_start = $sel_end = $(this);
            if (e.type == "touchstart") {
                start_sel_time = new Date();
                start_sel_pos_top = $sel_start.offset().top - $(window).scrollTop();
            }
        }
    }); // end .word.on.mousedown/touchstart

    /**
     * Word/Phrase selection end
     * @param {event object} e
     */
    $(document).on("mouseup touchend", ".word", function(e) {
        e.stopPropagation();
        e.preventDefault();

        end_sel_time = new Date();
        
        if (e.type == "touchend") {
            if (!swiping) {
                highlighting = (end_sel_time - start_sel_time > 1000);
            }
            $('html').css({'overflow':'visible'});    
            swiping = false;
        }

        if (highlighting) {
            if (e.which < 2) {
                // if left mouse button / touch...
                highlighting = false;
                
                if ($sel_start === $sel_end) {
                    $closest = $(this).closest('.learning, .learned, .forgotten');
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
                highlighting = (end_sel_time - start_sel_time > 1000);
            }
        } 

        if (highlighting) {
            if (e.type == "touchmove") {
                $('html').css({'overflow':'hidden'});
            }
            
            $(".word").removeClass("highlighted");

            $sel_end =
                e.type === "mouseover"
                    ? $(this)
                    : $(
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
            $end_obj.length > 0
                ? $end_obj
                : $selword
                      .nextAll()
                      .last()
                      .next();
        var $sentence = $start_obj
            .nextUntil($end_obj)
            .addBack()
            .next()
            .addBack();
        var sentence = $sentence.text().replace(/(\r\n|\n|\r)/gm, " ");

        return translator_URI.replace("%s", encodeURI(sentence));
    } // end buildTranslateParagraphLink

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
            if (data == 0) {
                $freqlvl.hide();
            } else if (data < 81) {
                $freqlvl
                    .hide()
                    .text("High frequency word")
                    .removeClass()
                    .addClass("badge badge-danger")
                    .show();
            } else {
                $freqlvl
                    .hide()
                    .text("Medium frequency word")
                    .removeClass()
                    .addClass("badge badge-warning")
                    .show();
            }
        });
    } // end getWordFrequency

    /**
     * Shows dictionary when user clicks a word
     * All words are enclosed in span.word tags
     */
    function showModal() {
        var audioplayer = $("#audioplayer");
        var doclang = $("html").attr("lang");

        if (audioplayer.length) {
            // if there is audio playing
            if (
                !audioplayer.prop("paused") &&
                audioplayer.prop("currentTime")
            ) {
                audioplayer.trigger("pause"); // pause audio
                playing_audio = true;
            } else {
                playing_audio = false;
            }
        }

        getWordFrequency($selword.text(), doclang);
        setAddDeleteButtons();

        $doc.find("#iframe-loader").attr('class','lds-ripple m-auto');
        $dic_frame.attr('class','d-none');

        // build translate sentence url
        translate_paragraph_link = buildTranslateParagraphLink();

        // show dictionary
        var search_text = $selword.text().replace(/\r?\n|\r/gm, " ");
        var url = dictionary_URI.replace("%s", encodeURIComponent(search_text));

        $dic_frame.get(0)
                  .contentWindow.location.replace(url);
        $("#btnadd").focus();
        // the previous line loads iframe content without adding it to browser history,
        // as this one does: $dic_frame.attr('src', url);

        $doc.find("#myModal")
            .modal("show");
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
                // if successful, underline word or phrase
                if (is_phrase) {
                    // if it's a phrase
                    var word_count = $selword.filter(".word").length;

                    // build filter based on first word of the phrase
                    var $filterphrase = $pagereader
                        .contents()
                        .find("span.word")
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
                            .nextAll("span.word")
                            .slice(0, word_count - 1)
                            .last();
                        var $phrase = $(this)
                            .nextUntil($lastword)
                            .addBack()
                            .next("span.word")
                            .addBack();

                        if (
                            $phrase.text().toLowerCase() ===
                            sel_text.toLowerCase()
                        ) {
                            if (
                                $(this).is(
                                    ".new, .learning, .learned, .forgotten"
                                )
                            ) {
                                $phrase.wrapAll(
                                    "<span class='word reviewing forgotten' data-toggle='modal' data-target='#myModal'></span>"
                                );
                            } else {
                                $phrase.wrapAll(
                                    "<span class='word reviewing new' data-toggle='modal' data-target='#myModal'></span>"
                                );
                            }

                            $phrase.contents().unwrap();
                        }
                    });
                } else {
                    // if it's a word
                    // build filter with all the instances of the word in the text
                    var $filterword = $pagereader
                        .contents()
                        .find("span.word")
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
                                "<span class='word reviewing forgotten' data-toggle='modal' data-target='#myModal'></span>"
                            );
                        } else {
                            $word.wrap(
                                "<span class='word reviewing new' data-toggle='modal' data-target='#myModal'></span>"
                            );
                        }
                    });

                    $filterword.contents().unwrap();
                }

                // if there were no previous word underlined, therefore phases 2 & 3 were off,
                // when user adds his first new word, activate these phases
                var actual_phase = $("#alert-msg-phase").attr("data-phase");
                if (phase == 4 && audio_is_loaded && actual_phase == 3) {
                    var phase_names = [
                        "Reading",
                        "Listening",
                        "Speaking",
                        "Writing"
                    ];
                    $("#btn-next-phase").html(
                        "Go to phase " +
                            phase +
                            '<br><span class="small">' +
                            phase_names[phase - 1] +
                            "</span>"
                    );
                    phase = parseInt(actual_phase);
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
        var audio_is_loaded =
            $("#audioplayer")
                .find("source")
                .attr("src") != undefined &&
            $("#audioplayer")
                .find("source")
                .attr("src") != "";

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
                    .find("span.word")
                    .filter(function() {
                        return (
                            $(this)
                                .text()
                                .toLowerCase() === $selword.text().toLowerCase()
                        );
                    });

                $.ajax({
                    url: "/ajax/underlinewords.php",
                    type: "POST",
                    data: {
                        txt: $selword.text(),
                        is_ebook: false
                    }
                }).done(function(result) {
                    // if everything went fine, remove the underlining
                    $filter.removeClass();
                    $filter.addClass('word');

                    // if user is in phase 3 (speaking) and deleted the only word that was underlined
                    // don't allow phase 3 (writing) & go directly to last phase (save changes)
                    if (
                        phase == 3 &&
                        audio_is_loaded > 0 &&
                        $(".learning, .new, .forgotten").length == 0
                    ) {
                        $("#btn-next-phase").html(
                            'Finish & Save<br><span class="small">Skipped phase 4 (writing): no underlined words</span>'
                        );
                        phase++;
                    }
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
     */
    $("body").on("click", "#btn-next-phase", function() {
        var audio_is_loaded =
            $("#audioplayer")
                .find("source")
                .attr("src") != undefined &&
            $("#audioplayer")
                .find("source")
                .attr("src") != "";
        var $msg_phase = $("#alert-msg-phase");

        switch (phase) {
            case 1:
                $("html, body").animate(
                    {
                        scrollTop: 0
                    },
                    "slow"
                );

                if (!audio_is_loaded) {
                    $(this).html(
                        'Finish & Save<br><span class="small">Skipped phases 2, 3 & 4: no audio detected</span>'
                    );
                    phase = 4;
                    break;
                }

                phase++;

                $msg_phase
                    .html(
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h6>Assisted learning - Phase 2: Listening</h6><span class="small">Pay attention to the pronunciation of each word. You can slow down the audio if necessary.</span>'
                    )
                    .attr("data-phase", phase);

                $(this).html(
                    'Go to phase 3<br><span class="small">Speaking</span>'
                );

                playAudioFromBeginning();
                break;
            case 2:
                $("html, body").animate(
                    {
                        scrollTop: 0
                    },
                    "slow"
                );
                if (!audio_is_loaded) {
                    $(this).html(
                        'Finish & Save<br><span class="small">Skipped phase 4 (writing): no audio detected</span>'
                    );
                    phase = 4;
                    break;
                }

                if ($(".learning, .new, .forgotten").length == 0) {
                    $(this).html(
                        'Finish & Save<br><span class="small">Skipped phase 4 (writing): no underlined words</span>'
                    );
                    phase = 4;
                    $msg_phase.attr("data-phase", 3);
                } else {
                    $(this).html(
                        'Go to phase 4<br><span class="small">Writing</span>'
                    );
                    phase++;
                    $msg_phase.attr("data-phase", phase);
                }

                $msg_phase.html(
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h6>Assisted learning - Phase 3: Speaking</h6><span class="small">Read out loud and try to emulate the pronunciation of each word as you listen to the audio. You can slow it down if necessary.</span>'
                );

                playAudioFromBeginning();
                break;
            case 3:
                $("html, body").animate(
                    {
                        scrollTop: 0
                    },
                    "slow"
                );

                phase++;

                $(this).html("Finish & Save");

                $msg_phase
                    .html(
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h6>Assisted learning - Phase 4: Writing</h6><span class="small">Fill in the blanks as you listen to the dictation.</span>'
                    )
                    .attr("data-phase", phase);

                toggleDictation();
                break;
            case 4:
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
    $("body").on("click", "#btn-save", archiveTextAndSaveWords);

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

        $(".learning").each(function() {
            word = $(this)
                .text()
                .toLowerCase();
            if (jQuery.inArray(word, oldwords) == -1) {
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
                        "</form>"
                );
                $("body").append(form);
                form.submit();
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Oops! There was an error updating the database.");
            });
    } // end #btn-save.on.click

    /**
     * Triggered when modal dictionary window is closed
     */
    $doc.on("hidden.bs.modal", "#myModal", function() {
        var audioplayer = $("#audioplayer");

        // Resumes playing if audio was paused when clicking on a word
        if (playing_audio && audioplayer.length) {
            audioplayer.trigger("play");
        }

        // removes word selection
        $selword.removeClass("highlighted");
    }); // end #myModal.on.hidden.bs.modal

    /**
     * Changes playback speed when user moves slider
     */
    $("body").on("input change", "#range-speed", function() {
        cpbr = parseFloat($(this).val()).toFixed(1);
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
        if ($(".dict-answer").length == 0) {
            // toggle dictation on
            //replace all underlined words/phrases with input boxes
            $(".learning, .new, .forgotten").each(function(index, value) {
                var $elem = $(this);
                var length = $elem.text().length;
                var width = $elem.width();
                var line_height = $elem.css("font-size");
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
            $("html, body").animate(
                {
                    scrollTop: 0
                },
                "slow"
            ); // go back to the top of the page

            // automatically play audio, from the beginning
            playAudioFromBeginning();

            $(":text:first").focus(); // focus first input
        } else {
            // toggle dictation off
            $(".learning, .new, .forgotten").each(function(index, value) {
                $elem = $(this);
                $elem
                    .show()
                    .nextAll(":lt(1)")
                    .remove();
            });
            $("html, body").animate(
                {
                    scrollTop: 0
                },
                "slow"
            );
            $("#audioplayer").trigger("pause");
        }
    } // end toggleDictation

    $("body").on("click", "#btndictation", function() {
        toggleDictation();
    }); // end #btndictation.on.click

    /**
     * Checks if answer is correct and shows a cue to indicate status when user moves
     * focus out of an input box.
     */
    $("body").on("blur", ":text", function() {
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
    }); // end :text.on.blur

    /**
     * Jumps to next input when user presses Enter inside an input
     */
    $("body").on("input", ".dict", function(e) {
        if (e.which === 13) {
            var index = $(".dict").index(this) + 1;
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
        $("#alert-msg-audio").addClass("d-none");
        $("#audioplayer-loader").removeClass("d-none");
        loadAudio();
    }); // end #retry-audio-load.on.click

    /**
     * Helper function to skip audio phases
     */
    function skipAudioPhases() {
        $("#btn-next-phase").html(
            'Finish & Save<br><span class="small">Skipped phases 2, 3 & 4: no audio detected</span>'
        );

        phase = 4;
    } // end skipAudioPhases

    /**
     * Loads audio for text
     */
    function loadAudio() {
        if ($("#audioplayer").length > 0) {
            var txt = $("#text").text();
            var doclang = $("html").attr("lang");

            $.ajax({
                type: "POST",
                url: "ajax/fetchaudiostream.php",
                data: { text: txt, langiso: doclang },
                dataType: "json"
            })
                .done(function(e) {
                    if (e.error != null || e.response == false) {
                        skipAudioPhases();
                        return false;
                    }
                    var $audio_player = $("#audioplayer");
                    $audio_player.find("source").attr("src", e.response);
                    $audio_player[0].load();
                    $("#audioplayer-loader").addClass("d-none");
                    $("#audioplayer").removeClass("d-none");
                    $("#audioplayer-speedbar").removeClass("d-none");
                    return true;
                })
                .fail(function(xhr) {
                    if (xhr.status == 403) {
                        $("#audioplayer-loader").addClass("d-none");
                        $("#alert-msg-audio").removeClass("d-none").empty().append('You have reached your audio streaming limit for today. Although it is possible to continue with the revision of the text, we do not recommend it. Alternatively, you can try again tomorrow or <a class="alert-link" href="gopremium.php">improve your plan</a> to increase your daily audio streaming limit.');
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

            $text_container = $("#text-container").length
                ? $("#text-container")
                : $pagereader.contents();

            highlighting = false;
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
