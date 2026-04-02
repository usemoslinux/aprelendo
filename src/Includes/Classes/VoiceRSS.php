<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

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
    } 

    /**
     * Validates request before sending it to VoiceRSS server
     *
     * @param array $settings
     * @return void
     */
    private function validate($settings): void
    {
        if (empty($settings)) {
            throw new UserException('The settings are undefined');
        }
        if (empty($settings['key'] ?? '')) {
            throw new UserException('The API key is undefined');
        }
        if (empty($settings['src'] ?? '')) {
            throw new UserException('The text is undefined');
        }
        if (empty($settings['hl'] ?? '')) {
            throw new UserException('The language is undefined');
        }
    } 

    /**
     * Requests TTS conversion to VoiceRSS server
     *
     * @param array $settings
     * @return array
     */
    private function request(array $settings): array
    {
        $url = (($settings['ssl'] ?? false) ? 'https' : 'http') . '://api.voicerss.org/';
        
        $curl_options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded; '
                . 'charset=UTF-8; Expect: 100-continue'],
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $this->buildRequest($settings)
        ];

        $resp = Curl::getUrlContents($url, $curl_options);
        $is_error = str_starts_with($resp, 'ERROR');
        
        return [
            'error' => ($is_error) ? $resp : null,
            'response' => (!$is_error) ? $resp: null
        ];
    } 

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
    } 
}
