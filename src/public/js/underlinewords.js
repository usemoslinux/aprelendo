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

const TextUnderliner = (() => {
    const langs_with_no_word_separator = ['zh', 'ja', 'ko'];
    const vocab_status = { 0: 'learned', 1: 'learning', 2: 'new', 3: 'forgotten' };

    /**
     * Escapes special characters in a string to be used in a regular expression.
     *
     * @param {string} string - The string to be escaped.
     * @returns {string} The escaped string, suitable for use in a regular expression.
     */
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
    } // end escapeRegExp()

    /**
     * Adds underlining to user-specified phrases within the text, marking them based on their learning status.
     * Adapts the regex pattern based on the document's language characteristic regarding word separators.
     * Phrases are different from words because they include more than one word.
     *
     * @param {string} text - The original text to process.
     * @param {string} doclang - The language code of the document.
     * @param {array} user_phrases - Array of user-specified phrases to underline. These are the phrases the user is learning.
     * @param {string} user_phrases_str - Concatenated string of user phrases to be used in regex.
     * @returns {string} The text with user-specified phrases underlined.
     */
    function underlineUserPhrases(text, doclang, user_phrases, user_phrases_str) {
        let pattern = '';

        if (user_phrases.length > 0) {
            if (langs_with_no_word_separator.includes(doclang)) {
                pattern = new RegExp("(?:<[^>]*>)|(" + user_phrases_str + ")", 'iug');
            } else {
                pattern = new RegExp("(?:<[^>]*>)|(?<![\\p{L}])(" + user_phrases_str + ")(?![\\p{L}])", 'iug');
            }

            text = text.replace(pattern, function (match, g1) {
                if (g1 === undefined) {
                    return match
                }

                const user_phrase_match = user_phrases.find(element => element.word.toLowerCase() === match.toLowerCase());
                const phrase_status = vocab_status[user_phrase_match.status];

                return `<a class="word reviewing ${phrase_status}">${g1}</a>`;
            });
        }

        return text;
    } // end underlineUserPhrases()

    /**
     * Adds underlining to user-specified words within the text, marking them based on their learning status.
     * Applies specific regex patterns based on whether the document's language uses word separators.
     *
     * @param {string} text - The original text to process.
     * @param {string} doclang - The language code of the document.
     * @param {array} user_words - Array of user-specified words to underline. These are the words the user is learning.
     * @param {string} user_words_str - Concatenated string of user words to be used in regex.
     * @returns {string} The text with user-specified words underlined.
     */
    function underlineUserWords(text, doclang, user_words, user_words_str) {
        let pattern = '';

        if (user_words.length > 0) {
            if (langs_with_no_word_separator.includes(doclang)) {
                pattern = new RegExp("(?:s*<a class='word[^>]+>.*?</a>|<[^>]*>)|(" + user_words_str + ")", 'iug');
            } else {
                pattern = new RegExp("(?:s*<a class='word[^>]+>.*?</a>|<[^>]*>)|(?<![\\p{L}])(" + user_words_str + ")(?![\\p{L}])", 'iug');
            }

            text = text.replace(pattern, function (match, g1) {
                if (g1 === undefined) {
                    return match
                }

                const user_word_match = user_words.find(element => element.word.toLowerCase() === match.toLowerCase());
                const word_status = vocab_status[user_word_match.status];

                return `<a class="word reviewing ${word_status}">${g1}</a>`;
            });
        }

        return text;
    } // underlineUserWords()

    /**
     * Adds underlining to high-frequency words within the text, using a list of such words and marking them for
     * language learning emphasis.
     * Adapts the regex pattern to the language's use of word separators.
     *
     * @param {string} text - The original text to process.
     * @param {string} doclang - The language code of the document.
     * @param {array} high_freq - Array of high-frequency words to be underlined.
     * @returns {string} The text with high-frequency words underlined.
     */
    function underlineFrequentWords(text, doclang, high_freq) {
        let pattern = '';

        high_freq = high_freq.join('|');

        if (langs_with_no_word_separator.includes(doclang)) {
            pattern = new RegExp("(?:s*<a class='word[^>]+>.*?</a>|<[^>]*>)|(" + high_freq + ")", 'iug');
        } else {
            pattern = new RegExp("(?:s*<a class='word[^>]+>.*?</a>|<[^>]*>)|(?<![\\p{L}])(" + high_freq + ")(?![\\p{L}])", 'iug');
        }

        text = text.replace(pattern, function (match, p1, offset, string) {
            return p1 === undefined
                ? match
                : `<a class="word frequency-list">${p1}</a>`;
        });

        return text;
    } // end underlineFrequentWords()

    /**
     * Modifies the provided text to make unknown words clickable.
     * Dynamically adds <A> tags to words and phrases not previously identified,
     * adjusting the pattern based on the language's characteristic use of word separators.
     *
     * @param {string} text - The text to be processed for adding links.
     * @param {string} doclang - The language code of the document (e.g., 'en', 'zh'), used to determine the regex pattern.
     * @returns {string} The modified text with clickable links added to unknown words.
     */
    function addLinks(text, doclang) {
        const is_lang_with_no_word_delimeter = langs_with_no_word_separator.includes(doclang);

        // Define a regex pattern based on the language type
        const word_pattern = is_lang_with_no_word_delimeter
            ? /(?:\s*<a class="word[^>]+>.*?<\/a>|<[^>]*>)|(\p{L})/iug
            : /(?:\s*<a class="word[^>]+>.*?<\/a>|<[^>]*>)|(\p{L}+)/iug;

        // Process the text to wrap unknown words
        let result = text.replace(word_pattern, (match, word) => {
            return word !== undefined
                ? `<a class="word">${word}</a>`
                : match;
        });

        // Wrap whitespaces and non-word groups in <a> tags
        const non_word_pattern = /(?<=<[^>]*>)([^\p{L}<]+)/ug;
        result = result.replace(non_word_pattern, (match) => {
            return `<a>${match}</a>`;
        });

        return result;
    } // end addLinks()

    /**
     * Processes the given text to underline words based on their status in the user's vocabulary list and their frequency
     * in the language. This function first underlines phrases and words known by the user, then words from a 
     * high-frequency list, and finally links all the other words or phrases to make them clickable.
     *
     * @param {object} data - An object containing text to be underlined, words known by the user, and high-frequency words.
     * @param {string} doclang - The language code indicating specific processing rules.
     * @returns {string} The processed text with clickable words and some underlined based on their status and frequency.
     */
    function apply(data, doclang) {
        let text = data.text;
        let user_phrases = [];
        let user_phrases_str = '';
        let user_words = [];
        let user_words_str = '';

        // Filter elements into the appropriate object and create the strings containing the vocabulary
        // pieces to be used in the RegEx
        data.user_words.forEach(element => {
            if (element.is_phrase > 0) {
                user_phrases.push(element);
                user_phrases_str += escapeRegExp(element.word) + '|';
            } else {
                user_words.push(element);
                user_words_str += element.word + '|';
            }
        });

        // remove trailing |
        user_phrases_str = user_phrases_str.slice(0, -1);
        user_words_str = user_words_str.slice(0, -1);

        // 1. underline phrases & words
        text = underlineUserPhrases(text, doclang, user_phrases, user_phrases_str);
        text = underlineUserWords(text, doclang, user_words, user_words_str);

        // 2. underline words in frequency list
        if (data.high_freq) {
            text = underlineFrequentWords(text, doclang, data.high_freq);
        }

        // 3. create links for each word/phrase
        return addLinks(text, doclang);
    } // apply()

    return {
        apply
    };
})();

const TextProcessor = (() => {
    const langs_with_no_word_separator = ['zh', 'ja', 'ko'];
    let $all_anchors;

    function getTextContainer() {
        return $(parent.document).find('#text');
    }

    function updateAnchorsList() {
        $all_anchors = getTextContainer().find('a'); // all <a> inside #text
    }

    function getAnchorIndex(obj) {
        return $all_anchors.index(obj);
    }

    function langHasNoWordSeparators(doclang) {
        return langs_with_no_word_separator.includes(doclang);
    }

    return {
        getTextContainer,
        updateAnchorsList,
        getAnchorsList: () => $all_anchors,
        getAnchorIndex,
        langHasNoWordSeparators
    };
})();

const TextHighlighter = (() => {
    function removeAll() {
        TextProcessor.getAnchorsList().filter('.highlighted').removeClass('highlighted');
    }

    function addSelection(start_index, current_index) {
        const min = Math.min(start_index, current_index);
        const max = Math.max(start_index, current_index);
        const start_obj_parent = TextProcessor.getAnchorsList().eq(start_index).parent()[0];
        const current_obj_parent = TextProcessor.getAnchorsList().eq(current_index).parent()[0];

        if (start_obj_parent === current_obj_parent) {
            for (let i = min; i <= max; i++) {
                TextProcessor.getAnchorsList().eq(i).addClass('highlighted');
            }
        }
    }

    function getSelection(start_index, current_index) {
        const min = Math.min(start_index, current_index);
        const max = Math.max(start_index, current_index);
        let sel_array = [];
        for (let i = min; i <= max; i++) {
            sel_array.push(TextProcessor.getAnchorsList().eq(i)[0]);
        }

        return $(sel_array);
    }

    return {
        removeAll,
        addSelection,
        getSelection
    };
})();