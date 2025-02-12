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
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_REFERER => $referer,
                CURLOPT_USERAGENT => MOCK_USER_AGENT,
                CURLOPT_ENCODING => "", // Enable automatic decompression
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

        // convert to utf-8 if necessary
        $charset = 'utf-8';
        if (isset($info['content_type']) && preg_match('/charset=([^;]+)/i', $info['content_type'], $match)) {
            $charset = trim($match[1]);
        }
    
        return (strtolower($charset) === 'utf-8') ? $result : iconv($charset, 'utf-8', $result);
    } // end getUrlContents()

    /**
     * Gets final URL after HTTP redirects
     *
     * @param string $url
     * @return string
     */
    public static function getFinalUrl(string $url): string
    {
        $ch = curl_init();
    
        // Basic cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => MOCK_USER_AGENT,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,  // Only allow HTTP and HTTPS protocols
        ]);
    
        $final_url = $url;
        $redirect_count = 0;
        $max_redirects = 10;
    
        while ($redirect_count < $max_redirects) {
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
            // Check for cURL errors
            if ($response === false) {
                throw new UserException('The URL ' . $url  . ' returned the following error: ' . curl_error($ch));
            }
    
            // Only follow 301, 302, 303, 307, and 308 redirects
            if (!in_array($http_code, [301, 302, 303, 307, 308])) {
                break;
            }
    
            // Extract Location header
            if (preg_match('/^Location:\s*(.+)$/mi', $response, $matches)) {
                $next_url = trim($matches[1]);
                
                // Handle relative URLs
                if (parse_url($next_url, PHP_URL_SCHEME) === null) {
                    $parsed_url = parse_url($final_url);
                    if (strpos($next_url, '/') === 0) {
                        $next_url = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $next_url;
                    } else {
                        $base_url = dirname($final_url);
                        $next_url = $base_url . '/' . $next_url;
                    }
                }
    
                // Validate protocol
                $scheme = parse_url($next_url, PHP_URL_SCHEME);
                if (!in_array($scheme, ['http', 'https'])) {
                    break;
                }
    
                $final_url = $next_url;
                curl_setopt($ch, CURLOPT_URL, $final_url);
                $redirect_count++;
            } else {
                break;
            }
        }
    
        curl_close($ch);
        return $final_url;
    }
}
