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

use Aprelendo\UserException;

require_once dirname(__DIR__) . '../../config/config.php';

class Curl
{
    /**
     * Gets file contents using curl
     * @param string $url
     * @return string
     */
    public static function getUrlContents(string $url, array $options = []): string
    {
        $ch = curl_init();
        
        if (!isset($options) || empty($options)) {
            $referer = 'https://www.aprelendo.com';
            $options = [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_REFERER => $referer,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true
            ];
        }

        $options[CURLOPT_URL] = $url;
        
        if (!empty(PROXY)) {
            $options[CURLOPT_PROXY] = PROXY;
        }

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
    
        // Check the http response
        $httpCode = $info['http_code'];
        if ($httpCode != 200) {
            throw new UserException("The URL $url returned HTTP error $httpCode");
        }

        curl_close($ch);

        $charset = preg_match('/(?<=\bcharset=)[A-Za-z]*-[a-zA-Z0-9]*/', $info['content_type'], $match)
            ? $match[0]
            : 'utf-8';
        
        // if HTML doc get character encoding

        return (strtolower($charset) == 'utf-8') ? $result : iconv($charset, 'utf-8', $result);
    } // end getUrlContents()

    /**
     * Gets final URL after HTTP redirects
     *
     * @param string $url
     * @return string
     */
    public static function getFinalUrl(string $url): string
    {
        $final_url = $url;
        $ch = curl_init();
    
        while (true) {
            curl_setopt($ch, CURLOPT_URL, $final_url);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $result = curl_exec($ch);
    
            if (preg_match('~Location: (.*)~i', $result, $match)) {
                $final_url = trim($match[1]);
            } else {
                break; // No more redirects
            }
        }
    
        curl_close($ch);
    
        return $final_url;
    }
}
