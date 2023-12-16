/**
 * Builds a translator link including the paragraph to translate as a parameter.
 * @param {string} translator_URI - The initial translator URI 
 * @param {jQuery} $selword - The element selected by user
 * @returns {string} The complete translator link
 */
function buildTranslationLink(translator_URI, $selword) {
    let $start_obj;
    let $end_obj = $selword;
    let $sentence_obj = $();
    let sentence = '';
    let final_iteration = false;
    const sentence_dividers = ":contains('.'), :contains('。'), :contains('!'), :contains('?')";

    // select first part of sentence: from sentence divider (.!?) to selection
    while (!final_iteration) {
        if ($end_obj.index() === 0) {
            // sentence = $end_obj.text().replace(/(\r\n|\n|\r)/gm, " ").trim() + " ";
            // $end_obj = $end_obj.parent().prev().children().last();
            // continue;
            sentence = $end_obj.text().replace(/(\r\n|\n|\r)/gm, " ").trim();
            break;
        }

        if ($end_obj.filter(sentence_dividers).length > 0) {
            break;
        } 

        $start_obj = $end_obj
            .prevAll(sentence_dividers)
            .next()
            .last();

        final_iteration = $start_obj.length > 0 || $end_obj.filter(sentence_dividers).length > 0;
        $start_obj = final_iteration ? $start_obj : $end_obj.siblings().addBack().first();

        $sentence_obj = $start_obj
            .nextUntil($end_obj)
            .addBack()
            .next()
            .addBack();

        sentence = $sentence_obj.text().replace(/(\r\n|\n|\r)/gm, " ").trim() + " " + sentence;

        $end_obj = $end_obj.parent().prev().children().last();
        final_iteration = final_iteration || $end_obj.length === 0;
    }

    // select second part of sentence: from selection to sentence divider
    $start_obj = $selword.next().length === 0 ? $selword : $selword.next();
    let regex = /[.。!?]/g;
    let separator;
    let matches = sentence.match(regex);
    final_iteration = matches;
    while (!final_iteration) {
        $end_obj = $start_obj
            .nextAll(sentence_dividers)
            .first()

        $end_obj = $end_obj.index() === $end_obj.parent().children().length ? $start_obj : $end_obj;
        final_iteration = $end_obj.length > 0 || $start_obj.filter(sentence_dividers).length > 0;
        $end_obj = final_iteration ? $end_obj : $start_obj.siblings().last();

        if ($start_obj.filter(sentence_dividers).length > 0) {
            $sentence_obj = $start_obj;
            separator = "";
        } else {
            $sentence_obj = $start_obj
            .nextUntil($end_obj)
            .addBack()
            .next()
            .addBack();    
            separator = " ";
        }

        sentence = sentence.trim() + separator + $sentence_obj.text().replace(/(\r\n|\n|\r)/gm, " ").trim();

        $start_obj = $start_obj.parent().next().children().first();
    }

    return translator_URI.replace("%s", encodeURIComponent(sentence.trim()));
} // end buildTranslationLink

/**
 * Shows message for high & medium frequency words in dictionary modal window
 * @param {string} word
 * @param {string} lg_iso
 */
function getWordFrequency(word, lg_iso) {
    let $freqlvl = $("#bdgfreqlvl");

    // ajax call to get word frequency
    $.ajax({
        type: "GET",
        url: "/ajax/getwordfreq.php",
        data: { word: word, lg_iso: lg_iso }
    }).done(function (data) {
        if (data == 0) {
            $freqlvl.hide();
        } else if (data < 81) {
            $freqlvl
                .hide()
                .text("High frequency word")
                .removeClass()
                .addClass("badge text-bg-danger")
                .show();
        } else if (data < 97) {
            $freqlvl
                .hide()
                .text("Medium frequency word")
                .removeClass()
                .addClass("badge text-bg-warning")
                .show();
        }
    }).fail(function () {
        $freqlvl.hide();
    });
} // end getWordFrequency

/**
 * Sets Add & Delete buttons depending on whether selection exists in database
 * @param {jQuery} $selword - The element selected by user
*/
function setAddDeleteButtons($selword) {
    let $btnremove = $("#btnremove");
    let $btnadd = $("#btnadd");

    let underlined_words_in_selection = $selword.filter(
        ".learning, .new, .forgotten, .learned"
    ).length;
    let words_in_selection = $selword.filter(".word").length;

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