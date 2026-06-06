<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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
        libxml_use_internal_errors(true); // used to avoid raising Exceptions in case of error
        $xml_object = simplexml_load_string(stripslashes($xml), \SimpleXMLElement::class, LIBXML_NOCDATA);
        libxml_clear_errors();

        if ($xml_object === false) {
            return false;
        }

        $text_nodes = $xml_object->xpath('//text');

        if ($text_nodes === false || empty($text_nodes)) {
            return false;
        }

        $text_parts = array_map(static function (\SimpleXMLElement $node): string {
            return trim((string)$node);
        }, $text_nodes);

        $text_parts = array_filter($text_parts, static fn(string $text): bool => $text !== '');

        return implode(' ', $text_parts);
    } 

    /**
     * Get audio_uri for embedding
     *
     * @param string $audio_uri
     * @return string
     */
    public static function getGoogleDriveAudioUri(string $audio_uri): string
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
    } 

    /**
     * Checks if the provided URL is a Google Drive link with the specific format
     * expected for file URLs (i.e., "https://drive.google.com/file/d")
     *
     * @param string $audio_uri The URL to be checked.
     * @return bool True if the URL is a Google Drive file link, false otherwise.
     */
    public static function isGoogleDriveLink(string $audio_uri): bool
    {
        return str_starts_with($audio_uri, 'https://drive.google.com/file/d');
    }

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
    } 
}
