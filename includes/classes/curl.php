<?php
/**
 * Copyright (C) 2018 Pablo Castagnino
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

trait Curl
{
    private static $proxy = 'www-proxy.mrec.ar:8080';
    // private static $proxy = '';

    /**
     * Gets file contents using curl
     * @param string $url
     * @return string
     */
    public static function get_url_contents(string $url): string {
        $ch = curl_init();
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'https://www.aprelendo.com';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow 301 redirects
        curl_setopt($ch, CURLOPT_PROXY, self::$proxy);
        // $httpCode = curl_getinfo($ch , CURLINFO_HTTP_CODE); // used to check possible errors
        $result = curl_exec($ch);

        curl_close($ch); 

        return $result ? $result : '';
    } // end get_url_contents()
}
