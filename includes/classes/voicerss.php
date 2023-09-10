<?php
/**
 * Copyright (C) 2019 VoiceRSS (http://www.voicerss.org/sdk)
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

use Aprelendo\Includes\Classes\Curl;
use Aprelendo\Includes\Classes\UserException;

class VoiceRSS
{
    /**
     * Converts text to speech
     *
     * @param array $settings
     * @return array
     */
    public function speech(array $settings): array
    {
        $this->validate($settings);
        return $this->request($settings);
    } // end speech()

    /**
     * Validates request before sending it to VoiceRSS server
     *
     * @param array $settings
     * @return void
     */
    private function validate($settings): void
    {
        if (!isset($settings) || empty($settings)) {
            throw new UserException('The settings are undefined');
        }
        if (!isset($settings['key']) || empty($settings['key'])) {
            throw new UserException('The API key is undefined');
        }
        if (!isset($settings['src']) || empty($settings['src'])) {
            throw new UserException('The text is undefined');
        }
        if (!isset($settings['hl']) || empty($settings['hl'])) {
            throw new UserException('The language is undefined');
        }
    } // end validate()

    /**
     * Requests TTS conversion to VoiceRSS server
     *
     * @param array $settings
     * @return array
     */
    private function request(array $settings): array
    {
        $url = ((isset($settings['ssl']) && $settings['ssl']) ? 'https' : 'http') . '://api.voicerss.org/';
        
        $curl_options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded; '
                . 'charset=UTF-8; Expect: 100-continue'],
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $this->buildRequest($settings)
        ];

        $resp = Curl::getUrlContents($url, $curl_options);
        $is_error = strpos($resp, 'ERROR') === 0;
        
        return [
            'error' => ($is_error) ? $resp : null,
            'response' => (!$is_error) ? $resp: null
        ];
    } // end request()

    /**
     * Builds CURL request based on $settings array
     *
     * @param array $settings
     * @return string
     */
    private function buildRequest(array $settings): string
    {
        return http_build_query(
            [
                'key' => $settings['key'] ?? '',
                'src' => $settings['src'] ?? '',
                'hl' => $settings['hl'] ?? '',
                'r' => $settings['r'] ?? '',
                'c' => $settings['c'] ?? '',
                'f' => $settings['f'] ?? '',
                'ssml' => $settings['ssml'] ?? '',
                'b64' => $settings['b64'] ?? ''
            ]
        );
    } // end buildRequest()
}
