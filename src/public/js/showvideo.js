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
    let gems_earned = 0;
    
    // HTML selectors
    const doclang = $("html").attr("lang");

    // configuration to show confirmation dialog on close
    let show_confirmation_dialog = true; // confirmation dialog that shows when closing window before saving data

    // Fix for mobile devices where vh includes hidden address bar
    // First we get the viewport height and we multiple it by 1% to get a value for a vh unit
    let vh = window.innerHeight * 0.01;
    // Then we set the value in the --vh custom property to the root of the document
    document.documentElement.style.setProperty('--vh', `${vh}px`);

    // initialize video player
    initializeVideoPlayer(0); // start at 0 seconds
    
    // initial AJAX calls
    underlineText(); // underline text with user words/phrases
    Dictionaries.fetchURIs(); // get dictionary & translator URIs

    /**
     * Fetches user words/phrases from the server and underlines them in the text, but only if this
     * is a simple text, not an ebook
     */
    function underlineText() {
        $.ajax({
            type: "POST",
            url: "/ajax/getuserwords.php",
            data: { txt: $('#text').html() },
            dataType: "json"
        })
        .done(function (data) {
            $('#text').html(TextUnderliner.apply(data, doclang, false));
            TextProcessor.updateAnchorsList();
        })
        .fail(function (xhr, ajaxOptions, thrownError) {
            console.log("There was an unexpected error trying to underline words in this text")
        }); // end $.ajax    
    }

    // *************************************************************
    // ******************* WORD/PHRASE SELECTION ******************* 
    // *************************************************************

    WordSelection.setupEvents({
        actionBtns: VideoActionBtns,
        controller: VideoController,
        linkBuilder: LinkBuilder.forTranslationInVideo
    });

    // *************************************************************
    // **** ACTION BUTTONS (ADD, DELETE, FORGOT & DICTIONARIES) **** 
    // *************************************************************

    /**
     * Adds selected word or phrase to the database and underlines it in the text
     */
    $("#btn-add, #btn-forgot").on("click", function (e) {
        const $selword = WordSelection.get();
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
                sentence: SentenceExtractor.extractSentence($selword)
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
        const $selword = WordSelection.get();
        
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

                    let $result = $(TextUnderliner.apply(data, doclang, false));
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
                        TextProcessor.updateAnchorsList();
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
    $(document).on("click", "#btn-save-ytvideo", function () {
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
    }); // end #btn-save-ytvideo.on.click

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
            return "To save your progress, please click the Save button before you go. Otherwise, your changes "
                + "will be lost. Are you sure you want to exit this page?";
        }
    }); // end window.on.beforeunload
});
