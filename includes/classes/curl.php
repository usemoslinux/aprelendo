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

require_once '../config/config.php';

class Curl
{
    /**
     * Gets file contents using curl
     * @param string $url
     * @return string
     */
    public static function getUrlContents(string $url, array $options = []): string {
        $ch = curl_init();
        
        if (!isset($options) || empty($options)) {
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'https://www.aprelendo.com';
            $options = array(CURLOPT_RETURNTRANSFER => 1,
                             CURLOPT_CONNECTTIMEOUT => 5,
                             CURLOPT_REFERER => $referer,
                             CURLOPT_SSL_VERIFYPEER => false,
                             CURLOPT_SSL_VERIFYHOST => false,
                             CURLOPT_FOLLOWLOCATION => true
                            );
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
            throw new \Exception("Oops! The URL $url returned HTTP error $httpCode");
        }

        curl_close($ch); 

        return $result ? $result : '';
    } // end getUrlContents()

}
