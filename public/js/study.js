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
    let dictionary_URI = "";            // user dictionary URI
    let translator_URI = "";            // user dictionary URI
    const $dic_frame = $("#dicFrame");  // dictionary iframe inside modal window
    let $sel_word = $();                // jQuery object used to open dictionary modal
    let words = [];                     // array containing all words user is learning
    let max_cards = 10;                 // maximum nr. of cards
    let cur_word_index = 0;             // current word index
    let cur_card_index = 0;             // current word index

    // nr. of words recalled during practice
    let answers = [
        ["0", 0, "bg-success", "Excellent"],
        ["1", 0, "bg-info", "Partial"],
        ["2", 0, "bg-warning", "Fuzzy"],
        ["3", 0, "bg-danger", "No recall"],
    ];

    // initialize modal dictionary window buttons
    // $("#btn-translate").hide();
    $("#btn-translate").removeClass("ps-0");
    $("#btnremove").hide();
    $("#btnadd").hide();
    $("#btncancel").addClass("ms-auto").html("&#x2715");
    $(".modal-header").addClass("p-0");

    // disable Yes/No buttons
    $(".btn-answer").prop('disabled', true);
  
    // ajax call to get dictionary URI
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

    getListofCards();

    /**
     * Shows custom message in the top section of the screen
     * @param {string} html
     * @param {string} type
     */
    function showMessage(html, type) {
        $("#alert-msg")
            .html(html)
            .removeClass()
            .addClass("alert " + type);
        $(window).scrollTop(0);
    } // end showMessage()

    /**
     * Fetches list of words user is learning
     */
    function getListofCards() {
        $.ajax({
            type: "POST",
            url: "ajax/getcards.php",
            dataType: "json"
            })
            .done(function(data) {
                words = data.map(function(value,index) { 
                    return value.word.replace(/\r?\n|\r/g, " "); 
                });
                max_cards = words.length > max_cards ? max_cards : words.length;

                $("#card-counter").text("1" + "/" + max_cards);
                getExampleSentencesforCard(words[0]);
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                showMessage("Oops! There was an unexpected error trying to fetch the list of words you are learning "
                    + "in this language.", "alert-danger");
            }); // end $.ajax
    } // end getListofCards()

    /**
     * Fetches examples sentences for a specific word
     * @param {string} word
     */
    function getExampleSentencesforCard(word) {
        // if deck is empty or last card is reached, exit
        if (lastCardReached()) {
            return;
        }
        
        // empty card and show spinner
        $("#card-loader").removeClass('d-none');
        $("#card-text").empty();
        $("#card-header").html("Looking for examples of " + word + "...");

        $.ajax({
            type: "POST",
            url: "ajax/getcards.php",
            data: { word: word },
            dataType: "json"
            })
            .done(function(data) {
                let examples = "";
                let examples_count = 0;
                const lang_iso = $("#card").data("lang");
                let sentence_regex = new RegExp(
                    '([^\\n.?!]|[\\d][.][\\d]|[A-Z][.](?:[A-Z][.])+)*(?<![\\p{L}])' + word + '(?![\\p{L}])([^\\n.?!]|[.][\\d]|[.](?:[A-Z][.])+)*[\\n.?!]',
                    'gmiu'
                );

                // different sentence separator for Japanese and Chinese, as
                // they don't separate words and finish sentences with 。
                if (lang_iso == "ja" || lang_iso == "zh") {
                    sentence_regex = new RegExp(
                        '[^\n?!。]*' + word + '[^\n?!。]*[\n?!。]',
                        'gmiu'
                    );    
                } 

                const word_regex = new RegExp('(?<![\\p{L}|\\d])' + word + '(?![\\p{L}|\\d])', 'gmiu');
                const spaces_symbols_regex = new RegExp('^[\\s\\d]+|[\\s\\d]+$' , 'g');
                
                data.forEach(text => {                   
                    // extract example sentences from text
                    let m;
                    while ((m = sentence_regex.exec(text.text)) !== null) {
                        // This is necessary to avoid infinite loops with zero-width matches
                        if (m.index === sentence_regex.lastIndex) {
                            sentence_regex.lastIndex++;
                        }
                        
                        if (examples_count < 3) {
                            // create html for each example sentence, max 3 examples
                            let match = m[0];
                            // first, remove leading/trailing spaces and leading symbols/numbers from sentences
                            match = match.replace(spaces_symbols_regex, '');
                            // remove unclosed quotes
                            match = removeUnclosedQuotes(match);
                            // check that match is not the only word in current example sentence
                            if (match !== word) {
                                // make the word user is studying clickable
                                match = match.replace(word_regex, function(match, g1) {
                                    return g1 === undefined
                                        ? match
                                        : "<a class='word fw-bold'>" + match.replace(/\s\s+/g, ' ') + "</a>";
                                });
                                // make sure example sentence is unique, then add to the list
                                if (examples.search(escapeRegex(match)) == -1) {
                                    examples += "<blockquote cite='" + text.source_uri + "'>";
                                    examples += "<p>" + match + "</p>";
                                    examples += "<cite>" + (text.author == "" ? "Anonymous" : text.author);
                                    examples += ", <a href='" + text.source_uri + "' target='_blank'>" + text.title + "</a></cite>"
                                    examples += "</blockquote>"
                                    examples_count++;    
                                }
                            }
                        }
                    }
                });

                // if example sentence is empty, go to next card
                if (examples == "") {
                    $("#card-header").html("Skipped. No examples found.");
                    cur_word_index++;
                    getExampleSentencesforCard(words[cur_word_index]);
                    return;
                }

                // show card
                $("#card").data('word', word);
                $("#card-loader").addClass('d-none');
                $("#card-counter").text((cur_card_index+1) + "/" + max_cards);
                $("#card-header").html(word);
                $("#card-text").append(examples);
                $(".btn-answer").prop('disabled', false);
                cur_word_index++;
                cur_card_index++;
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                showMessage("Oops! There was an unexpected error trying to fetch example sentences for this word.", 
                    "alert-danger");
            }); // end $.ajax
    } // end getExampleSentencesforCard()

    /**
     * Escapes regex strings
     */
    function escapeRegex(str) {
        return str.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
    } // end escapeRegex()

    function removeUnclosedQuotes(sentence) {
        const count = (sentence.match(/"/g) || []).length;
        
        if (count % 2 !== 0) {
          if (sentence.length <= 4) return sentence;
            let firstFive = sentence.substring(0, 2).replace("\"", "");
            let lastFive = sentence.substring(sentence.length - 2).replace("\"", "");
            return firstFive + sentence.substring(2, sentence.length - 2) + lastFive;
          }
          
        return sentence;
    } // end removeUnclosedQuotes()

    /**
     * Checks if there are any cards in deck or if end of practice was reached
     * In that case, show respective message to user
     */
    function lastCardReached() {
        if (max_cards == 0) { 
            $("#card-header").text("Sorry, no cards to practice");
            $("#card-text").html("<div class='fas fa-exclamation-circle text-danger display-3'>"
                + "</div><div class='mt-3'>It seems you don't have any cards left for learning.</div>");
            $("#card-footer").addClass("d-none");
            $("#card-loader").addClass("d-none");
            return true;
        } else if (cur_card_index > max_cards-1) {
            $("#card-header").text("Congratulations!");

            let progress_html = "";
            for(let i = 0; i < answers.length; i++) {
                let subtotal = answers[i][1];
                let percentage = subtotal / max_cards * 100;
                let bg_class = answers[i][2];
                let title = answers[i][3];

                progress_html += "<div class='progress-bar " + bg_class + "' role='progressbar' aria-valuenow='" + 
                    percentage + "' aria-valuemin='0' aria-valuemax='100' style='width: " + percentage + "%' title='" +
                    title + ": " + subtotal + " answer(s)'>" + percentage + " %</div>";
            }

            $("#card-text").html("<div class='fa-solid fa-flag-checkered text-primary display-3 mt-3'></div>"
                + "<div class='mt-3'>You have reached the end of your study.</div>"
                + "<div class='mt-3'>These were your results:</div>"
                + "<div class='progress mx-auto mt-3 fw-bold' style='height: 25px;max-width: 550px'>" + progress_html + "</div>"
                + "<div class='small mt-4'>If you want to continue, you can "
                + "refresh this page (F5).<br>However, we strongly recommend that you keep your study sessions short "
                + "and take rest intervals.</div>");
            $("#card-footer").addClass("d-none");
            $("#card-loader").addClass("d-none");
            return true;
        }

        return false;
    } // end lastCardReached()

    /**
     * Open dictionary modal
     * Triggers when user clicks word
     * @param {event object} e
     */
    $("body").on("click", ".word", function(e) {
        $sel_word = $(this);
        const url = dictionary_URI.replace("%s", encodeURI($sel_word.text()));

        // set up buttons
        $("#btnadd").text("Forgot");
        
        // show loading spinner
        $("#loading-spinner").attr('class','lds-ellipsis m-auto');
        $dic_frame.attr('class','d-none');

        $dic_frame.get(0).contentWindow.location.replace(url);
        // the previous line loads iframe content without adding it to browser history,
        // as this one does: $dic_frame.attr('src', url);

        $("#dic-modal").modal("show");
    }); // end #.word.on.click

    /**
     * Hides loader spinner when dictionary iframe finished loading
     */
     $dic_frame.on("load", function() {
        $("#loading-spinner").attr('class','d-none');
        $dic_frame.removeClass();
    }); // end $dic_frame.on.load()

    /**
     * Triggers when user clicks on answer (Yes/No) buttons
     */
    $(".btn-answer").click(function (e) { 
        e.preventDefault();
        const word = $("#card").data('word');
        const answer = $(this).attr("value");

        answers[answer][1] = answers[answer][1] + 1;

        // disable Yes/No buttons
        $(".btn-answer").prop('disabled', true);

        // update card status
        $.ajax({
            type: "POST",
            url: "ajax/updatecard.php",
            data: { word: word, answer: answer }
            // dataType: "json"
        })
        .done(function(data) {
            // go to next card
            getExampleSentencesforCard(words[cur_word_index]);
        })
        .fail(function(xhr, ajaxOptions, thrownError) {
            showMessage("There was an unexpected error updating this word's status", "alert-danger");
        });
    }); // end .btn-answer.on.click()

    /**
     * Builds translator link using the word object as a parameter
     */
    function buildTranslationLink($word) {
        const sentence = $word.parent("p").text().trim();

        return translator_URI.replace("%s", encodeURIComponent(sentence));
    } // end buildTranslationLink

    /**
     * Opens translator in new window. 
     * Triggers when user click in translate button in modal window
     */
     $("#btn-translate").on("click", function() {
        window.open(buildTranslationLink($sel_word));
    }); // end #btn-translate.on.click()

    /**
     * Disables right click context menu
     */
     $(document).on("contextmenu",function(e){
        // opens dictionary translator in case user right clicked on a word/phrase
        // but only on desktop browsers
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

        if (!isMobile && $(e.target).is(".word")) {
            window.open(buildTranslationLink($(e.target)));
        }
        return false;
     }); // end document.contextmenu

    /**
     * Implements shortcuts for buttons
     */
    $(document).keypress(function (e) {
        // only allow shortcuts if buttons are enabled
        if (!$(".btn-answer").prop('disabled')) {
            switch (e.which) {
                case 49: // 49 is the keycode for "1" key
                    $("#btn-answer-no-recall").click();
                    break;
                case 50: // 50 is the keycode for "2" key
                    $("#btn-answer-fuzzy").click();
                    break;
                case 51: // 51 is the keycode for "3" key
                    $("#btn-answer-partial").click();
                    break;
                case 52: // 52 is the keycode for "4" key
                    $("#btn-answer-excellent").click();
                    break;
                default:
                    break;
            }
        }
    });
});
