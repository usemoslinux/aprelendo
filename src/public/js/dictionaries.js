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



const Dictionaries = (() => {
    // dictionary/translator variables
    let URIs = {
        "dictionary": '',
        "img_dictionary": '',
        "translator": ''
    }

    /**
     * Fetches dictionary and translator URIs from the server via an AJAX GET request.
     * @returns {jqXHR} A jQuery promise object that resolves with the JSON response or rejects with error information.
     */
    const fetchURIs = () => {

        $.ajax({
            url: "/ajax/getdicuris.php",
            type: "GET",
            dataType: "json"
        }).done(function (data) {
            if (data.error_msg == null) {
                URIs.dictionary = data.dictionary_uri;
                URIs.img_dictionary = data.img_dictionary_uri;
                URIs.translator = data.translator_uri;
            }
        }); // end $.ajax
    }

    /**
     * Shows message for high & medium frequency words in dictionary modal window
     * @param {int} frequency_index
     */
    const getWordFrequency = (frequency_index) => {
        if (frequency_index == 100) {
            return 'Medium/low'
        } else if (frequency_index < 81) {
            return 'Very high';
        } else if (frequency_index < 97) {
            return 'High';
        }
    } // end getWordFrequency

    return {
        fetchURIs,
        getWordFrequency,
        getURIs: () => URIs
    };
})();

// TODO: this is failing when using abbreviations that don't end sentences (dot followed by space): "U.S. relations"
// "e.g.", "i.e.", "etc.", "Mr.", "Mrs.", "Dr.", "vs.", "U.S.", "U.K.", "No."
// It is also failing when a decimal number starts a sentence: "2.8 % of the population" or three consecutive dots
// are used instead of ellipsis character.
const SentenceExtractor = (() => {
    /**
    * Checks if the given text contains any known abbreviation or ellipsis
    * (universal or language-specific), or any uppercase abbreviation with dots
    * (e.g., "U.S.", "U.K.").
    * @param {string} text - The text to examine.
    * @param {string} isoCode - Two-letter ISO code (e.g. 'en', 'es').
    * @returns {boolean} true if at least one matching abbreviation or uppercase-dotted
    *                   word is found; false otherwise.
    */
   function isAbbreviation($all_anchors, sel_index, direction) {
        const next_element_text = $($all_anchors[sel_index + direction]).text(); 
        const check_pattern = /^[\p{P}\p{Z}]$/u;
        return check_pattern.test(next_element_text);
   }
    /**
   * Extracts the sentence to which a specific <a> element belongs.
   * @param {Jquery Object} $selword - The index of the <a> element.
   * @returns {string} - The full sentence containing the selected <a> element.
   */
    function extractSentence($selword) {
        // Define sentence delimiters for Western and Eastern languages
        // Western sentence delimiters include . (period), ! (exclamation mark), and ? (question mark).
        // Eastern sentence delimiters include 。 (Chinese/Japanese period), ！ (Chinese/Japanese exclamation mark), 
        // and ？ (Chinese/Japanese question mark).
        // Match only if followed by a space or end of text
        const sentence_delimiters = /[.!?\u3002\uFF01\uFF1F](?=\s|$)/;
        const $all_anchors = TextProcessor.getAnchorsList();
        const sel_index = TextProcessor.getAnchorIndex($selword);

        // Ensure the index is within bounds
        if (sel_index < 0 || sel_index >= $all_anchors.length) {
            console.error("Index out of range");
            return "";
        }

        // Crawl backward to find the start of the sentence
        let start_index = sel_index;
        while (start_index > 0) {
            const prev_element_text = $($all_anchors[start_index - 1]).text();
            if (sentence_delimiters.test(prev_element_text) && !isAbbreviation($all_anchors, start_index, -1)) {
                break;
            }
            start_index--;
        }

        // Crawl forward to find the end of the sentence
        let end_index = sel_index;
        while (end_index < $all_anchors.length - 1) {
            const next_element_text = $($all_anchors[end_index + 1]).text();
            if (sentence_delimiters.test(next_element_text) && !isAbbreviation($all_anchors, end_index, 1)) {
                end_index++; // Include the delimiter in the result
                break;
            }
            end_index++;
        }

        // Combine the sentence elements
        let sentence = "";
        for (let i = start_index; i <= end_index; i++) {
            sentence += $($all_anchors[i]).text();
        }

        return sentence.replace(/(\r\n|\n|\r)/g, " ").trim();
    }

    return {
        extractSentence
    };
})();

const LinkBuilder = (() => {
    /**
     * Build Dictionary & Image Dictionary links, provided a base URI is given
     * @param {string} sel_word 
     * @param {string} dictionary_URI 
     * @returns string
     */
    const forWordInDictionary = (dictionary_URI, sel_word) => {
        const word = sel_word.replace(/\s+/g, " ").trim();
        return dictionary_URI.replace("%s", encodeURIComponent(word));
    } // end LinkBuilder.forWordInDictionary

    /**
     * Builds translator link including the paragraph to translate as a parameter
     * Used for texts, ebooks (not YT videos & offline videos)
     * @param {string} translator_URI - The initial translator URI 
     * @param {jQuery} $selword - The element selected by user
     * @returns {string} The complete translator link
     */
    const forTranslationInText = (translator_URI, $selword) => {
        let sentence = SentenceExtractor.extractSentence($selword);

        return translator_URI.replace("%s", encodeURI(sentence));
    } // end forTranslationInText

    /**
     * Builds a translator link including the paragraph to translate as a parameter.
     * Used only for YT videos and offline videos
     * @param {string} translator_URI - The initial translator URI 
     * @param {jQuery} $selword - The element selected by user
     * @returns {string} The complete translator link
     */
    const forTranslationInVideo = (translator_URI, $selword) => {
        let sentence = SentenceExtractor.extractSentence($selword);

        return translator_URI.replace("%s", encodeURIComponent(sentence));
    } // end forTranslationInVideo

    /**
     * Builds translator link using the word object as a parameter
     * Used for Study sessions only
     * @param {string} translator_URI 
     * @param {string} $selword 
     * @returns string
     */
    const forTranslationInStudy = (translator_URI, $selword) => {
        const sentence = $selword.parent("p").text().trim() || $selword.text();
        return translator_URI.replace("%s", encodeURIComponent(sentence));
    } // end forTranslationInStudy

    return {
        forWordInDictionary,
        forTranslationInText,
        forTranslationInVideo,
        forTranslationInStudy
    };
})();