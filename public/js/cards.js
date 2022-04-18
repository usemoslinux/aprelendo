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
    var dictionary_URI = "";            // user dictionary URI
    var translator_URI = "";            // user dictionary URI
    var $dic_frame = $("#dicFrame");    // dictionary iframe inside modal window
    var $sel_word = $();                // jQuery object used to open dictionary modal
    var words = [];                     // array containing all words user is learning
    var max_cards = 20;                 // maximum nr. of cards
    var cur_word_index = 0;             // current word index
    var cur_card_index = 0;             // current word index

    // initialize modal dictionary window buttons
    // $("#btn-translate").hide();
    $("#btn-translate").removeClass("pl-0");
    $("#btnremove").hide();
    $("#btnadd").hide();
    $("#btncancel").addClass("ml-auto").html("&#x2715");
    $(".modal-header").addClass("p-0");

    // disable Yes/No buttons
    $(".btn-remember").prop('disabled', true);
  
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
    } // end showMessage

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
                // words = data.slice(0,50);
                
                words = data.map(function(value,index) { 
                    return value.word; 
                });

                max_cards = words.length > max_cards ? max_cards : words.length;

                $("#card-counter").text("1" + "/" + max_cards);
                getExampleSentencesforCard(words[0]);
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                showMessage("Oops! There was an unexpected error trying to fetch the list of words you are learning in this language.", "alert-danger");
            }); // end $.ajax
    }

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
        $("#card-header").html("Looking for examples of <b>" + word + "</b>...");

        $.ajax({
            type: "POST",
            url: "ajax/getcards.php",
            data: { word: word },
            dataType: "json"
            })
            .done(function(data) {
                // alert(data);
                var examples = "";
                var sentence = "";
                // old sentence_regex '[^\\n.?!]*(?<=[.?\s!])' + word + '(?=[\\s.?!])[^\\n.?!]*[.?!\\n)]',
                const sentence_regex = new RegExp(
                                    '[^\\n.?!]*(?<![\\p{L}])' + word + '(?![\\p{L}])[^\\n.?!]*[.?!\\n)]',
                                    'gmiu'
                                );
                const word_regex = new RegExp('(?<![\\p{L}|\\d])' + word + '(?![\\p{L}|\\d])', 'gmiu');

                data.forEach(text => {                   
                    // extract example sentences from text
                    while ((m = sentence_regex.exec(text.text)) !== null) {
                        // This is necessary to avoid infinite loops with zero-width matches
                        if (m.index === sentence_regex.lastIndex) {
                            sentence_regex.lastIndex++;
                        }
                        
                        // create html for each example sentence
                        m.forEach((match, groupIndex) => {
                            match = match.replace(word_regex, function(match, g1) {
                                return g1 === undefined ? match : "<a class='word font-weight-bold'>" + match.replace(new RegExp('\\s\\s+', 'g'), ' ') + "</a>";
                            });
                            // make sure example sentence is unique, then add to the list
                            examples += examples.search(match) > 0 ? "" : "<p>" + match + "</p>\n";
                        });
                    }
                });

                // if example sentence is empty, go to next card
                if (examples == "") {
                    $("#card-header").html("Skipped <b>" + word + "</b>. No examples found.");
                    cur_word_index++;
                    getExampleSentencesforCard(words[cur_word_index]);
                    return;
                }

                // show card
                $("#card").data('word', word);
                $("#card-loader").addClass('d-none');
                $("#card-counter").text((cur_card_index+1) + "/" + max_cards);
                $("#card-header").html("<h3 class='m-0'>" + word + "</h3>");
                $("#card-text").append(examples);
                $(".btn-remember").prop('disabled', false);
                cur_word_index++;
                cur_card_index++;
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                showMessage("Oops! There was an unexpected error trying to fetch example sentences for this word.", "alert-danger");
            }); // end $.ajax
    }

    /**
     * Checks if there are any cards in deck or if end of practice was reached
     * In that case, show respective message to user
     */
    function lastCardReached() {
        if (max_cards == 0) { 
            $("#card-header").text("Sorry, no cards to practice");
            $("#card-text").html("<i class='fas fa-exclamation-circle text-danger display-3'></i><br><br>It seems you don't have any cards left for learning.");
            $("#card-footer").addClass("d-none");
            $("#card-loader").addClass("d-none");
            return true;
        } else if (cur_card_index > max_cards-1) {
            $("#card-header").text("This is the end, my friend");
            $("#card-text").html("<i class='fas fa-check-circle text-success display-3'></i><br><br>You have reached the end of your practice.");
            $("#card-footer").addClass("d-none");
            $("#card-loader").addClass("d-none");
            return true;
        }

        return false;
    }

    /**
     * Decodes HTML entities, this should be XSS safe
     */
    // function decodeEntities(encodedString) {
    //     var textArea = document.createElement('textarea');
    //     textArea.innerHTML = encodedString;
    //     return textArea.value;
    // }

    /**
     * Open dictionary modal
     * Triggers when user clicks word
     * @param {event object} e
     */
    $("body").on("click", ".word", function(e) {
        $sel_word = $(this);
        var url = dictionary_URI.replace("%s", encodeURI($sel_word.text()));

        // set up buttons
        $("#btnadd").text("Forgot");
        
        // show loading spinner
        $("#iframe-loader").attr('class','lds-ellipsis m-auto');
        $dic_frame.attr('class','d-none');

        $dic_frame.get(0).contentWindow.location.replace(url);
        // the previous line loads iframe content without adding it to browser history,
        // as this one does: $dic_frame.attr('src', url);

        $("#myModal").modal("show");
    }); // end #.word.on.click

    /**
     * Hides loader spinner when dictionary iframe finished loading
     */
     $dic_frame.on("load", function() {
        $("#iframe-loader").attr('class','d-none');
        $dic_frame.removeClass();
    }); // end $dic_frame.on.load()

    /**
     * Triggers when user clicks on answer (Yes/No) buttons
     */
    $(".btn-remember").click(function (e) { 
        e.preventDefault();
        var word = $("#card").data('word');
        var remember = e.currentTarget.id == "btn-remember-yes";

        // disable Yes/No buttons
        $(".btn-remember").prop('disabled', true);

        // update card status
        $.ajax({
            type: "POST",
            url: "ajax/updatecard.php",
            data: { word: word, remember: remember }
            // dataType: "json"
        })
        .done(function(data) {
            // go to next card
            getExampleSentencesforCard(words[cur_word_index]);
        })
        .fail(function(xhr, ajaxOptions, thrownError) {
            showMessage("There was an unexpected error updating this word's status", "alert-danger");
        });
    });

    /**
     * Builds translator link including the paragraph to translate as a parameter
     */
    function buildTranslateParagraphLink($word) {
        var sentence = $word.parent("p").text().trim();

        return translator_URI.replace("%s", encodeURIComponent(sentence));
    } // end buildTranslateParagraphLink

    /**
     * Opens translator in new window. 
     * Triggers when user click in translate button in modal window
     */
     $("#btn-translate").on("click", function() {
        window.open(buildTranslateParagraphLink($sel_word));
    }); // end #btn-translate.on.click()

});
