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

const ActionBtns = (() => {
    /**
     * Displays action buttons next to the selected word element on the screen.
     * It determines the appropriate position for the action buttons
     * based on the available space on the screen. If there is not enough space 
     * to the right of the selected word, the buttons are positioned to the left.
     *
     * @param {jQuery} $selword - The jQuery object representing the selected word element.
     */
    const show = ($selword) => {
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

        $actions.removeClass('invisible');
    }

    /**
     * Hides the action buttons element from the screen.
     * It is typically called when the user clicks outside the action buttons
     * or when the user interaction with the word element is complete.
     *
     * @param {Event} e - The event object (optional), used to capture event data if needed.
     */
    const hide = (e) => {
        $('#action-buttons').addClass('invisible');
    }

    /**
     * Sets Add, Forgot & Remove buttons depending on whether selection already is underlined or not
     * @param {jQuery} $selword 
     */
    const createWordActionBtns = ($selword, is_study) => {
        let $word_action_group_1 = $("#word-actions-g1") || $(parent.document).find("#word-actions-g1");
        let $word_action_group_2 = $("#word-actions-g2") || $(parent.document).find("#word-actions-g2");

        if (is_study) {
            $word_action_group_1.addClass('d-none');
            $word_action_group_2.addClass('d-none');
            return;
        }

        const underlined_words_in_selection = $selword.filter(
            ".learning, .new, .forgotten, .learned"
        ).length;
        const words_in_selection = $selword.filter(".word").length;

        if (words_in_selection == underlined_words_in_selection) {
            $word_action_group_1.addClass('d-none');
            $word_action_group_2.removeClass('d-none');
        } else {
            $word_action_group_1.removeClass('d-none');
            $word_action_group_2.addClass('d-none');
        }
    } // end createWordActionBtns

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
     * @param {string} source - Page where dictionary pop-up will be shown. Different translation links are build based
     */
    const bindDictionaryBtnsOnClick = ($selword, source) => {
        const base_uris = Dictionaries.getURIs();
        const dic_link = LinkBuilder.forWordInDictionary(base_uris.dictionary, $selword.text());
        const img_dic_link = LinkBuilder.forWordInDictionary(base_uris.img_dictionary, $selword.text());

        let translator_link = '';
        switch (source) {
            case 'text':
                translator_link = LinkBuilder.forTranslationInText(base_uris.translator, $selword);
                break;
            case 'video':
                translator_link = LinkBuilder.forTranslationInVideo(base_uris.translator, $selword);
                break;
            case 'study':
                translator_link = LinkBuilder.forTranslationInStudy(base_uris.translator, $selword);
                break;
            default:
                break;
        }

        $('#btn-open-dict').off('click').on('click', function () {
            openInNewTab(dic_link);
        });
        $('#btn-open-img-dict').off('click').on('click', function () {
            openInNewTab(img_dic_link);
        });
        $('#btn-open-translator').off('click').on('click', function () {
            openInNewTab(translator_link);
        });
        $('#btn-open-ai-bot-modal').off('click').on('click', function () {
            const $ai_bot_modal = $('#ask-ai-bot-modal');
            $ai_bot_modal.attr('data-word', $selword.text());
            $ai_bot_modal.modal('show');
        });
    }

    return {
        show,
        hide,
        createWordActionBtns,
        bindDictionaryBtnsOnClick
    };
})();

const TextActionBtns = (() => {
    /**
     * Shows pop up toolbar when user clicks a word
     */
    const show = ($selword) => {
        ActionBtns.createWordActionBtns($selword, false);
        $("#text-container").disableScroll();
        ActionBtns.bindDictionaryBtnsOnClick($selword, 'text');
        ActionBtns.show($selword);
    } // end show()

    /**
     * Hides actions pop up toolbar
     */
    const hide = () => {
        $("#text-container").enableScroll();
        ActionBtns.hide();
    } // end hide()


    return {
        show,
        hide
    };
})();

const VideoActionBtns = (() => {
    /**
     * Shows pop up toolbar when user clicks a word
     */
    const show = ($selword) => {
        $("#text-container").disableScroll();
        ActionBtns.createWordActionBtns($selword, false);
        ActionBtns.bindDictionaryBtnsOnClick($selword, 'video');
        ActionBtns.show($selword);
    } // end show

    /**
     * Hides actions pop up toolbar
     */
    const hide = () => {
        $("#text-container").enableScroll();
        ActionBtns.hide();
    } // end hide


    return {
        show,
        hide
    };
})();

const StudyActionBtns = (() => {
    /**
     * Shows pop up toolbar when user clicks a word
     */
    const show = ($selword) => {
        ActionBtns.createWordActionBtns($selword, true);
        ActionBtns.bindDictionaryBtnsOnClick($selword, 'study');
        ActionBtns.show($selword);
    } // end show

    /**
     * Hides actions pop up toolbar
     */
    const hide = () => {
        ActionBtns.hide();
    } // end hide

    return {
        show,
        hide
    };
})();