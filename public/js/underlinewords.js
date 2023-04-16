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

const langs_with_no_word_separator = ['zh', 'ja', 'ko'];

/**
 * Adds as to unknown words and whitespaces
 * This way these unknown words will be clickable and empty spaces will be recognized 
 * when selecting phrases
 * @param {string} text 
 * @returns string
 */
function addLinks(text, doclang) {
    // add a to unknown words, but use different regex for languages with no word separators, such as Chinese
    let pattern = '';
    let result = '';
    
    if (langs_with_no_word_separator.includes(doclang)) {
        pattern = new RegExp(/(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(\p{L})/iug);    
    } else {
        pattern = new RegExp(/(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(\p{L}+)/iug);
    }
    
    result = text.replace(pattern, function(match, g1) {
        return g1 === undefined ? match : '<a class="word" data-toggle="modal" data-bs-target="#myModal">' + g1 + '</a>';
    });

    // add a to whitespaces
    pattern = new RegExp(/(?<=<[^>]*>)([^\p{L}<]+)/ug);
    result = result.replace(pattern, function(match, g1) {
        return g1 === undefined ? match : '<a>' + g1 + '</a>';    
    });

    return result;
} // end addLinks()

/**
 * Underlines text using data information
 * data.text = text to underline; data.user_words = words in user dictionary; 
 * data.high_freq = high frequency words for that language
 * @param {object} data  
 * @returns string 
 */
function underlineWords(data, doclang) {
    let text = data.text;
    let pattern = '';
    let user_phrases_learning = '';
    let user_phrases_learned = '';
    let user_words_learning = '';
    let user_words_learned = '';
    let high_freq = '';

    // 1. underline phrases & words

    Object.values(data.user_words).forEach(element => {
        if (element.is_phrase > 0) {
            if (element.status > 0) {
                user_phrases_learning += escapeRegExp(element.word) + '|';    
            } else {
                user_phrases_learned += escapeRegExp(element.word) + '|';
            }
        } else {
            if (element.status > 0) {
                user_words_learning += element.word + '|';    
            } else {
                user_words_learned += element.word + '|';
            }
        }

        console.log("word: " + element.word);
    });

        if (user_phrases_learning) {
            user_phrases_learning = user_phrases_learning.slice(0, -1); // remove trailing |
            
            if (langs_with_no_word_separator.includes(doclang)) {
                pattern = new RegExp("(?:<[^>]*>)|(" + user_phrases_learning + ")", 'iug');
            } else {
                pattern = new RegExp("(?:<[^>]*>)|(?<![\\p{L}])(" + user_phrases_learning + ")(?![\\p{L}])", 'iug');
            }

            text = text.replace(pattern, function(match, g1) {
                return g1 === undefined ? match : "<a class='word reviewing learning' data-toggle='modal' data-bs-target='#myModal'>" + g1 + "</a>";
            });
        }

        if (user_phrases_learned) {
            user_phrases_learned  = user_phrases_learned.slice(0, -1); // remove trailing |

            if (langs_with_no_word_separator.includes(doclang)) {
                pattern = new RegExp("(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(" + user_phrases_learned + ")", 'iug');
            } else {
                pattern = new RegExp("(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(?<![\\p{L}])(" + user_phrases_learned + ")(?![\\p{L}])", 'iug');
            }

            text = text.replace(pattern, function(match, g1) {
                return g1 === undefined ? match : "<a class='word learned' data-toggle='modal' data-bs-target='#myModal'>" + g1 + "</a>";
            });
        }

        if (user_words_learning) {
            user_words_learning = user_words_learning.slice(0, -1); // remove trailing |

            if (langs_with_no_word_separator.includes(doclang)) {
                pattern = new RegExp("(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(" + user_words_learning + ")", 'iug');
            } else {
                pattern = new RegExp("(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(?<![\\p{L}])(" + user_words_learning + ")(?![\\p{L}])", 'iug');
            }

            text = text.replace(pattern, function(match, g1) {
                return g1 === undefined ? match : "<a class='word reviewing learning' data-toggle='modal' data-bs-target='#myModal'>" + g1 + "</a>";
            });
        }

        if (user_words_learned) {
            user_words_learned  = user_words_learned.slice(0, -1); // remove trailing |
            
            if (langs_with_no_word_separator.includes(doclang)) {
                pattern = new RegExp("(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(" + user_words_learned + ")", 'iug');
            } else {
                pattern = new RegExp("(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(?<![\\p{L}])(" + user_words_learned + ")(?![\\p{L}])", 'iug');
            }
            
            text = text.replace(pattern, function(match, g1) {
                return g1 === undefined ? match : "<a class='word learned' data-toggle='modal' data-bs-target='#myModal'>" + g1 + "</a>";
            });
        }

    // 2. underline frequency list words

    if (data.high_freq) {
        high_freq = data.high_freq.join('|');
        
        if (langs_with_no_word_separator.includes(doclang)) {
            pattern = new RegExp("(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(" + high_freq + ")", 'iug');
        } else {
            pattern = new RegExp("(?:\s*<a class='word[^>]+>.*?<\/a>|<[^>]*>)|(?<![\\p{L}])(" + high_freq + ")(?![\\p{L}])", 'iug');
        }

        text = text.replace(pattern, function(match, p1, offset, string) {
            return p1 === undefined ? match : "<a class='word frequency-list' data-toggle='modal' data-bs-target='#myModal'>" + p1 + "</a>";
        });
    }

    return addLinks(text, doclang);
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
}