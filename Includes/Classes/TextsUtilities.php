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

namespace Aprelendo;

class TextsUtilities
{
    
    /**
    * Determines if $text is valid XML code & extracts text from it
    *
    * @param string $xml
    * @return string|boolean
    */
    public static function extractFromXML(string $xml): string|bool
    {
        // check if $text is valid XML (video transcript) or simple text
        libxml_use_internal_errors(true); // used to avoid raising Exceptions in case of error
        $xml = (array)simplexml_load_string(html_entity_decode(stripslashes($xml)));
        
        return array_key_exists('text', $xml) ? implode(" ", $xml['text']) : false;
    } // end extractFromXML()

    /**
     * Get audio_uri for embedding
     *
     * @param string $audio_uri
     * @return string
     */
    public static function getAudioUriForEmbbeding(string $audio_uri): string
    {
        $url = '';

        if (!empty($audio_uri)) {
            $url = "https://www.googleapis.com/drive/v3/files/";
            $file_id = '';
            $pattern = '/\/d\/([-\w]+)\//'; // regex to match the file id
            if (preg_match($pattern, $audio_uri, $matches)) {
                $file_id = $matches[1]; // return the first captured group (the id)
            }
            
            $url .= $file_id;
            $url .= "?alt=media&key=" . GOOGLE_DRIVE_API_KEY;
        }
        
        return $url;
    } // end getAudioUriForEmbbeding()

    /**
     * Convert author case to Title Case, except for acronyms in author names
     * Input: 'j.r.r. tolkien' >> ouput 'J.R.R. Tolkien'
     *
     * @param string $author
     * @return string
     */
    public static function formatAuthorCase(string $author): string
    {
        return preg_replace_callback(
            '/\b([\p{L}]+(?:\.[\p{L}]+)*)\b/u',
            function($matches) {
                $word = $matches[1];
                if (mb_strpos($word, '.') === false) {
                    // Convert regular word to title case using mb_convert_case
                    return mb_convert_case($word, MB_CASE_TITLE, 'UTF-8');
                } else {
                    // Convert acronym case using mb_convert_case
                    return mb_convert_case($word, MB_CASE_UPPER, 'UTF-8');
                }
            },
            $author
        );
    } // end formatAuthorCase()
}
