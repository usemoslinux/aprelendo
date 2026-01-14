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

/**
 * Normalizes punctuation in a string for lenient comparison.
 * Replaces smart quotes, dashes, and ellipses with their simple ASCII equivalents.
 * @param {string} text The string to normalize.
 * @returns {string} The normalized string.
 */
function normalize(text) {
    if (typeof text !== 'string') {
        return '';
    }
    return text
        .replace(/[’‘]/g, "'") // Single quotes
        .replace(/[“”]/g, '"') // Double quotes
        .replace(/…/g, '...') // Ellipsis
        .replace(/[–—]/g, '-'); // Dashes
}

$(document).ready(function () {
    /**
     * Checks if answer is correct and shows a cue to indicate status when user moves
     * focus out of an input box.
     */
    $("body").on("blur", ".dict", function () {
        let $curinput = $(this);
        let user_answer = $.trim($curinput.val().toLowerCase());

        // 1. Handle Empty Input (Reset State)
        if (user_answer === "") {
            $curinput.css("border-color", ""); // Reset to default
            $curinput.next("span").addClass("d-none");
            return;
        }

        // Normalize both for a fair comparison
        let normalized_user_answer = normalize(user_answer);
        let normalized_correct_answer = normalize($curinput.attr("data-text").toLowerCase());

        // 2. Check if Correct
        if (normalized_user_answer === normalized_correct_answer) {
            $curinput.css("border-color", "var(--w-learned)");
            $curinput.next("span").addClass("d-none");
        }
        // 3. Handle Wrong Answer
        else {
            $curinput.css("border-color", "var(--w-forgotten)");
        }
    });

    /**
     * Modify backspace and space normal behavior in dictation mode
     */
    $("body").on("keydown", ".dict", function (e) {
        const keyCode = e.keyCode || e.which;

        // IME on mobile devices may not return correct keyCode
        if (e.isComposing || keyCode == 0 || keyCode == 229) {
            return;
        }

        // if input is empty...
        if (!$(this).val()) {
            if (keyCode == 8) {
                // if backspace is pressed, move focus to previous input
                const index = $(".dict").index(this) - 1;
                e.preventDefault();
                $(".dict")
                    .eq(index)
                    .focus();
            } else if (keyCode == 32) {
                // if space key is pressed, prevent default behavior
                e.preventDefault();
            }
        }
    }); // end .dict.on.keydown

    /**
     * Implements shortcuts for dictation
     */
    $("body").on("input", ".dict", function (e) {
        let keyCode = e.keyCode || e.which;
        const maxLength = $(this).attr("maxlength");
        const curTime = $("#audioplayer")[0].currentTime;

        // make sure keycode is correct (fix for IME on mobile devices)
        if (keyCode == 0 || keyCode == 229) {
            keyCode = e.target.value.charAt(e.target.selectionStart - 1).charCodeAt();
        }

        // if "1", rewind 1 sec; if "2", toggle audio; if "3" fast-forward 1 sec
        switch (keyCode) {
            case 8: // backspace
                if (!$(this).val()) {
                    const index = $(".dict").index(this) - 1;
                    $(".dict")
                        .eq(index)
                        .focus();
                }
                break;
            case 49: // 1
                $("#audioplayer")[0].currentTime = curTime - 5;
                break;
            case 50: // 2
                AudioController.togglePlayPause();
                break;
            case 51: // 3       
                $("#audioplayer")[0].currentTime = curTime + 5;
                break;
            default:
                break;
        }
        $(this).val($(this).val().replace(/\d/gi, '')); // don't allow digits to get printed

        // if maxlength reached, switch focus to next input
        if (maxLength == $(this).val().length && !e.originalEvent.isComposing) {
            const index = $(".dict").index(this) + 1;
            $(".dict")
                .eq(index)
                .focus();
        }
    }); // end .dict.on.input
});

/**
  * Toggles dictation on/off
  */
function toggleDictation() {
    const audio_is_loaded = $("#audioplayer").find("source").attr("src") != "";

    if (audio_is_loaded) {
        let $container = $("#text").clone();
        let $elems = $container.find(".word");
        let $original_elems = $(".word");

        if ($(".dict-answer").length == 0) {
            // toggle dictation on
            // replace all underlined words/phrases with input boxes
            $elems.each(function (index, value) {
                let $elem = $(this);
                const length = $elem.text().length;
                const width = $original_elems.eq(index).width();
                const line_height = $original_elems.eq(index).css("font-size");
                let border_color = '';

                if ($elem.hasClass('learned')) {
                    border_color = 'var(--w-learned)'
                } else if ($elem.hasClass('learning')) {
                    border_color = 'var(--w-learning)'
                } else if ($elem.hasClass('new')) {
                    border_color = 'var(--w-new)'
                } else if ($elem.hasClass('forgotten')) {
                    border_color = 'var(--w-forgotten)'
                }

                $elem
                    .hide()
                    .after(
                        '<div class="input-group dict-input-group"><input type="text" class="dict" ' +
                        'style="width:' + width + "px; line-height:" + line_height + "; border-color:" +
                        border_color + ';" ' + 'maxlength="' + length + '" data-text="' + $elem.text() + '">' +
                        '<span class="dict-answer d-none"></span></div>'
                    );
            });

            $("#text").replaceWith($container);

            scrollToPageTop();
            AudioController.playFromBeginning();

            $(":text:first").focus(); // focus first input
        } else {
            // toggle dictation off
            $elems.each(function () {
                let $elem = $(this);
                let $input = $elem.next(".dict-input-group").find(".dict");

                if ($input.length > 0) {
                    let userAnswer = $input.val();
                    let correctAnswer = $input.attr("data-text");
                    let normalized_user_answer = normalize(userAnswer.toLowerCase().trim());
                    let normalized_correct_answer = normalize(correctAnswer.toLowerCase());
                    
                    if (normalized_user_answer !== '' && normalized_user_answer !== normalized_correct_answer) {
                        $elem.after('<span class="misspelled-word text-danger fw-bold"> [' + userAnswer + ']</span>');
                    }
                }

                $elem.show();
            });

            $container.find(".dict-input-group").remove();

            $("#text").replaceWith($container);
            TextProcessor.updateAnchorsList();

            scrollToPageTop();
            AudioController.stop();
        }
    }
} // end toggleDictation