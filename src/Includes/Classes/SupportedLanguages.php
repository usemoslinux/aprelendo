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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Aprelendo;

class SupportedLanguages
{
    // ISO-639-3 codes used for "problematic" languages:
    // Norwegian: nor is the "umbrella", nob is Bokmål (the version 90% of people use).
    // Chinese: zho is the "umbrella", cmn is Mandarin specifically.

    private static $languages = [
        'ar' => ['ISO-639-1' => 'ar', 'ISO-639-3' => 'ara', 'name' => 'arabic'],
        'bg' => ['ISO-639-1' => 'bg', 'ISO-639-3' => 'bul', 'name' => 'bulgarian'],
        'ca' => ['ISO-639-1' => 'ca', 'ISO-639-3' => 'cat', 'name' => 'catalan'],
        'cs' => ['ISO-639-1' => 'cs', 'ISO-639-3' => 'ces', 'name' => 'czech'],
        'da' => ['ISO-639-1' => 'da', 'ISO-639-3' => 'dan', 'name' => 'danish'],
        'de' => ['ISO-639-1' => 'de', 'ISO-639-3' => 'deu', 'name' => 'german'],
        'el' => ['ISO-639-1' => 'el', 'ISO-639-3' => 'ell', 'name' => 'greek'],
        'en' => ['ISO-639-1' => 'en', 'ISO-639-3' => 'eng', 'name' => 'english'],
        'es' => ['ISO-639-1' => 'es', 'ISO-639-3' => 'spa', 'name' => 'spanish'],
        'fr' => ['ISO-639-1' => 'fr', 'ISO-639-3' => 'fra', 'name' => 'french'],
        'he' => ['ISO-639-1' => 'he', 'ISO-639-3' => 'heb', 'name' => 'hebrew'],
        'hi' => ['ISO-639-1' => 'hi', 'ISO-639-3' => 'hin', 'name' => 'hindi'],
        'hr' => ['ISO-639-1' => 'hr', 'ISO-639-3' => 'hrv', 'name' => 'croatian'],
        'hu' => ['ISO-639-1' => 'hu', 'ISO-639-3' => 'hun', 'name' => 'hungarian'],
        'it' => ['ISO-639-1' => 'it', 'ISO-639-3' => 'ita', 'name' => 'italian'],
        'ja' => ['ISO-639-1' => 'ja', 'ISO-639-3' => 'jpn', 'name' => 'japanese'],
        'ko' => ['ISO-639-1' => 'ko', 'ISO-639-3' => 'kor', 'name' => 'korean'],
        'nl' => ['ISO-639-1' => 'nl', 'ISO-639-3' => 'nld', 'name' => 'dutch'],
        'no' => ['ISO-639-1' => 'no', 'ISO-639-3' => 'nob', 'name' => 'norwegian'],
        'pl' => ['ISO-639-1' => 'pl', 'ISO-639-3' => 'pol', 'name' => 'polish'],
        'pt' => ['ISO-639-1' => 'pt', 'ISO-639-3' => 'por', 'name' => 'portuguese'],
        'ro' => ['ISO-639-1' => 'ro', 'ISO-639-3' => 'ron', 'name' => 'romanian'],
        'ru' => ['ISO-639-1' => 'ru', 'ISO-639-3' => 'rus', 'name' => 'russian'],
        'sk' => ['ISO-639-1' => 'sk', 'ISO-639-3' => 'slk', 'name' => 'slovak'],
        'sl' => ['ISO-639-1' => 'sl', 'ISO-639-3' => 'slv', 'name' => 'slovenian'],
        'sv' => ['ISO-639-1' => 'sv', 'ISO-639-3' => 'swe', 'name' => 'swedish'],
        'tr' => ['ISO-639-1' => 'tr', 'ISO-639-3' => 'tur', 'name' => 'turkish'],
        'vi' => ['ISO-639-1' => 'vi', 'ISO-639-3' => 'vie', 'name' => 'vietnamese'],
        'zh' => ['ISO-639-1' => 'zh', 'ISO-639-3' => 'cmn', 'name' => 'chinese']
    ];

    /**
     * Find language data and optionally return a specific field.
     * * @param string $search The value to look for (ISO-639-1, ISO-639-3, or name)
     * @param string|null $field The specific field to return (ISO-639-1, ISO-639-3, or name)
     * @return mixed Array of info, a specific string, or null if not found
     */
    public static function get(string $search, ?string $field = null) {
        $search = strtolower(trim($search));
        $result = null;

        // find the language data
        foreach (self::$languages as $data) {
            if (
                $data['ISO-639-1'] === $search || 
                $data['ISO-639-3'] === $search || 
                $data['name'] === $search
            ) {
                $result = $data;
                break;
            }
        }

        if (!$result) {
            return null;
        }

        // if a specific field is requested (and exists), return only that string
        if ($field && isset($result[$field])) {
            return $result[$field];
        }

        // otherwise return the whole array
        return $result;
    }

    /**
     * Returns complete list of supported languages, optionally sorted by a field.
     *
     * @param string|null $sortBy The field to sort by (ISO-639-1, ISO-639-3, or name)
     * @return array
     */
    public static function getAll(?string $sortBy = null): array
    {
        $cache_key = 'languages_list';
        $languages = Cache::get($cache_key, 86400 * 30); // cache for 30 days

        if ($languages === null) {
            $languages = self::$languages;
            Cache::set($cache_key, $languages);
        }

        $first = reset($languages);
        if ($sortBy && $first && isset($first[$sortBy])) {
            uasort($languages, function ($left, $right) use ($sortBy) {
                return strcasecmp($left[$sortBy], $right[$sortBy]);
            });
        }

        return $languages;
    } // end getAll()

}
