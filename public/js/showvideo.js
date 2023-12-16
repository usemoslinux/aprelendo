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

$(document).ready(function () {
    let highlighting = false;
    let $sel_start, $sel_end;
    let start_sel_time, end_sel_time;
    let start_sel_pos_top; // used in mobile devices to activate "word/phrase selection mode"
    let swiping = false; // used in mobile devices to activate "word/phrase selection mode"
    let $selword = null; // jQuery object of the selected word/phrase
    let dictionary_URI = "";
    let translator_URI = "";
    let translate_paragraph_link = "";
    let resume_video = false;
    let video_paused = false;
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
            dictionary_URI = data.dictionary_uri;
            translator_URI = data.translator_uri;
        }
    });

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

    /**
     * Disable right click context menu 
     */
    $(document).on("contextmenu", ".word", function (e) {
        e.preventDefault();
        return false;
    }); // end .word.on.contextmenu

    /**
     * Word/Phrase selection start
     * @param {event object} e
     */
    $(document).on("mousedown touchstart", ".word", function (e) {
        e.stopPropagation();

        video_paused = player.getPlayerState() != 1;

        // if there is video playing
        if (!video_paused) {
            player.pauseVideo();
            resume_video = true;
        }

        if (e.which < 2) {
            // if left mouse button / touch...
            highlighting = true;
            $sel_start = $sel_end = $(this);
            if (e.type == "touchstart") {
                start_sel_time = new Date();
                start_sel_pos_top = $sel_start.offset().top - $(window).scrollTop();
            }
        } else if (e.which == 3) {
            // on right click show translation of the whole sentence
            $selword = $(this);
            window.open(buildTranslationLink(translator_URI, $selword));
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
            $('html').css({ 'overflow': 'visible' });
            swiping = false;
        }

        if (highlighting) {
            if (e.which < 2) {
                // if left mouse button / touch...
                highlighting = false;
                if ($sel_start === $sel_end) {
                    $selword = $(this);
                }
                showModal();
            }
        }
    }); // end .word.on.mouseup/touchend

    /**
     * Determines if an element is after another one
     * @param {Jquery object} sel
     */
    $.fn.isAfter = function (sel) {
        return this.prevUntil(sel).length !== this.prevAll().length;
    }; // end $.fn.isAfter

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
            if (e.type == "touchmove") {
                $('html').css({ 'overflow': 'hidden' });
            }

            $(".word").removeClass("highlighted");

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
    $("#btnadd").on("click", function () {
        const sel_text = $selword.text();
        const is_phrase = $selword.length > 1 ? 1 : 0;
        // add selection to "words" table
        $.ajax({
            type: "POST",
            url: "ajax/addword.php",
            data: {
                word: sel_text.toLowerCase(),
                is_phrase: is_phrase
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
                                "<a class='word reviewing new' data-toggle='modal' data-bs-target='#dic-modal'></a>"
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
                                "<a class='word reviewing forgotten' data-toggle='modal' data-bs-target='#dic-modal'></a>"
                            );
                        } else {
                            $word.wrap(
                                "<a class='word reviewing new' data-toggle='modal' data-bs-target='#dic-modal'></a>"
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
    }); // end #btnadd.on.click

    /**
     * Updates vh value on window resize
     * Fix for mobile devices where vh includes hidden address bar
     */
    $(window).on('resize', function () {
        vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    });

    /**
     * Shows dictionary when user clicks a word
     * All words are enclosed in a.word tags
     */
    function showModal() {
        getWordFrequency($selword.text(), doclang);
        setAddDeleteButtons($selword);

        $("#loading-spinner").attr('class', 'lds-ellipsis m-auto');
        $("#dicFrame").attr('class', 'd-none');

        // build translate sentence url
        translate_paragraph_link = buildTranslationLink(translator_URI, $selword);

        // show dictionary
        const selword_text = $selword.text().replace(/(\r\n|\n|\r)/gm, " ");
        const url = dictionary_URI.replace("%s", encodeURIComponent(selword_text));

        $(parent.document)
            .find("#dicFrame")
            .get(0)
            .contentWindow.location.replace(url);
        $("#btnadd").focus();
        // the previous line loads iframe content without adding it to browser history,
        // as this one does: $('#dicFrame').attr('src', url);

        $(parent.document)
            .find("#dic-modal")
            .modal("show");
    } // end showModal

    /**
     * Remove selected word or phrase from database
     */
    $("#btnremove").on("click", function () {
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
    }); // end #btnremove.on.click

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
     * Resumes video when modal window is closed
     */
    $("#dic-modal").on("hidden.bs.modal", function () {
        if (resume_video) {
            player.playVideo();
            resume_video = false;
        }

        // removes word selection
        $selword.removeClass("highlighted");
    }); // end #dic-modal.on.hidden.bs.modal

    /**
     * Hides loader spinner when dictionary iframe finished loading
     */
    $("#dicFrame").on("load", function () {
        $("#loading-spinner").attr('class', 'd-none');
        $(this).removeClass();
    }); // end #dicFrame.on.load()

    $("#btn-translate").on("click", function () {
        window.open(translate_paragraph_link, '_blank', 'noopener');
    }); // end #btn-translate.on.click()

    /**
     * Removes word highlighting when user opens dictionary for word
     */
    $("#text-container").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        highlighting = false;
        $("#text-container")
            .find(".highlighted")
            .removeClass("highlighted");
    }); // end #text-container.on.click

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
