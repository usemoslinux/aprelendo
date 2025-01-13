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
    // word selection variables
    let long_press_timer;
    let has_long_pressed = false;
    let start_x, start_y;
    let start_index = -1;
    let current_index = -1;
    let $selword = null; // jQuery object with selected word/phrase

    let gems_earned = 0;
    
    // HTML selectors
    const doclang = $("html").attr("lang");
    const $doc = $(parent.document);
    const player = document.querySelector('video');
    
    // configuration to show confirmation dialog on close
    let show_confirmation_dialog = true; // confirmation dialog that shows when closing window before saving data

    // Fix for mobile devices where vh includes hidden address bar
    // First we get the viewport height and we multiple it by 1% to get a value for a vh unit
    let vh = window.innerHeight * 0.01;
    // Then we set the value in the --vh custom property to the root of the document
    document.documentElement.style.setProperty('--vh', `${vh}px`);

    Dictionaries.fetchURIs(); // get dictionary & translator URIs

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
            VideoActionBtns.hide();
            VideoController.pause(true);
            VideoActionBtns.show($selword);
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
            VideoController.pause(false);
            $selword = $(e.target);
            const base_uris = Dictionaries.getURIs();
            openInNewTab(LinkBuilder.forTranslationInVideo(base_uris.translator, $selword));
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
            VideoController.pause(true);
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
            VideoActionBtns.show($selword);
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
                VideoActionBtns.hide();
                VideoController.resume();
            }
        }
    }); // end $document.on.mouseup

    // *************************************************************
    // **** ACTION BUTTONS (ADD, DELETE, FORGOT & DICTIONARIES) **** 
    // *************************************************************

    /**
     * Adds selected word or phrase to the database and underlines it in the text
     */
    $("#btn-add, #btn-forgot").on("click", function (e) {
        const sel_text = $selword.text();
        const is_phrase = $selword.length > 1 ? 1 : 0;

        // add selection to "words" table
        $.ajax({
            type: "POST",
            url: "ajax/addword.php",
            data: {
                word: sel_text.toLowerCase(),
                is_phrase: is_phrase,
                source_id: $('[data-idtext]').attr('data-idtext'),
                text_is_shared: true,
                sentence: SentenceExtractor.fromVideo($selword)
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

            TextProcessor.updateAnchorsList();
        })
        .fail(function (XMLHttpRequest, textStatus, errorThrown) {
            alert(
                "Oops! There was an error adding this word or phrase to the database."
            );
        });

        VideoActionBtns.hide();
        VideoController.resume();
    }); // end #btn-add.on.click

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

                        let $result = $(TextProcessor.underlineWords(data, doclang, false));
                        let $cur_filter = {};
                        let cur_word = /""/;

                        $filter.each(function () {
                            $cur_filter = $(this);

                            $result.filter(".word").each(function (key) {
                                if (TextProcessor.langHasNoWordSeparators(doclang)) {
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

                        TextProcessor.updateAnchorsList();
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

            VideoActionBtns.hide();
            VideoController.resume();
    }); // end #btn-remove.on.click

    // *************************************************************
    // ******************* MAIN MENU BUTTONS ***********************
    // *************************************************************

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

                document.getElementById('text').innerHTML = text;

                // ajax call to underline text
                $.ajax({
                    type: "POST",
                    url: "/ajax/getuserwords.php",
                    data: { txt: $('#text').html() },
                    dataType: "json"
                })
                    .done(function (data) {
                        $('#text').html(TextProcessor.underlineWords(data, doclang, false));
                        TextProcessor.updateAnchorsList();
                    })
                    .fail(function (xhr, ajaxOptions, thrownError) {
                        console.log("There was an unexpected error trying to underline words in this text")
                    }); // end $.ajax    
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
    $(window).on("beforeunload", function () {
        if (show_confirmation_dialog) {
            return "To save your progress, please click the Save button before you go. Otherwise, your changes will "
                + "be lost. Are you sure you want to exit this page?";
        }
    }); // end window.on.beforeunload

});
