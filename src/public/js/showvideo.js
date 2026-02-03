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
    async function underlineText() {
        try {
            const form_data = new URLSearchParams({ txt: $('#text').html() });
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
        }
    }

    // *************************************************************
    // ****************** AUDIO/VIDEO CONTROLLER ******************* 
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
    $("#btn-add, #btn-forgot").on("click", async function (e) {
        const $selword = WordSelection.get();
        const sel_text = $selword.text();
        const is_phrase = $selword.length > 1 ? 1 : 0;

        try {
            const form_data = new URLSearchParams({
                word: sel_text.toLowerCase(),
                is_phrase: is_phrase,
                source_id: $('[data-idtext]').attr('data-idtext'),
                text_is_shared: true,
                sentence: SentenceExtractor.extractSentence($selword)
            });

            const response = await fetch("/ajax/addword.php", {
                method: "POST",
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to add word.');
            }

            // underline word or phrase
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

        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }

        VideoActionBtns.hide();
        VideoController.resume();
    }); // end #btn-add.on.click

    /**
     * Remove selected word or phrase from database
     */
    $("#btn-remove").on("click", async function () {
        const $selword = WordSelection.get();

        try {
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

            let $filter = $("a.word").filter(function () {
                return (
                    $(this)
                        .text()
                        .toLowerCase() === $selword.text().toLowerCase()
                );
            });

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
                TextProcessor.updateAnchorsList();
            });
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }

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
    $(document).on("click", "#btn-save-ytvideo", async function () {
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

        try {
            const update_words_response = await fetch("/ajax/updatewords.php", {
                method: "POST",
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    words: JSON.stringify(oldwords),
                    textIDs: JSON.stringify(ids)
                })
            });

            if (!update_words_response.ok) { throw new Error(`HTTP error: ${update_words_response.status}`); }

            const update_words_data = await update_words_response.json();

            if (!update_words_data.success) {
                throw new Error(update_words_data.error_msg || 'Failed to update words status.');
            }

            const review_data = {
                    words: {
                        new: getUniqueElements('.reviewing.new'),
                        learning: getUniqueElements('.reviewing.learning'),
                        forgotten: getUniqueElements('.reviewing.forgotten')
                    },
                    texts: { reviewed: 1 }
                };

                const update_user_score_response = await fetch("/ajax/updateuserscore.php", {
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
                    '<input type="hidden" name="is_shared" value="1" />' +
                    "</form>"
                );
                $("body").append(form);
                form.trigger("submit");
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
    }); // end #btn-save-ytvideo.on.click

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
            return 'Press Save before you go or your changes will be lost.';
        }
    }); // end window.on.beforeunload
});
