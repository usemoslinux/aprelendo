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

const langs_with_no_word_separator = ['zh', 'ja', 'ko'];
const vocab_status = { 0 : 'learned', 1 : 'learning', 2 : 'new', 3 : 'forgotten' };

/**
 * Modifies the provided text to make unknown words clickable.
 * This function dynamically adds <A> tags to words and phrases not previously identified, adjusting the pattern
 * based on the language's characteristic use of word separators.
 *
 * @param {string} text - The text to be processed for adding links.
 * @param {string} doclang - The language code of the document (e.g., 'en', 'zh'), used to determine the regex pattern.
 * @param {boolean} hide_elem - Determines if the newly created links should be initially hidden.
 * @returns {string} The modified text with clickable links added to unknown words and whitespaces.
 */
function addLinks(text, doclang, hide_elem) {
    let pattern = '';
    let result = '';
    const hide_elem_str = hide_elem ? "style='display: none;'" : "";

    if (langs_with_no_word_separator.includes(doclang)) {
        pattern = new RegExp(/(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(\p{L})/iug);
    } else {
        pattern = new RegExp(/(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(\p{L}+)/iug);
    }

    result = text.replace(pattern, function (match, g1) {
        return g1 === undefined
            ? match
            : '<a class="word" ' + hide_elem_str + ">" + g1 + '</a>';
    });

    // add a to whitespaces
    pattern = new RegExp(/(?<=<[^>]*>)([^\p{L}<]+)/ug);
    result = result.replace(pattern, function (match, g1) {
        return g1 === undefined ? match : '<a>' + g1 + '</a>';
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
 * @param {boolean} hide_elem - Flag to hide elements (words/phrases) when rendered.
 * @returns {string} The processed text with clickable words and some underlined based on their status and frequency.
 */
function underlineWords(data, doclang, hide_elem) {
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
    text = underlineUserPhrases(text, doclang, hide_elem, user_phrases, user_phrases_str);
    text = underlineUserWords(text, doclang, hide_elem, user_words, user_words_str);

    // 2. underline words in frequency list
    if (data.high_freq) {
        text = underlineFrequentWords(text, doclang, hide_elem, data.high_freq);
    }
    
    // 3. create links for each word/phrase
    return addLinks(text, doclang, hide_elem);
} // underlineWords()

/**
 * Adds underlining to user-specified words within the text, marking them based on their learning status.
 * Applies specific regex patterns based on whether the document's language uses word separators.
 *
 * @param {string} text - The original text to process.
 * @param {string} doclang - The language code of the document.
 * @param {boolean} hide_elem - Flag to optionally hide the elements being modified.
 * @param {array} user_words - Array of user-specified words to underline. These are the words the user is learning.
 * @param {string} user_words_str - Concatenated string of user words to be used in regex.
 * @returns {string} The text with user-specified words underlined.
 */
function underlineUserWords(text, doclang, hide_elem, user_words, user_words_str) {
    let pattern = '';
    const hide_elem_str = hide_elem ? " style='display: none;'" : "";

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

            return "<a class='word reviewing " + word_status + "' " + hide_elem_str + ">" + g1 + "</a>";
        });
    }

    return text;
} // underlineUserWords()

/**
 * Adds underlining to user-specified phrases within the text, marking them based on their learning status.
 * Adapts the regex pattern based on the document's language characteristic regarding word separators.
 * Phrases are different from words because they include more than one word.
 *
 * @param {string} text - The original text to process.
 * @param {string} doclang - The language code of the document.
 * @param {boolean} hide_elem - Flag to optionally hide the elements being modified.
 * @param {array} user_phrases - Array of user-specified phrases to underline. These are the phrases the user is learning.
 * @param {string} user_phrases_str - Concatenated string of user phrases to be used in regex.
 * @returns {string} The text with user-specified phrases underlined.
 */
function underlineUserPhrases(text, doclang, hide_elem, user_phrases, user_phrases_str) {
    let pattern = '';
    const hide_elem_str = hide_elem ? " style='display: none;'" : "";

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

            return "<a class='word reviewing " + phrase_status + "' " + hide_elem_str + ">" + g1 + "</a>";
        });
    }

    return text;
} // end underlineUserPhrases()

/**
 * Adds underlining to high-frequency words within the text, using a list of such words and marking them for
 * language learning emphasis.
 * Adapts the regex pattern to the language's use of word separators.
 *
 * @param {string} text - The original text to process.
 * @param {string} doclang - The language code of the document.
 * @param {boolean} hide_elem - Flag to optionally hide the elements being modified.
 * @param {array} high_freq - Array of high-frequency words to be underlined.
 * @returns {string} The text with high-frequency words underlined.
 */
function underlineFrequentWords(text, doclang, hide_elem, high_freq) {
    let pattern = '';
    const hide_elem_str = hide_elem ? "style='display: none;'" : "";

    high_freq = high_freq.join('|');

    if (langs_with_no_word_separator.includes(doclang)) {
        pattern = new RegExp("(?:s*<a class='word[^>]+>.*?</a>|<[^>]*>)|(" + high_freq + ")", 'iug');
    } else {
        pattern = new RegExp("(?:s*<a class='word[^>]+>.*?</a>|<[^>]*>)|(?<![\\p{L}])(" + high_freq + ")(?![\\p{L}])", 'iug');
    }

    text = text.replace(pattern, function (match, p1, offset, string) {
        return p1 === undefined
            ? match
            : "<a class='word frequency-list' " + hide_elem_str + ">" + p1 + "</a>";
    });

    return text;
} // end underlineFrequentWords()

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
 * Calculates the number of unique elements of a specific class in the document, providing a count of unique occurrences.
 *
 * @param {string} class_name - The class name to target within the document.
 * @returns {number} The count of unique textual elements of the specified class.
 */
function getUniqueElements(class_name) {
    let unique_elements = new Set();

    $(class_name).each(function () {
        let text = $(this).text().toLowerCase().trim();
        unique_elements.add(text);
    });

    return unique_elements.size;
} // end getUniqueElements()