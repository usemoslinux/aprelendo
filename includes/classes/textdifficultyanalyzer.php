<?php
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

namespace Aprelendo\Includes\Classes;

class TextDifficultyAnalyzer
{
    private $pdo;
    private $text;

    /**
     * Constructor
     *
     * @param string $text
     */
    public function __construct(string $text) {
        $this->text = $text;
    }

    /**
    * Calculates difficulty level of a given $text
    *
    * Each freqlist table contains the most used words for that specific language, based on opensubtitles data (2018).
    * This means there will be different records for "be, was, were", etc. This data was then filtered with MS Windows
    * and LibreOffice (hunspell) spellcheckers, and entries with strange characters, numbers, names, etc. were all
    * removed. From that filtered list, the % of use of each word was calculated. By adding them, it was possible to
    * determine what percentage of a text a person can understand if he or she knows that word and all the words that
    * appear before in the list. In other words, a frequency_index of 80 means that if a person knows that word and the
    * previous ones, he or she will understand around 80% of any text. Each freqlist table includes words with a
    * WordFreq index of up to 96 (around 10k words).
    * This was done to reduce table size and increase speed.
    *
    * If Score < 60, text difficulty is set to "beginner".
    * If Score < 75, text difficulty is set to "intermediate".
    * Else, text difficulty is set to "advanced".
    *
    * @param string $text
    * @return int
    */
    public function calculateDifficulty(string $text = ''): int
    {
        try {
            $frequency_list_table = '';     // frequency list table name: should be something like frequency_list_en
            $frequency_list_words = [];     // array with all the words in the corresponding frequency list table
            $frequency_list_indexes = [];   // array with all the scores of the words in $frequency_list_words
            $words_in_text = [];            // array with all the valid words in $text
            $sentences_in_text = [];        // array with all sentences in $text (only used to calculate $word_count)
            $word_count = 0;               // number of words in $text
            $nr_of_sentences = 0;           // number of sentences in $text
            $score = 0;                     // readability score of $text
            $xml_text = '';                 // used to check if $text parameter is XML code
            $lang_iso = '';                 // two letter long iso code of the text's language

            // if $text is XML code (video transcript), extract text from XML string
            $xml_text = $this->extractFromXML($text);
            
            if ($xml_text !== false) {
                $text = $xml_text;
            }

            // get learning language ISO name
            $sql = "SELECT `name`
                    FROM `languages`
                    WHERE `id`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->lang_id]);

            // build frequency list table name based on learning language name
            $row = $stmt->fetch(\PDO::FETCH_NUM);
            $lang_iso = $row[0];
            $frequency_list_table = 'frequency_list_' . $lang_iso;
            
            // build frequency list array (around 10.000 words)
            $sql = "SELECT `word`, `frequency_index`
                    FROM `$frequency_list_table`";
            // "WHERE `frequency_index` < 97" is not necessary as db is optimized to only contain
            // frequecy_index values < 97

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $frequency_list_words[] = $row['word'];
                $frequency_list_indexes[] = $row['frequency_index'];
            }

            // if there is no frequency list for this language, return unknown level for text
            if (empty($frequency_list_words)) {
                return 0;
            }

            // calculate nr. of words & nr. of sentences in text
            $word_count = $this->countVocabTokens($lang_iso, $text, $words_in_text);
            $nr_of_sentences = preg_match_all("/([?!.] \p{L}|^\s*?\p{L})/um", $text . ' ', $sentences_in_text);

            // check how many words in the frequency list where found in the "tokenized" list of words of the text
            // and calculate score
            $words_found = array_uintersect($words_in_text[0], $frequency_list_words, 'strcasecmp');
            
            foreach ($words_found as $word_found) {
                $word_index = array_search(strtolower($word_found), $frequency_list_words);
                $score += $frequency_list_indexes[$word_index];
            }
            
            $nr_of_unknown_words = sizeof($words_in_text[0]) - sizeof($words_found);
            $score = (0.6 * ($score + $nr_of_unknown_words * 100) / $word_count)
                + (2 * $word_count / $nr_of_sentences);

            if ($score < 60) {
                $result = 1; // beginner
            } elseif ($score < 75) {
                $result = 2; // intermediate
            } else {
                $result = 3; // advanced
            }
            
            return $result;
        } catch (\PDOException $e) {
            throw new AprelendoException('Error calculating text difficulty level.');
        } finally {
            $stmt = null;
        }
    } // end calculateDifficulty()

    /**
     * Calculates the nr. of words in a text
     *
     * @param string $lang_iso
     * @param string $text
     * @param array $words_in_text
     * @return integer
     */
    private function countVocabTokens(string $lang_iso, string $text, ?array &$words_in_text): int
    {
        /*
            explanation of regex to extract list of words from the text
            
            1st part: select all words including word characters except for those having an apostrophe or single
            quote in the middle, abbreviations (USA or U.S.A.) and words starting with a capital letter
            (?<![\p{L}]['’])(?![A-Z]{2,})(?![a-zA-Z]\.){2,}(?!\p{Lu})\b(\p{L}+)\b(?!['’][\p{L}])
            
            ignore contractions before and after apostrophes or single quotes (often used as apostrophes)
            (?<![\p{L}]['’])     >>>>> ignore contractions before apostrophes or single quotes (ISN't)
            (?!['’][\p{L}])      >>>>> ignore contractions after apostrophes or single quotes (isn'T)
            
            (?![A-Z]{2,})        >>>>> ignore abbreviations (EU, USA, etc.)
            (?![a-zA-Z]\.){2,}   >>>>> ignore abbreviations (E.U, U.S.A., e.g., i.e., etc.)
            (?!\p{Lu})           >>>>> ignore words starting with a capital letter
            \b(\p{L}+)\b         >>>>> select only words word including unicode characters (ignore numbers, etc.)
            
            2nd part: select all words that start with a capital letter which are at the beginning of a line or
            sentence. Ignore all words starting with a capital letter in the middle of a sentence (e.g. names of
            people or places)
            (?![A-Z]{2,})(?![a-zA-Z]\.){2,}(?<=^|[\.\!\?]\s)(?<![\p{L}]['’])\b(\p{L}+)\b(?!['’][\p{L}])
            
            (?<![\p{L}]['’])     >>>>> ignore contractions before apostrophes or single quotes (ISN't)
            (?!['’][\p{L}])      >>>>> ignore contractions after apostrophes or single quotes (isn'T)
            
            (?![A-Z]{2,})        >>>>> ignore abbreviations (EU, USA, etc.)
            (?![a-zA-Z]\.){2,}   >>>>> ignore abbreviations (E.U, U.S.A., e.g., i.e., etc.)
            (?<=^|[\.\!\?]\s)    >>>>> only include words starting a line or a sentence
            \b(\p{L}+)\b         >>>>> select only words word including unicode characters (ignore numbers, etc.)
            
            Note: it is important to include the /u (unicode) and /m (multiline) flags. Unicode, so that word selection
            works as expected for any language. Multiline because otherwise the string would be considered a very long
            string instead of one composed of multiple lines and the beginning of line selector (^) would not work
            correctly.
            
            Note 2: for German we need to use a special regex string as all nouns are capitalized, not only names of
            people or places.
        */
        if ($lang_iso == 'de') {
            $regex_word_filter = "/(?<![\p{L}]['’])(?![A-Z]{2,})(?![a-zA-Z]\.){2,}\b(\p{L}+)\b(?!['’][\p{L}])/um";
        } else {
            $regex_word_filter = "/(?<![\p{L}]['’])(?![A-Z]{2,})(?![a-zA-Z]\.){2,}(?!\p{Lu})\b(\p{L}+)"
                ."\b(?!['’][\p{L}])|(?![A-Z]{2,})(?![a-zA-Z]\.){2,}(?<=^|[\.\!\?]\s)(?<![\p{L}]['’])\b"
                ."(\p{L}+)\b(?!['’][\p{L}])/um";
        }
        
        return preg_match_all($regex_word_filter, $text, $words_in_text);
    } // end countVocabTokens()
}
