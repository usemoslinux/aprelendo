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
            const user_phrase_status_map = new Map();
            user_phrases.forEach(function (element) {
                const word_key = element.word.toLowerCase();

                if (!user_phrase_status_map.has(word_key)) {
                    user_phrase_status_map.set(word_key, element.status);
                }
            });

            if (langs_with_no_word_separator.includes(doclang)) {
                pattern = new RegExp("(?:<[^>]*>)|(" + user_phrases_str + ")", 'iug');
            } else {
                pattern = new RegExp("(?:<[^>]*>)|(?<![\\p{L}])(" + user_phrases_str + ")(?![\\p{L}])", 'iug');
            }

            text = text.replace(pattern, function (match, g1) {
                if (g1 === undefined) {
                    return match
                }

                const phrase_status_key = user_phrase_status_map.get(g1.toLowerCase());
                const phrase_status = vocab_status[phrase_status_key];

                if (phrase_status === undefined) {
                    return match;
                }

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
            const user_word_status_map = new Map();
            user_words.forEach(function (element) {
                const word_key = element.word.toLowerCase();

                if (!user_word_status_map.has(word_key)) {
                    user_word_status_map.set(word_key, element.status);
                }
            });

            if (langs_with_no_word_separator.includes(doclang)) {
                pattern = new RegExp("(?:<a\\b[^>]*>[^<]*<\\/a>|<[^>]*>)|(" + user_words_str + ")", 'iug');
            } else {
                pattern = new RegExp("(?:<a\\b[^>]*>[^<]*<\\/a>|<[^>]*>)|(?<![\\p{L}])(" + user_words_str + ")(?![\\p{L}])", 'iug');
            }

            text = text.replace(pattern, function (match, g1) {
                if (g1 === undefined) {
                    return match
                }

                const word_status_key = user_word_status_map.get(g1.toLowerCase());
                const word_status = vocab_status[word_status_key];

                if (word_status === undefined) {
                    return match;
                }

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

        if (high_freq.length === 0) {
            return text;
        }

        const has_no_word_separator = langs_with_no_word_separator.includes(doclang);
        const high_freq_set = new Set(
            high_freq.map(function (word) {
                return word.toLowerCase();
            })
        );
        const escaped_high_freq_str = high_freq.map(function (word) {
            return escapeRegExp(word);
        }).join('|');
        const word_anchor_pattern = /<a\b([^>]*)>([^<]*)<\/a>/iug;
        const class_pattern = /\bclass\s*=\s*(['"])([^'"]*)\1/iu;

        // Existing word links should be updated in place and reviewing links must be kept unchanged.
        text = text.replace(word_anchor_pattern, function (match, attrs, anchor_text) {
            const class_match = attrs.match(class_pattern);

            if (!class_match) {
                return match;
            }

            const class_value = class_match[2];
            const class_tokens = class_value.trim().split(/\s+/u);
            const has_reviewing_class = class_tokens.includes('reviewing');
            const has_word_class = class_tokens.includes('word');
            const is_high_freq = high_freq_set.has(anchor_text.toLowerCase());

            if (!has_word_class || has_reviewing_class || !is_high_freq) {
                return match;
            }

            if (class_tokens.includes('frequency-list')) {
                return match;
            }

            const updated_class_value = `${class_value} frequency-list`;
            const updated_attrs = attrs.replace(class_pattern, `class=${class_match[1]}${updated_class_value}${class_match[1]}`);

            return `<a${updated_attrs}>${anchor_text}</a>`;
        });

        if (has_no_word_separator) {
            pattern = new RegExp("(?:<a\\b[^>]*>[^<]*<\\/a>|<[^>]*>)|(" + escaped_high_freq_str + ")", 'iug');
        } else {
            pattern = /(?:<a\b[^>]*>[^<]*<\/a>|<[^>]*>)|(\p{L}+)/iug;
        }

        text = text.replace(pattern, function (match, p1) {
            if (p1 === undefined) {
                return match;
            }

            return high_freq_set.has(p1.toLowerCase())
                ? `<a class="word frequency-list">${p1}</a>`
                : match;
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
                user_words_str += escapeRegExp(element.word) + '|';
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
    let all_anchors = $();

    /**
     * Gets the text container that stores underlined anchors.
     *
     * @returns {jQuery} jQuery object for `#text` in the parent document.
     */
    function getTextContainer() {
        return $(parent.document).find('#text');
    }

    /**
     * Refreshes and caches all anchor tags inside the text container.
     *
     * @returns {jQuery} jQuery object with all cached anchors.
     */
    function updateAnchorsList() {
        all_anchors = getTextContainer().find('a'); // all <a> inside #text
        return all_anchors;
    }

    /**
     * Returns the cached anchor list and initializes it if needed.
     *
     * @returns {jQuery} jQuery object with all anchors.
     */
    function getAnchorsList() {
        if (!all_anchors || typeof all_anchors.filter !== 'function') {
            return updateAnchorsList();
        }

        return all_anchors;
    }

    /**
     * Gets the index of an anchor inside the cached anchors list.
     *
     * @param {jQuery|HTMLElement} obj - Anchor object to find.
     * @returns {number} The index in the anchors list, or -1 if not found.
     */
    function getAnchorIndex(obj) {
        return getAnchorsList().index(obj);
    }

    /**
     * Checks whether the provided language uses word separators.
     *
     * @param {string} doclang - Language code.
     * @returns {boolean} True when the language has no explicit word separators.
     */
    function langHasNoWordSeparators(doclang) {
        return langs_with_no_word_separator.includes(doclang);
    }

    return {
        getTextContainer,
        updateAnchorsList,
        getAnchorsList,
        getAnchorIndex,
        langHasNoWordSeparators
    };
})();

const TextHighlighter = (() => {
    /**
     * Removes highlighting from all currently highlighted anchors.
     *
     * @returns {void}
     */
    function removeAll() {
        const anchors_list = TextProcessor.getAnchorsList();

        if (!anchors_list || anchors_list.length === 0) {
            return;
        }

        anchors_list.filter('.highlighted').removeClass('highlighted');
    }

    /**
     * Applies highlighting to every anchor between two indexes (inclusive).
     *
     * @param {number} start_index - Selection start index.
     * @param {number} current_index - Selection end index.
     * @returns {void}
     */
    function addSelection(start_index, current_index) {
        const anchors_list = TextProcessor.getAnchorsList();

        if (!anchors_list || anchors_list.length === 0) {
            return;
        }

        const min = Math.min(start_index, current_index);
        const max = Math.max(start_index, current_index);
        const start_anchor = anchors_list.eq(start_index);
        const current_anchor = anchors_list.eq(current_index);
        const start_obj_parent = start_anchor.parent()[0];
        const current_obj_parent = current_anchor.parent()[0];

        if (!start_obj_parent || !current_obj_parent) {
            return;
        }

        if (start_obj_parent === current_obj_parent) {
            for (let i = min; i <= max; i++) {
                anchors_list.eq(i).addClass('highlighted');
            }
        }
    }

    /**
     * Gets a jQuery object containing anchors between two indexes.
     *
     * @param {number} start_index - Selection start index.
     * @param {number} current_index - Selection end index.
     * @returns {jQuery} Selected anchors as a jQuery collection.
     */
    function getSelection(start_index, current_index) {
        const anchors_list = TextProcessor.getAnchorsList();
        const selected_nodes = [];

        if (!anchors_list || anchors_list.length === 0) {
            return $(selected_nodes);
        }

        const min = Math.min(start_index, current_index);
        const max = Math.max(start_index, current_index);

        for (let i = min; i <= max; i++) {
            const current_node = anchors_list.eq(i)[0];

            if (current_node) {
                selected_nodes.push(current_node);
            }
        }

        return $(selected_nodes);
    }

    return {
        removeAll,
        addSelection,
        getSelection
    };
})();
