
/**
 * Gets sentence where selected word is included
 * Used for texts, ebooks (not YT videos & offline videos)
 * @param {jQuery} $selword - The element selected by user
 * @returns {string} The complete example sentence
 */
function getTextSentence($selword) {
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
    
    return sentence.trim();
} // end getTextSentence

/**
 * Builds translator link including the paragraph to translate as a parameter
 * Used for texts, ebooks (not YT videos & offline videos)
 * @param {string} translator_URI - The initial translator URI 
 * @param {jQuery} $selword - The element selected by user
 * @returns {string} The complete translator link
 */
function buildTextTranslationLink(translator_URI, $selword) {
    let sentence = getTextSentence($selword);

    return translator_URI.replace("%s", encodeURI(sentence));
} // end buildTextTranslationLink

/**
 * Gets sentence where selected word is included
 * Used only for YT videos and offline videos
 * @param {jQuery} $selword - The element selected by user
 * @returns {string} The complete translator link
 */
function getVideoSentence($selword) {
    let $start_obj;
    let $end_obj = $selword;
    let $sentence_obj = $();
    let sentence = '';
    let final_iteration = false;
    const sentence_dividers = ":contains('.'), :contains('。'), :contains('!'), :contains('?')";

    // select first part of sentence: from sentence divider (.!?) to selection
    while (!final_iteration) {
        if ($end_obj.index() === 0) {
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

        sentence = $sentence_obj.text().replace(/(\r\n|\n|\r)/gm, " ").trim() + "\n" + sentence;

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
            separator = "\n";
        }

        sentence = sentence.trim() + separator + $sentence_obj.text().replace(/(\r\n|\n|\r)/gm, " ").trim();

        $start_obj = $start_obj.parent().next().children().first();
        final_iteration = final_iteration || $start_obj.length === 0;
    }

    return sentence.trim();
} // end getVideoSentence

/**
 * Builds a translator link including the paragraph to translate as a parameter.
 * Used only for YT videos and offline videos
 * @param {string} translator_URI - The initial translator URI 
 * @param {jQuery} $selword - The element selected by user
 * @returns {string} The complete translator link
 */
function buildVideoTranslationLink(translator_URI, $selword) {
    let sentence = getVideoSentence($selword);

    return translator_URI.replace("%s", encodeURIComponent(sentence));
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
 * Sets Add, Forgot & Remove buttons depending on whether selection already is underlined or not
 * @param {jQuery} $selword 
 */
function setWordActionButtons($selword) {
    let $word_action_group_1 = $("#word-actions-g1") || $(parent.document).find("#word-actions-g1");
    let $word_action_group_2 = $("#word-actions-g2") || $(parent.document).find("#word-actions-g2");

    const underlined_words_in_selection = $selword.filter(
        ".learning, .new, .forgotten, .learned"
    ).length;
    const words_in_selection = $selword.filter(".word").length;

    if (words_in_selection == underlined_words_in_selection) {
        $word_action_group_1.hide();
        $word_action_group_2.show();
    } else {
        $word_action_group_1.show();
        $word_action_group_2.hide();
    }
} // end setWordActionButtons

/**
 * Shows message for high & medium frequency words in dictionary modal window
 * @param {string} word
 * @param {string} lg_iso
 */
function getWordFrequency(word, lg_iso) {
    let $freqlvl = $("#bdgfreqlvl") || $(parent.document).find("#bdgfreqlvl");

    return new Promise((resolve, reject) => {
        $.ajax({
            type: "GET",
            url: "/ajax/getwordfreq.php",
            data: { word: word, lg_iso: lg_iso }
        }).done(function (data) {
            if (data == 0) {
                $freqlvl.removeClass().hide();
            } else if (data < 81) {
                $freqlvl
                    .removeClass()
                    .addClass("badge text-bg-danger")
                    .text("High frequency word")
                    .show();
            } else if (data < 97) {
                $freqlvl
                    .removeClass()
                    .addClass("badge text-bg-warning")
                    .text("Medium frequency word")
                    .show();
            }
            resolve(data);
        }).fail(function () {
            $freqlvl.removeClass().hide();
            reject(new Error("AJAX request failed"));
        });
    });
} // end getWordFrequency

/**
 * Displays action buttons next to the selected word element on the screen.
 * It determines the appropriate position for the action buttons
 * based on the available space on the screen. If there is not enough space 
 * to the right of the selected word, the buttons are positioned to the left.
 *
 * @param {jQuery} $selword - The jQuery object representing the selected word element.
 */
function showActionButtons($selword) {
    const $actions = $('#action-buttons');
    const actions_width = $actions.outerWidth();
    const screen_width = $(window).width();
    const offset = $selword.offset();
    const word_width = $selword.outerWidth();
    
    // Check if there is enough space on the right
    if (offset.left + actions_width > screen_width) {
        // If not enough space, align to the right
        $actions.css({
            top: offset.top - $actions.outerHeight() - 2,
            left: offset.left - actions_width + word_width
        });
    } else {
        // Default positioning to the left of the word
        $actions.css({
            top: offset.top - $actions.outerHeight() - 2,
            left: offset.left
        });
    }

    $actions.show();
}

/**
 * Hides the action buttons element from the screen.
 * It is typically called when the user clicks outside the action buttons
 * or when the user interaction with the word element is complete.
 *
 * @param {Event} e - The event object (optional), used to capture event data if needed.
 */
function hideActionButtons(e) {
    $('#action-buttons').hide();
}

/**
 * Sets up click event listeners on dictionary and translator action buttons.
 * When an action button is clicked, it opens a new browser window or tab
 * with the appropriate link (dictionary, image dictionary, or translator) 
 * based on the selected word's text.
 *
 * @param {jQuery} $selword - The jQuery object representing the selected word element.
 * @param {Object} base_uris - An object containing base URIs for dictionary, image dictionary, and translator services.
 *                             The object should have the following structure:
 *                             {
 *                               dictionary: 'base URI for the dictionary service',
 *                               img_dictionary: 'base URI for the image dictionary service',
 *                               translator: 'base URI for the translator service'
 *                             }
 */
function setDicActionButtonsClick($selword, base_uris) {
    const dic_link = buildDictionaryLink(base_uris.dictionary, $selword.text())
    const img_dic_link = buildDictionaryLink(base_uris.img_dictionary, $selword.text());
    const translator_link = buildTextTranslationLink(base_uris.translator, $selword);
    
    $('#btn-open-dict').off('click').on('click', function() {
        window.open(dic_link, '_blank', 'noopener,noreferrer');
    });
    $('#btn-open-img-dict').off('click').on('click', function() {
        window.open(img_dic_link, '_blank', 'noopener,noreferrer');
    });
    $('#btn-open-translator').off('click').on('click', function() {
        window.open(translator_link, '_blank', 'noopener,noreferrer');
    });
}