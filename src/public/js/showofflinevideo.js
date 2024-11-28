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

$(document).ready(function () {
    let player = document.querySelector('video');
    let highlighting = false;
    let $sel_start, $sel_end;
    let start_sel_time, end_sel_time;
    let start_sel_pos_top; // used in mobile devices to activate "word/phrase selection mode"
    let swiping = false; // used in mobile devices to activate "word/phrase selection mode"
    let $selword = null; // jQuery object of the selected word/phrase
    let dictionary_URI = "";
    let img_dictionary_URI = "";
    let translator_URI = "";
    let show_confirmation_dialog = true; // confirmation dialog that shows when closing window before saving data
    let gems_earned = 0;
    let doclang = $("html").attr("lang");

    // Fix for mobile devices where vh includes hidden address bar
    // First we get the viewport height and we multiple it by 1% to get a value for a vh unit
    let vh = window.innerHeight * 0.01;
    // Then we set the value in the --vh custom property to the root of the document
    document.documentElement.style.setProperty('--vh', `${vh}px`);

    // ajax call to get dictionary & translator URIs
    $.ajax({
        url: "ajax/getdicuris.php",
        type: "GET",
        dataType: "json"
    }).done(function (data) {
        if (data.error_msg == null) {
            dictionary_URI     = data.dictionary_uri;
            img_dictionary_URI = data.img_dictionary_uri;
            translator_URI     = data.translator_uri;
        }
    });

    /**
     * Disable right click context menu 
     */
    $(document).on("contextmenu", function (e) {
        e.preventDefault();
        return false;
    }); // end .word.on.contextmenu

    /**
     * Word/Phrase selection start
     * @param {event object} e
     */
    $(document).on("mousedown touchstart", ".word", function (e) {
        e.stopPropagation();

        hideActionButtonsPopUpToolbar();

        if (e.which < 2) {
            // if left mouse button / touch...
            video_controller.pause(true);
            highlighting = true;
            $sel_start = $sel_end = $(this);
            if (e.type == "touchstart") {
                start_sel_time = new Date();
                start_sel_pos_top = $sel_start.offset().top - $(window).scrollTop();
            }
        } else if (e.which == 3) {
            // on right click show translation of the whole sentence
            video_controller.pause(false);
            $selword = $(this);
            openInNewTab(buildVideoTranslationLink(translator_URI, $selword));
        }
    }); // end .word.on.mousedown/touchstart

    /**
     * Word/Phrase selection end
     * @param {event object} e
     */
    $(document).on("mouseup touchend", ".word", function (e) {
        e.stopPropagation();

        end_sel_time = new Date();

        if (e.type == "touchend") {
            if (!swiping) {
                highlighting = (end_sel_time - start_sel_time) > 1000;
            }
            swiping = false;
        }

        if (highlighting) {
            if (e.which < 2) {
                // if left mouse button / touch...
                highlighting = false;
                if ($sel_start === $sel_end) {
                    $selword = $(this);
                }

                showActionButtonsPopUpToolbar();
            }
        }
    }); // end .word.on.mouseup/touchend

    /**
     * Word/Phrase selection
     * While user drags the mouse without releasing the mouse button
     * or while touches an elements an moves the pointer without releasing
     * Here we build the selected phrase & change its background color to gray
     * @param {event object} e
     */
    $(document).on("mouseover touchmove", ".word", function (e) {
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
            $(".highlighted").removeClass("highlighted"); // remove previous highlighting

            $sel_end =
                e.type === "mouseover" ? $(this) : $(
                    document.elementFromPoint(
                        e.originalEvent.touches[0].clientX,
                        e.originalEvent.touches[0].clientY
                    )
                );

            // if $sel_start & $sel_end are on the same line, then...
            if ($sel_start.parent().get(0) === $sel_end.parent().get(0)) {
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
            } else {
                $sel_end = $selword = $sel_start;
            }
        }
    }); // end .word.on.mouseover/touchmove

    /**
     * Adds selected word or phrase to the database and underlines it in the text
     */
    $("#btn-add, #btn-forgot").on("click", function () {
        const sel_text = $selword.text();
        const is_phrase = $selword.length > 1 ? 1 : 0;
        // add selection to "words" table
        $.ajax({
            type: "POST",
            url: "ajax/addword.php",
            data: {
                word: sel_text.toLowerCase(),
                is_phrase: is_phrase,
                source_id: null,
                text_is_shared: null,
                sentence: getVideoSentence($selword)
            }
        })
            .done(function () {
                // if successful, underline word or phrase
                if (is_phrase) {
                    // if it's a phrase
                    const firstword = $selword.eq(0).text();
                    const phraseext = $selword.filter(".word").length;
                    let $filterphrase = $("a.word").filter(function () {
                        return (
                            $(this)
                                .text()
                                .toLowerCase() === firstword.toLowerCase()
                        );
                    });

                    $filterphrase.each(function () {
                        let lastword = $(this)
                            .nextAll("a.word")
                            .slice(0, phraseext - 1)
                            .last();
                        let phrase = $(this)
                            .nextUntil(lastword)
                            .addBack()
                            .next("a.word")
                            .addBack();

                        if (
                            phrase.text().toLowerCase() ===
                            sel_text.toLowerCase()
                        ) {
                            phrase.wrapAll(
                                "<a class='word reviewing new'></a>"
                            );

                            phrase.contents().unwrap();
                        }
                    });
                } else {
                    // if it's a word
                    let $filterword = $("a.word").filter(function () {
                        return (
                            $(this)
                                .text()
                                .toLowerCase() === sel_text.toLowerCase()
                        );
                    });

                    $filterword.each(function () {
                        let $word = $(this);
                        if ($word.is(".new, .learning, .learned, .forgotten")) {
                            $word.wrap(
                                "<a class='word reviewing forgotten'></a>"
                            );
                        } else {
                            $word.wrap(
                                "<a class='word reviewing new'></a>"
                            );
                        }
                    });

                    $filterword.contents().unwrap();
                }
            })
            .fail(function (XMLHttpRequest, textStatus, errorThrown) {
                alert(
                    "Oops! There was an error adding this word or phrase to the database."
                );
            });

            hideActionButtonsPopUpToolbar();
            video_controller.resume();
    }); // end #btn-add.on.click

    /**
     * Updates vh value on window resize
     * Fix for mobile devices where vh includes hidden address bar
     */
    $(window).on('resize', function () {
        vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    });

    /**
     * Remove selected word or phrase from database
     */
    $("#btn-remove").on("click", function () {
        $.ajax({
            type: "POST",
            url: "ajax/removeword.php",
            data: {
                word: $selword.text().toLowerCase()
            }
        })
            .done(function () {
                let $filter = $("a.word").filter(function () {
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
                    .done(function (data) {
                        // if everything went fine, remove the underlining and underline once again the whole selection
                        // also, the case of the word/phrase in the text has to be respected
                        // for phrases, we need to make sure that new underlining is added for each word

                        let $result = $(underlineWords(data, doclang, false));
                        let $cur_filter = {};
                        let cur_word = /""/;

                        $filter.each(function () {
                            $cur_filter = $(this);

                            $result.filter(".word").each(function (key) {
                                if (langs_with_no_word_separator.includes(doclang)) {
                                    cur_word = new RegExp(
                                        "(?<![^])" + $(this).text() + "(?![$])",
                                        "iug"
                                    ).exec($cur_filter.text());
                                }
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
                    })
                    .fail(function (xhr, ajaxOptions, thrownError) {
                        console.log("There was an unexpected error trying to underline words in this text")
                    }); // end $.ajax    
            })
            .fail(function (XMLHttpRequest, textStatus, errorThrown) {
                alert(
                    "Oops! There was an error removing the word from the database."
                );
            });

            hideActionButtonsPopUpToolbar();
            video_controller.resume();
    }); // end #btn-remove.on.click

    /**
     * Finished studying this text. Archives text & saves new status of words/phrases
     * Executes when the user presses the big green button at the end
     */
    $("#btn-save-offline-video").on("click", archiveTextAndSaveWords);

    /**
     * Archives text and updates status of all underlined words & phrases
     */
    function archiveTextAndSaveWords() {
        // build array with underlined words
        let oldwords = [];
        let ids = [];
        let word = "";
        $(".learning").each(function () {
            word = $(this)
                .text()
                .toLowerCase();
            if ($.inArray(word, oldwords) == -1) {
                oldwords.push(word);
            }
        });

        ids.push($("#text-container").attr("data-IdText")); // get text ID

        $.ajax({
            type: "POST",
            url: "ajax/archivetext.php",
            data: {
                words: oldwords,
                textIDs: JSON.stringify(ids)
            }
        })
            .done(function (data) {
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
                        .done(function (data) {
                            // show text review stats
                            if (data.error_msg == null) {
                                gems_earned = data.gems_earned;
                                show_confirmation_dialog = false;
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
                                    '<input type="hidden" name="is_shared" value="1" />' +
                                    "</form>"
                                );
                                $("body").append(form);
                                form.submit();
                            } else {
                                alert("Oops! There was an unexpected error.");
                            }
                        })
                        .fail(function (XMLHttpRequest, textStatus, errorThrown) {
                            alert("Oops! There was an unexpected error.");
                        });
                } else {
                    alert("Oops! There was an unexpected error.");
                }
            })
            .fail(function (XMLHttpRequest, textStatus, errorThrown) {
                alert("Oops! There was an error updating the database.");
            });
    } // end #btn-save-offline-video.on.click

    /**
     * Removes selection when user clicks in white-space
     */
    $(document).on("mouseup touchend", "#text-container", function(e) {
        if ($(e.target).is(".word") === false && !$(e.target).closest('#action-buttons').length > 0) {
            e.stopPropagation();

            let $text_container = $("#text-container");

            highlighting = false;
            $text_container.find(".highlighted").removeClass("highlighted");
            hideActionButtonsPopUpToolbar();
            video_controller.resume();
        }
    }); // end $pagereader.on.mouseup

    /**
     * Open video selection dialog
     */
    $("#btn-selvideo").on("click", function (e) {
        e.preventDefault();
        $("#video-file-input").trigger('click');
    }); // end #btn-selvideo.on.click

    /**
     * After user selects video, load it
     */
    $("#video-file-input").on("change", function () {
        if (this.files[0]) {
            const file = this.files[0];
            const type = file.type;
            if (player.canPlayType(type)) {
                const fileURL = URL.createObjectURL(file);
                player.src = fileURL;
            }
        }
    }); // end #video-file-input.on.change

    /**
     * Open subtitle selection dialog
     */
    $("#btn-selsubs").on("click", function (e) {
        e.preventDefault();
        $("#subs-file-input").trigger('click');
    }); // end #btn-selsubs.on.click
    
    /**
     * Load subtitles selected by user
     */
    $("#subs-file-input").on("change", function () {
        if (this.files[0]) {
            const file = this.files[0];
            const reader = new FileReader();

            reader.addEventListener('load', (event) => {
                const srt = event.target.result;
                const data = parser.fromSrt(srt, true);
                let text = '';

                for (const element of data) {
                    let line = '<div class="text-center"';

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
                                line += '>' + value;
                                break;
                            default:
                                break;
                        }
                    }

                    line += '</div>';
                    text += line;
                }

                document.getElementById('text-container').innerHTML = text;

                // ajax call to underline text
                $.ajax({
                    type: "POST",
                    url: "/ajax/getuserwords.php",
                    data: { txt: $('#text-container').html() },
                    dataType: "json"
                })
                    .done(function (data) {
                        $('#text-container').html(underlineWords(data, doclang, false));
                    })
                    .fail(function (xhr, ajaxOptions, thrownError) {
                        console.log("There was an unexpected error trying to underline words in this text")
                    }); // end $.ajax    
                // document.getElementById('text-container').innerText = JSON.stringify(data);
            });

            reader.readAsText(file);
        }
    }); // end #subs-file-input.on.change

    /**
     * Toggle fullscreen mode
     */
    $("#btn-fullscreen").on("click", function(e) {
        // Check if we're already in fullscreen
        if (document.fullscreenElement) {
            // Exit fullscreen
            document.exitFullscreen().catch((err) => {
                alert(`An error occurred while trying to exit fullscreen mode: ${err.message} (${err.name})`);
            });
        } else {
            // Request fullscreen
            let elem = document.documentElement;
    
            elem.requestFullscreen({ navigationUI: "show" })
                .catch((err) => {
                    alert(`An error occurred while trying to switch into fullscreen mode: ${err.message} (${err.name})`);
                });
        }
    }); // end #btn-fullscreen.on.click

    /**
     * Shows dialog message reminding users to save changes before leaving
     */
    $(window).on("beforeunload", function () {
        if (show_confirmation_dialog) {
            return "To save your progress, please click the Save button before you go. Otherwise, your changes will "
                + "be lost. Are you sure you want to exit this page?";
        }
    }); // end window.on.beforeunload
    
    /**
     * Shows dictionary when user clicks a word
     * All words are enclosed in a.word tags
     */
    function showActionButtonsPopUpToolbar() {
        // TODO: IS WORD FREQUENCY STILL NECESSARY? HOW CAN I REINVENT THIS?
        // getWordFrequency($selword.text(), doclang);
        $("#text-container").disableScroll();
        setWordActionButtons($selword, false);

        const base_uris = {
            dictionary: dictionary_URI,
            img_dictionary: img_dictionary_URI,
            translator: translator_URI
        };

        setDicActionButtonsClick($selword, base_uris, 'video');
        showActionButtons($selword);
    } // end showActionButtonsPopUpToolbar

    /**
     * Hides actions pop up toolbar
     */
    function hideActionButtonsPopUpToolbar() {
        $("#text-container").enableScroll();
        hideActionButtons();
    } // end hideActionButtonsPopUpToolbar
});
