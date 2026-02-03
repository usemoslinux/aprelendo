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

    $("#btn-add, #btn-forgot").on("click", async function (e) {
        const $selword = WordSelection.get();
        const sel_text = $selword.text();
        const is_phrase = $selword.length > 1 ? 1 : 0;

        try {
            const form_data = new URLSearchParams({
                word: sel_text.toLowerCase(),
                is_phrase: is_phrase,
                source_id: $('[data-idtext]').attr('data-idtext') || '',
                text_is_shared: false,
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
                const first_word = $selword.eq(0).text();
                const phrase_ext = $selword.filter(".word").length;
                let $filter_phrase = $("a.word").filter(function () {
                    return (
                        $(this)
                            .text()
                            .toLowerCase() === first_word.toLowerCase()
                    );
                });

                $filter_phrase.each(function () {
                    let last_word = $(this)
                        .nextAll("a.word")
                        .slice(0, phrase_ext - 1)
                        .last();
                    let phrase = $(this)
                        .nextUntil(last_word)
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
                let $filter_word = $("a.word").filter(function () {
                    return (
                        $(this)
                            .text()
                            .toLowerCase() === sel_text.toLowerCase()
                    );
                });

                $filter_word.each(function () {
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

                $filter_word.contents().unwrap();
            }

            TextProcessor.updateAnchorsList();

        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }

        VideoActionBtns.hide();
        VideoController.resume();
    }); // end #btn-add.on.click

    $("#btn-remove").on("click", async function () {
        const $selword = WordSelection.get();

        try {
            const remove_word_response = await fetch("/ajax/removeword.php", {
                method: "POST",
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ word: $selword.text().toLowerCase() })
            });

            if (!remove_word_response.ok) throw new Error(`HTTP error: ${remove_word_response.status}`);

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

            if (!get_user_words_response.ok) throw new Error(`HTTP error: ${get_user_words_response.status}`);

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
            });

            TextProcessor.updateAnchorsList();
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
    $("#btn-save-offline-video").on("click", updateWordsLearningStatus);

    /**
     * Archives text and updates status of all underlined words & phrases
     */
    async function updateWordsLearningStatus() {
        // build array with underlined words
        let oldwords = [];
        let ids = [];
        let word = "";
        $("#text").find(".reviewing").each(function () {
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
            form.trigger( "submit" );
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
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

            reader.addEventListener('load', async (event) => {
                const srt = event.target.result;
                const data = parser.fromSrt(srt, true);
                let text = '';

                for (const element of data) {
                    let line = '<span';

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
                                line += '>' + value.replace(/(\r\n|\n|\r)/g, " ");
                                break;
                            default:
                                break;
                        }
                    }

                    line += '</span>' + "\r\n";
                    text += line;
                }

                document.getElementById('text').innerHTML = text;

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
            });

            reader.readAsText(file);
            $("#nosubs").remove(); // remove "no subtitles loaded" message
            $("#btn-save-offline-video").removeClass("disabled"); // enable save button
        }
    }); // end #subs-file-input.on.change

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
