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
    } // end Dictionaries.fetchURIs

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
    } // end Dictionaries.getWordFrequency

    return {
        fetchURIs,
        getWordFrequency,
        getURIs: () => URIs
    };
})();

const SentenceExtractor = (() => {
    const common_contractions = {
        universal: ["etc.", "et al.", "Mr.", "Ms.", "Mrs.", "Dr.", "Prof."],
        ar: [],
        bg: ["т.е.", "и т.н.", "и др."],
        ca: ["p. ex.", "p. s."],
        zh: [],
        hr: ["npr.", "itd.", "dr.", "str."],
        cs: ["tj.", "atd.", "č. p.", "str."],
        da: ["dvs.", "osv."],
        nl: ["d.w.z.", "enz."],
        en: ["Rev.", "St.", "No.", "Jr.", "Sr.", "Ph.D."],
        fr: ["M.", "Mme.", "Mlle.", "p. ex.", "p. j."],
        de: ["z.B.", "u.a.", "d.h.", "usw.", "Nr."],
        el: ["π.χ.", "κ.λπ."],
        he: ["וכו.", "ד\"ר."],
        hi: ["डॉ.", "श्री."],
        hu: ["pl.", "stb.", "u.ö."],
        it: ["Sig.", "Sig.ra", "ecc.", "p.es.", "dott."],
        ja: [],
        ko: [],
        no: ["dvs.", "osv."],
        pl: ["np.", "itp.", "dr."],
        pt: ["Sr.", "Sra.", "Dra.", "p. ex."],
        ro: ["d-l.", "d-na.", "ș.a.m.d."],
        ru: ["т.е.", "и т.д.", "с. (стр.)"],
        sk: ["tzn.", "atď."],
        sl: ["npr.", "ipd."],
        es: ["Sr.", "Sra.", "Dra.", "p. ej."],
        sv: ["t.ex.", "dvs.", "osv."],
        tr: ["örn.", "vb."],
        vi: ["v.v.", "Tp."]
    };

    let current_contractions;

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
        // check if text is part of an initialism (initials of some sort, like U.S., U.K.)
        let index = sel_index;
        let count = 0;

        let char_length = $($all_anchors[index]).text().length;

        while (char_length === 1) {
            let next_char = $($all_anchors[index + direction]).text().trim();

            if (next_char === '.') {
                count += 2;
                index += direction * 2;
            } else {
                index += direction;
            }

            char_length = $($all_anchors[index + direction]).text().length;
        }

        return count;
    } // end SentenceExtractor.isAbbreviation

    function hasContraction($all_anchors, sel_index, direction) {
        // check if text is part of an initialism (initials of some sort, like U.S., U.K.)
        const current_element_text = direction === 1
            ? $($all_anchors[sel_index]).text()
            : $($all_anchors[sel_index - 2]).text();

        return current_contractions.some(abbr => current_element_text === abbr);
    } // end SentenceExtractor.hasContraction

    function updateCurrentAbbreviationList(iso_code) {
        current_contractions = [];

        if (common_contractions.universal) {
            const universal_contractions = common_contractions.universal.map(contraction => {
                // Regex to find the substring starting from the second-to-last character
                // and search backward until a non-word character is found
                const match = RegExp(/(\w+)\W*$/u).exec(contraction);
                return match ? match[1] : contraction; // Fallback to the full contraction if no match
            });
            current_contractions.push(...universal_contractions);
        }

        if (common_contractions[iso_code]) {
            const localized_contractions = common_contractions[iso_code].map(contraction => {
                // Regex to find the substring starting from the second-to-last character
                // and search backward until a non-word character is found
                const match = RegExp(/(\w+)\W*$/u).exec(contraction);
                return match ? match[1] : contraction; // Fallback to the full contraction if no match
            });
            current_contractions.push(...localized_contractions);
        }
    } // end SentenceExtractor.updateCurrentAbbreviationList

    /**
     * @function getLanguageQuotes
     * @description Retrieves the quote marks configuration for the specified language ISO code.
     * @param {string} langIso - The ISO code for the language (e.g., 'en', 'fr', etc.).
     * @returns {object} An object containing 'single' and 'double' quote arrays for opening and closing quotes.
     */
    function getLanguageQuotes(langIso) {
        const quote_marks = {
            // Western languages
            'en': {
                single: { open: ["'", '\u2018', '\u201B'], close: ["'", '\u2019', '\u201A'] },
                double: { open: ['"', '\u201C', '\u2033'], close: ['"', '\u201D', '\u2033'] }
            },
            'fr': {
                single: { open: ["'", '\u2018'], close: ["'", '\u2019'] },
                double: { open: ['\u00AB', '"'], close: ['\u00BB', '"'] }
            },
            'de': {
                single: { open: ["'", '\u2018'], close: ["'", '\u2019'] },
                double: { open: ['"', '\u201E', '\u201C'], close: ['"', '\u201D'] }
            },
            'es': {
                single: { open: ["'", '\u2018'], close: ["'", '\u2019'] },
                double: { open: ['\u00AB', '"'], close: ['\u00BB', '"'] }
            },
            'it': {
                single: { open: ["'", '\u2018'], close: ["'", '\u2019'] },
                double: { open: ['\u00AB', '"'], close: ['\u00BB', '"'] }
            },
            'pt': {
                single: { open: ["'", '\u2018'], close: ["'", '\u2019'] },
                double: { open: ['\u00AB', '"'], close: ['\u00BB', '"'] }
            },
            // Eastern languages
            'zh': {
                single: { open: ["'"], close: ["'"] },
                double: { open: ['"'], close: ['"'] }
            },
            'ja': {
                single: { open: ["'"], close: ["'"] },
                double: { open: ['"'], close: ['"'] }
            },
            'ko': {
                single: { open: ["'"], close: ["'"] },
                double: { open: ['"'], close: ['"'] }
            },
            'ar': {
                single: { open: ['\u2018'], close: ['\u2019'] },
                double: { open: ['\u201C'], close: ['\u201D'] }
            },
            'he': {
                single: { open: ['\u2018'], close: ['\u2019'] },
                double: { open: ['\u201C'], close: ['\u201D'] }
            },
            // Additional European languages
            'ru': {
                single: { open: ["'", '\u2018'], close: ["'", '\u2019'] },
                double: { open: ['\u00AB', '"'], close: ['\u00BB', '"'] }
            },
            'pl': {
                single: { open: ["'", '\u2018'], close: ["'", '\u2019'] },
                double: { open: ['\u201E', '"'], close: ['\u201D'] }
            },
            'tr': {
                single: { open: ["'", '\u2018'], close: ["'", '\u2019'] },
                double: { open: ['"'], close: ['"'] }
            },
            'el': {
                single: { open: ["'", '\u2018'], close: ["'", '\u2019'] },
                double: { open: ['"', '\u00AB'], close: ['"', '\u00BB'] }
            },
            'vi': {
                single: { open: ["'", '\u2018'], close: ["'", '\u2019'] },
                double: { open: ['"'], close: ['"'] }
            }
        };
        return quote_marks[langIso] || quote_marks['en'];
    } // end SentenceExtractor.getLanguageQuotes

    /**
     * @function findStrayClosingQuote
     * @description Scans the text for a closing quote that lacks a corresponding opening quote.
     * For quotes where the opening and closing characters are identical, it checks if there is an odd number of occurrences
     * and returns the first (or in this case, the last) stray occurrence. For distinct pairs, it simulates pairing by scanning
     * the text and returns the corresponding opening quote when a closing quote appears without a matching opener.
     * @param {string} text - The text to scan.
     * @param {string[]} open_quotes - Array of opening quote characters.
     * @param {string[]} close_quotes - Array of closing quote characters.
     * @returns {string} A string with the missing opening quote character.
     */
    function findStrayClosingQuote(text, open_quotes, close_quotes) {
        // First, handle pairs where opening and closing quotes are identical.
        for (let i = 0; i < open_quotes.length; i++) {
            if (open_quotes[i] === close_quotes[i]) {
                // Count how many times this quote appears.
                // let occurrences = text.split(open_quotes[i]).length - 1;
                const regex = new RegExp(open_quotes[i] + '(?=$|\\W)', 'gu');
                let occurrences = (text.match(regex) || []).length;

                if (occurrences % 2 !== 0) {
                    // We have an odd count so one occurrence is unmatched.
                    return open_quotes[i];
                }
            }
        }

        // Now, handle quote pairs where the opening and closing characters differ.
        for (let i = 0; i < open_quotes.length; i++) {
            if (open_quotes[i] !== close_quotes[i]) {
                // Count the occurrences in text.
                const open_regex = new RegExp(open_quotes[i] + '(?=$|\\W)', 'gu');
                let open_count = text.split(open_regex).length - 1;
                const close_regex = new RegExp(close_quotes[i] + '(?=$|\\W)', 'gu');
                let close_count = text.split(close_regex).length - 1;
                if (close_count > open_count) {
                    // There is an uneven amount of closing quotes.
                    // We now simulate pairing by scanning through text.
                    let balance = 0;
                    for (const element of text) {
                        if (element === open_quotes[i]) {
                            balance++;
                        } else if (element === close_quotes[i]) {
                            if (balance > 0) {
                                balance--;
                            } else {
                                // A closing quote was encountered with no matching opener.
                                // Return the matching opening quote character along with the index of the stray closing quote.
                                return open_quotes[i];
                            }
                        }
                    }
                }
            }
        }
        return "";
    } // end SentenceExtractor.findStrayClosingQuote

    /**
     * @function fixUnmatchedQuotes
     * @description Checks for a stray closing quote in the text that lacks a corresponding opening quote.
     * It temporarily removes any trailing closing quote before looking for stray quotes, then fixes the text
     * by prepending the missing opening quote if needed, and finally reattaches the removed closing quote.
     * @param {string} text - The text (sentence) to be fixed.
     * @param {object} lang_quotes - The quote marks configuration object.
     * @returns {string} The text with the unmatched quote fixed.
     */
    function fixUnmatchedQuotes(text, lang_quotes) {
        // Build arrays of all opening and closing quotes from the language configuration.
        const all_open_quotes = [...lang_quotes.single.open, ...lang_quotes.double.open];
        const all_close_quotes = [...lang_quotes.single.close, ...lang_quotes.double.close];

        // If the last character is a closing quote, remove it.
        if (all_open_quotes.includes(text[text.length - 1])) {
            text = text.slice(0, -1);
        }

        // Find the stray closing quote (or missing opening quote) in the text.
        const stray = findStrayClosingQuote(text, all_open_quotes, all_close_quotes);
        if (stray) {
            text = stray + text;
        }

        return text;
    } // end SentenceExtractor.fixUnmatchedQuotes

    /**
     * @function buildSentenceFromAnchors
     * @description Combines text from anchors between the given indices while handling line breaks.
     * If an anchor's text includes a line break and a sentence delimiter, then:
     *   - If the anchor is before or equal to the selected index, the accumulated sentence is reset.
     *   - If the anchor is after the selected index, the sentence building stops.
     * @param {Array} anchors - The list of anchor elements.
     * @param {number} start_index - The starting index for sentence extraction.
     * @param {number} end_index - The ending index for sentence extraction.
     * @param {number} sel_index - The index of the selected word.
     * @param {RegExp} sentence_delimiters - The regular expression for detecting sentence delimiters.
     * @returns {string} The combined sentence text.
     */
    function buildSentenceFromAnchors(anchors, start_index, end_index, sel_index, sentence_delimiters) {
        let sentence = "";
        for (let i = start_index; i <= end_index; i++) {
            const text = $(anchors[i]).text();
            // If the anchor's text contains any line break...
            if (/[\r\n]/.test(text)) {
                // ...and it also contains a sentence delimiter (Western/Eastern punctuation)...
                const match = text.match(sentence_delimiters);

                if (match) {
                    if (i <= sel_index) {
                        // If this element comes before (or is the selected word),
                        // reset the accumulated sentence and skip appending this fragment.
                        sentence = "";
                        continue;
                    } else {
                        // If this element comes after the selected word, stop sentence accumulation.
                        sentence += match[0]; // Append the detected delimiter
                        break;
                    }
                }
            }
            sentence += text;
        }
        // Finally, remove any lingering line breaks and trim extra whitespace.
        return sentence.replace(/(\r\n|\n|\r)/g, " ").trim();
    } // end SentenceExtractor.buildSentenceFromAnchors

    /**
     * @function extractSentence
     * @description Extracts a complete sentence containing the selected word, handling punctuation, abbreviations,
     * and unmatched quotes appropriately.
     * @param {HTMLElement} $selword - The selected word element.
     * @returns {string} The extracted sentence.
     */
    function extractSentence($selword) {
        // Define sentence delimiters for various languages (Western and Eastern punctuation).
        let sentence_delimiters = /[.!?\u3002\uFF01\uFF1F](?=\s|$)/;
        const $all_anchors = TextProcessor.getAnchorsList();
        const sel_index = TextProcessor.getAnchorIndex($selword);

        if (sel_index < 0 || sel_index >= $all_anchors.length) {
            console.error("Index out of range");
            return "";
        }

        const text_lang_iso = $('#text').data('text-lang');
        updateCurrentAbbreviationList(text_lang_iso);

        // Crawl backward to find the start of the sentence.
        let start_index = sel_index;
        while (start_index > 0) {
            const prev_element_text = $($all_anchors[start_index - 1]).text();
            if (sentence_delimiters.test(prev_element_text)) {
                const abbreviation_length = isAbbreviation($all_anchors, start_index - 2, -1);
                if (abbreviation_length) {
                    start_index -= abbreviation_length + 2;
                } else {
                    const has_contraction = hasContraction($all_anchors, start_index, -1);
                    if (!has_contraction) {
                        break;
                    }
                }
            }
            start_index--;
        }

        // Crawl forward to find the end of the sentence.
        let end_index = sel_index;
        while (end_index < $all_anchors.length - 1) {
            const next_element_text = $($all_anchors[end_index + 1]).text();
            if (sentence_delimiters.test(next_element_text)) {
                const abbreviation_length = isAbbreviation($all_anchors, end_index, 1);
                if (abbreviation_length) {
                    end_index += abbreviation_length;
                } else {
                    const has_contraction = hasContraction($all_anchors, end_index, 1);
                    if (!has_contraction) {
                        end_index++;
                        break;
                    }
                }
            }
            end_index++;
        }

        // Combine the sentence elements between the determined start and end indices.
        sentence_delimiters = /[.!?\u3002\uFF01\uFF1F]/;
        let sentence = buildSentenceFromAnchors($all_anchors, start_index, end_index, sel_index, sentence_delimiters);

        // Handle unmatched quotes by prepending a missing opening quote if needed.
        const lang_quotes = getLanguageQuotes(text_lang_iso);
        sentence = fixUnmatchedQuotes(sentence, lang_quotes);

        // Clean up extra whitespace in sentence
        sentence = sentence.replace(/\s+/g, " ").trim();
        return sentence;
    } // end SentenceExtractor.extractSentence


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
    } // end LinkBuilder.forTranslationInText

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
    } // end LinkBuilder.forTranslationInVideo

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
    } // end LinkBuilder.forTranslationInStudy

    return {
        forWordInDictionary,
        forTranslationInText,
        forTranslationInVideo,
        forTranslationInStudy
    };
})();