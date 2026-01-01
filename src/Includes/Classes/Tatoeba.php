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

class Tatoeba
{
    public $lang      = '';
    public $word      = '';
    public $sentences = [];
    private const BASE_URL = 'https://api.dev.tatoeba.org/unstable';
    private const MIN_NR_OF_WORDS = 3;
    private const MAX_NR_OF_SENTENCES = 10;

    /**
     * Constructor
     *
     * @param string $lang
     * @param string $word
     */
    public function __construct(string $lang, string $word)
    {
        $this->lang = $lang;
        $this->word = $word;
    } // end __construct()

    /**
     * Get example sentences from Tatoeba
     *
     * @throws UserException If there's a problem fetching or parsing the response
     * @return mixed
     */
    public function fetchExampleSentences(): mixed
    {
        $url = sprintf("%s/sentences?lang=%s&q=%s&word_count=%s-&sort=relevance&limit=%s", 
            self::BASE_URL,
            $this->lang,
            rawurlencode($this->word),
            self::MIN_NR_OF_WORDS,
            self::MAX_NR_OF_SENTENCES
        );
        
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_USERAGENT      => MOCK_USER_AGENT,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json'
            ]
        ];

        $json_response = Curl::getUrlContents($url, $options);

        if (!$json_response) {
            throw new UserException("Error fetching example sentences for '{$this->word}' in Tatoeba");
        }

        return $this->filterExampleSentences($json_response);
    } // end fetchExampleSentences()

    /**
     * Removes unnecessary meta data from json response by Tatoeba. Only leaves an array with the example
     * sentences. Also, those example sentences that DO NOT contain the exact word being searched
     * are filtered out.
     *
     * @param string $json_response
     * @return array
     */
    private function filterExampleSentences(string $json_response): array
    {
        $response = json_decode($json_response);
        $filtered_sentences = [];

        // filter example sentences that contain the exact word. Tatoeba
        // usually also returns approximate matches.
        foreach ($response->data as $item) {
            if (mb_stripos($item->text, $this->word)) {
                $match_to_add['title'] = 'Tatoeba';
                $match_to_add['author'] = $item->owner ? ucfirst($item->owner) : 'Anonymous';
                $match_to_add['text'] = $item->text;
                $match_to_add['source_uri'] = 'https://tatoeba.org';

                $filtered_sentences[] = $match_to_add;
            }
        }

        shuffle($filtered_sentences); // shuffle example sentences to randomize order
        return $filtered_sentences;
    } // end filterExampleSentences()
}
