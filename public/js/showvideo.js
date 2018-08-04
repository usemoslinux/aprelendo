$(document).ready(function () {
    $selword = null;
    dictionaryURI = "";
    translatorURI = "";
    prevsel = 0; // previous selection index in #selPhrase
    resume_video = true;
    video_paused = false;
    
    // ajax call to get dictionary & translator URIs
    $.ajax({
        url: "db/getdicuris.php",
        type: "GET",
        dataType: "json"
    }).done(function (data) {
        dictionaryURI = data.LgDict1URI;
        translatorURI = data.LgTranslatorURI;
    });
    
    /**
    * Sets Add & Delete buttons depending on whether selection exists in database
    */
    function setAddDeleteButtons() {
        var $btnremove = $("#btnremove");
        var $btnadd = $("#btnadd");
        var $btncancel = $("#btncancel");
        if ($selword.is(".learning, .new, .forgotten, .learned")) {
            if ($btnremove.is(":visible") === false) {
                $btncancel.hide();
                $btnremove.show();
                $btnadd.text("Forgot meaning");
            }
        } else {
            $btncancel.show();
            $btnremove.hide();
            $btnadd.text("Add");
        }
    }
    
    /**
    * Shows dictionary when user clicks a word
    * All words are enclosed in span.word tags
    */
    $(document).on("click", "span.word", function () {
        // if there is video playing
        if (!video_paused) {
            player.pauseVideo();
            resume_video = true;
        } else {
            resume_video = false;
        }
        
        $selword = $(this);
        
        setAddDeleteButtons();
        
        // show dictionary
        var url = dictionaryURI.replace("%s", encodeURIComponent($selword.text()));
        
        $("#dicFrame")
        .get(0)
        .contentWindow.location.replace(url);
        // the previous line loads iframe content without adding it to browser history,
        // as this one does: $('#dicFrame').attr('src', url);
        
        // build phrase select element in modal window
        $("#selPhrase").empty();
        $("#selPhrase").append(
            $("<option>", {
                value: $selword.text(),
                text: $selword.text()
            })
        );
        phraselength = 0;
        
        // chose max. 5 words. If .?! detected, then stop (it's the end of the sentence).
        $selword
        .nextAll("span")
        .slice(0, 20)
        .each(function (i, item) {
            if (
                phraselength == 5 ||
                $(item)
                .text()
                .search(/[.?!]/i) > -1
            ) {
                return false;
            } else {
                if ($(item).hasClass("word")) {
                    $("#selPhrase").append(
                        $("<option>", {
                            value: $selword.text() +
                            $selword
                            .nextAll("span")
                            .slice(0, i + 1)
                            .text(),
                            text: $selword.text() + "..." + $(item).text()
                        })
                    );
                    phraselength++;
                }
            }
        });
        $("#selPhrase").append(
            $("<option>", {
                value: "translate_sentence",
                text: "Translate sentence"
            })
        );
        
        prevsel = 0;
    });
    
    /**
    * Adds selected word or phrase to the database and underlines it in the text
    */
    $("#btnadd").on("click", function () {
        // check if selection is a word or phrase
        var selection = $("#selPhrase option:selected").val();
        var selphrase_sel_index = $("#selPhrase").prop("selectedIndex");
        var selphrase_count = $("#selPhrase option").length;
        var is_phrase =
        selphrase_sel_index > 0 && selphrase_sel_index != selphrase_count - 1;
        
        // add selection to "words" table
        $.ajax({
            type: "POST",
            url: "db/addword.php",
            data: {
                word: selection,
                isphrase: is_phrase
            }
        })
        .done(function () {
            // if successful, underline word or phrase
            if (is_phrase) { // if it's a phrase
            var firstword = $selword.text();
            var phraseext = selphrase_sel_index + 1;
            var filterphrase = $("span.word").filter(function () {
                return (
                    $(this)
                    .text()
                    .toLowerCase() === firstword.toLowerCase()
                );
            });
            
            filterphrase.each(function () {
                var lastword = $(this)
                .nextAll("span.word")
                .slice(0, phraseext - 1)
                .last();
                var phrase = $(this)
                .nextUntil(lastword)
                .addBack()
                .next("span.word")
                .addBack();
                
                if (phrase.text().toLowerCase() === selection.toLowerCase()) {
                    if ($(this).is('.new, .learning, .learned, .forgotten')) {
                        phrase.wrapAll(
                            "<span class='word reviewing forgotten' data-toggle='modal' data-target='#myModal'></span>"
                        );  
                    } else {
                        phrase.wrapAll(
                            "<span class='word reviewing new' data-toggle='modal' data-target='#myModal'></span>"
                        );
                    }
                    
                    phrase.contents().unwrap();
                }
            });
        } else { // if it's a word
        var filterword = $("span.word").filter(function () {
            return (
                $(this)
                .text()
                .toLowerCase() === selection.toLowerCase()
            );
        });
        
        filterword.each(function() {
            var $word = $(this);
            if ($word.is('.new, .learning, .learned, .forgotten')) {
                $word.html("<span class='word reviewing forgotten' data-toggle='modal' data-target='#myModal'>" +
                selection +
                "</span>");
            } else {
                $word.html("<span class='word reviewing new' data-toggle='modal' data-target='#myModal'>" +
                selection +
                "</span>");
            }
        });
        
        filterword.contents().unwrap();
    }
    
})
.fail(function (XMLHttpRequest, textStatus, errorThrown) {
    alert(
        "Oops! There was an error adding this word or phrase to the database."
    );
});
});

/**
* Remove selected word or phrase from database
*/
$("#btnremove").on("click", function () {
    $.ajax({
        type: "POST",
        url: "db/removeword.php",
        data: {
            word: $selword.text()
        }
    })
    .done(function () {
        var filter = $("span.word").filter(function () {
            return (
                $(this)
                .text()
                .toLowerCase() === $selword.text().toLowerCase()
            );
        });
        
        $.ajax({
            url: "db/underlinewords.php",
            type: "POST",
            data: {
                txt: $selword.text()
            }
        }).done(function (result) {
            filter.html(result);
            filter.contents().unwrap();
        });
    })
    .fail(function (XMLHttpRequest, textStatus, errorThrown) {
        alert("Oops! There was an error removing the word from the database.");
    });
});

/**
* Finished studying this text. Archives text & saves new status of words/phrases 
* Executes when the user presses the big green button at the end
*/
$('#btn_save').on('click', archiveTextAndSaveWords);

/**
* Archives text and updates status of all underlined words & phrases
*/
function archiveTextAndSaveWords() {
    // build array with underlined words
    var oldwords = [];
    var ids = [];
    var word = "";
    $(".learning").each(function () {
        word = $(this)
        .text()
        .toLowerCase();
        if (jQuery.inArray(word, oldwords) == -1) {
            oldwords.push(word);
        }
    });
    
    ids.push($("#container").attr("data-textID")); // get text ID
    
    $.ajax({
        type: "POST",
        url: "db/archivetext.php",
        data: {
            words: oldwords,
            textIDs: JSON.stringify(ids),
            archivetext: true
        }
    }).done(function (data) {
        window.location.replace("texts.php");
    })
    .fail(function (XMLHttpRequest, textStatus, errorThrown) {
        alert("Oops! There was an error updating the database.");
    });
}

/**
* Resumes video when modal window is closed
*/
$("#myModal").on("hidden.bs.modal", function () {
    if (resume_video) {
        player.playVideo();
    }
});

/**
* Updates dictionary in modal window when user selects a new word/phrase
* If user chooses to "translate sentence", the translator pops up
*/
$("#selPhrase").on("change", function () {
    var selindex = $("#selPhrase").prop("selectedIndex");
    var trans_whole_p_index = $("#selPhrase option").length - 1;
    var url = "";
    
    // set Add & Delete buttons depending on whether selection exists in database
    if (selindex == 0 || selindex == trans_whole_p_index) {
        // only for the first word we need to check if it exists in db
        setAddDeleteButtons();
    } else {
        // for the rest, due to the selection method used in Langx, we can be sure
        // they are not in the database
        $("#btnremove").hide();
        $("#btnadd").text("Add");
    }
    
    // define behaviour when user clicks on a phrase or "translate sentence"
    if (selindex == trans_whole_p_index) {
        // translate sentence
        var $start_obj = $selword.prevUntil(':contains(".")').last();
        var $end_obj = $selword.nextUntil(':contains(".")').last().next();
        var $sentence = $start_obj.nextUntil($end_obj).addBack().next().addBack();

        url = translatorURI.replace(
            "%s",
            encodeURIComponent($sentence.text())
        );
        var win = window.open(url);
        if (win) {
            win.focus();
        } else {
            alert(
                "Couldn't open translator window. Please allow popups for this website"
            );
        }
        $(this).prop("selectedIndex", prevsel);
    } else {
        // else, select phrase & look it up in dictionary
        phrase = $("#selPhrase option")
        .eq(selindex)
        .val();
        url = dictionaryURI.replace("%s", encodeURIComponent(phrase));
        $("#dicFrame")
        .get(0)
        .contentWindow.location.replace(url);
        prevsel = selindex;
    }
});

});