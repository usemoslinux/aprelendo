
/**
 * Builds translator link including the paragraph to translate as a parameter
 * Used for texts, ebooks (not YT videos & offline videos)
 * @param {string} translator_URI - The initial translator URI 
 * @param {jQuery} $selword - The element selected by user
 * @returns {string} The complete translator link
 */
function buildTextTranslationLink(translator_URI, $selword) {
    let $start_obj = $selword.prevUntil(":contains('.'), :contains('!'), :contains('?'), :contains('\n')").last();
    $start_obj = $start_obj.length > 0 ? $start_obj : $selword;

    let $end_obj = $selword.prev().length == 0
        ? $selword
            .nextUntil(":contains('.'), :contains('。'), :contains('!'), :contains('?'), :contains('\n')")
            .last()
            .next()
        : $selword
            .prev()
            .nextUntil(":contains('.'), :contains('。'), :contains('!'), :contains('?'), :contains('\n')")
            .last()
            .next();
    $end_obj =
        $end_obj.length > 0 ? $end_obj : $selword.nextAll().last().next();

    let end_obj_length = $end_obj.text().length;

    let $sentence_obj = $start_obj
        .nextUntil($end_obj)
        .addBack()
        .next()
        .addBack();
    let sentence = $sentence_obj.text().replace(/(\r\n|\n|\r)/gm, " ");
    if (end_obj_length > 1) {
        sentence.slice(0, -end_obj_length + 1);
    }
    sentence.trim();

    return translator_URI.replace("%s", encodeURI(sentence));
} // end buildTextTranslationLink

/**
 * Builds a translator link including the paragraph to translate as a parameter.
 * Used only for YT videos and offline videos
 * @param {string} translator_URI - The initial translator URI 
 * @param {jQuery} $selword - The element selected by user
 * @returns {string} The complete translator link
 */
function buildVideoTranslationLink(translator_URI, $selword) {
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
} // end buildVideoTranslationLink

/**
 * Builds translator link using the word object as a parameter
 * Used for Study sessions only
 * @param {string} translator_URI 
 * @param {string} $selword 
 * @returns string
 */
function buildStudyTranslationLink(translator_URI, $selword) {
    const sentence = $selword.parent("p").text().trim();
    return translator_URI.replace("%s", encodeURIComponent(sentence));
} // end buildStudyTranslationLink

/**
 * Build Dictionary & Image Dictionary links, provided a base URI is given
 * @param {string} sel_word 
 * @param {string} dictionary_URI 
 * @returns string
 */
function buildDictionaryLink(dictionary_URI, sel_word) {
    const word = sel_word.trim().replace(/\r?\n|\r/gm, " ");
    return dictionary_URI.replace("%s", encodeURIComponent(word));
} // end buildDictionaryLink

/**
 * Sets Add & Delete buttons depending on whether selection exists in database
 * @param {jQuery} $selword 
 */
function setAddDeleteButtons($selword) {
    let $btn_remove = $("#btn-remove") || $(parent.document).find("#btn-remove");
    let $btn_add = $("#btn-add") || $(parent.document).find("#btn-add");

    const underlined_words_in_selection = $selword.filter(
        ".learning, .new, .forgotten, .learned"
    ).length;
    const words_in_selection = $selword.filter(".word").length;

    if (words_in_selection == underlined_words_in_selection) {
        if ($btn_remove.is(":visible") === false) {
            $btn_remove.show();
            $btn_add.text("Forgot").removeClass('btn-primary').addClass('btn-danger');
        }
    } else {
        $btn_remove.hide();
        $btn_add.text("Add").removeClass('btn-danger').addClass('btn-primary');
    }
} // end setAddDeleteButtons

/**
 * Shows message for high & medium frequency words in dictionary modal window
 * @param {string} word
 * @param {string} lg_iso
 */
function getWordFrequency(word, lg_iso) {
    let $freqlvl = $("#bdgfreqlvl") || $(parent.document).find("#bdgfreqlvl");

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