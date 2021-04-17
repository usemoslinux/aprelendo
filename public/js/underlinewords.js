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

/**
 * Adds spans to unknown words and whitespaces
 * This way these unknown words will be clickable and empty spaces will be recognized 
 * when selecting phrases
 * @param {string} text 
 * @returns string
 */
function addLinks(text) {
    // add span to unknown words
    var pattern = new RegExp(/(?:\s*<span class='word[^>]+>.*?<\/span>|<[^>]*>)|(\p{L}+)/iug);
    result = text.replace(pattern, function(match, g1, offset, string) {
        return g1 === undefined ? match : '<span class="word" data-toggle="modal" data-target="#myModal">' + g1 + '</span>';
    });

    // add span to whitespaces
    pattern = new RegExp(/(?<=<[^>]*>)([^\p{L}<]+)/ug);
    result = result.replace(pattern, function(match, g1, offset, string) {
        return g1 === undefined ? match : '<span>' + g1 + '</span>';    
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
function underlineWords(data) {
    var text = data.text;
    var pattern = '';
    var user_phrases_learning = '';
    var user_phrases_learned = '';
    var user_words_learning = '';
    var user_words_learned = '';
    var high_freq = '';

    // 1. colorize phrases & words
    
    Object.values(data.user_words).forEach(element => {
        if (element.is_phrase > 0) {
            if (element.status > 0) {
                user_phrases_learning += element.word + '|';    
            } else {
                user_phrases_learned += element.word + '|';
            }
        } else {
            if (element.status > 0) {
                user_words_learning += element.word + '|';    
            } else {
                user_words_learned += element.word + '|';
            }
        }
    });

        if (user_phrases_learning) {
            user_phrases_learning = user_phrases_learning.slice(0, -1); // remove trailing |
            pattern = new RegExp("(?:<[^>]*>)|(?<![\\p{L}])(" + user_phrases_learning + ")(?![\\p{L}])", 'iug');
            text = text.replace(pattern, function(match, g1, offset, string) {
                return g1 === undefined ? match : "<span class='word reviewing learning' data-toggle='modal' data-target='#myModal'>" + g1 + "</span>";
            });
        }

        if (user_phrases_learned) {
            user_phrases_learned  = user_phrases_learned.slice(0, -1); // remove trailing |
            pattern = new RegExp("(?:\s*<span class='word[^>]+>.*?<\/span>|<[^>]*>)|(?<![\\p{L}])(" + user_phrases_learned + ")(?![\\p{L}])", 'iug');
            text = text.replace(pattern, function(match, g1, offset, string) {
                return g1 === undefined ? match : "<span class='word learned' data-toggle='modal' data-target='#myModal'>" + g1 + "</span>";
            });
        }

        if (user_words_learning) {
            user_words_learning = user_words_learning.slice(0, -1); // remove trailing |
            pattern = new RegExp("(?:\s*<span class='word[^>]+>.*?<\/span>|<[^>]*>)|(?<![\\p{L}])(" + user_words_learning + ")(?![\\p{L}])", 'iug');
            text = text.replace(pattern, function(match, g1, offset, string) {
                return g1 === undefined ? match : "<span class='word reviewing learning' data-toggle='modal' data-target='#myModal'>" + g1 + "</span>";
            });
        }

        if (user_words_learned) {
            user_words_learned  = user_words_learned.slice(0, -1); // remove trailing |
            pattern = new RegExp("(?:\s*<span class='word[^>]+>.*?<\/span>|<[^>]*>)|(?<![\\p{L}])(" + user_words_learned + ")(?![\\p{L}])", 'iug');
            text = text.replace(pattern, function(match, g1, offset, string) {
                return g1 === undefined ? match : "<span class='word learned' data-toggle='modal' data-target='#myModal'>" + g1 + "</span>";
            });
        }

    // 2. colorize frequency list words

    if (data.high_freq) {
        high_freq = data.high_freq.join('|');
        pattern = new RegExp("(?:\s*<span class='word[^>]+>.*?<\/span>|<[^>]*>)|(?<![\\p{L}])(" + high_freq + ")(?![\\p{L}])", 'iug');
        text = text.replace(pattern, function(match, p1, offset, string) {
            return p1 === undefined ? match : "<span class='word frequency-list' data-toggle='modal' data-target='#myModal'>" + p1 + "</span>";
        });
    }

    return addLinks(text);
}