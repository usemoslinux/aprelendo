<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

abstract class WordsUtilities
{
    /**
     * Splits text into unique words based on the language.
     * 
     * For languages like Chinese (zh), Japanese (ja), and Korean (ko), it splits into individual characters.
     * For other languages, it splits into words based on Unicode word characters.
     *
     * @param string $text The text to split.
     * @param string $lang_iso The ISO 639-1 language code.
     * @return array An array of unique, lowercase words/characters.
     */
    public static function splitIntoUniqueWords(string $text, string $lang_iso): array
    {
        $langs_with_no_word_separator = ['zh', 'ja', 'ko'];
        $is_no_separator = in_array($lang_iso, $langs_with_no_word_separator);

        if ($is_no_separator) {
            $pattern = '/\p{L}/u';
        } else {
            $pattern = '/\p{L}+/u';
        }

        preg_match_all($pattern, $text, $matches);

        if (empty($matches[0])) {
            return [];
        }

        $words = array_map(function ($word) {
            return mb_strtolower($word, 'UTF-8');
        }, $matches[0]);

        return array_unique($words);
    } 

    /**
     * Exports words to a CSV file
     *
     * It exports either the whole set of words corresponding to a user & language combination,
     * or the specific subset that results from applying additional filters (e.g. $search_text).
     * Results are ordered using $order_by.
     *
     * @param SearchWordsParameters $search_params
     * @return void
     */
    public static function exportToCSV(array $words): void
    {
        $headers = ['Words', 'Status', 'Freq_Level'];

        $fp = fopen('php://output', 'w');

        if ($fp) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="export.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            fputcsv($fp, $headers);

            foreach ($words as $word) {
                fputcsv($fp, [$word['word'], $word['status'], $word['freq_level']]);
            }

            fclose($fp);
        }
    } 
}
