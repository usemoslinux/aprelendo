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

use Aprelendo\Language;

class AIBot
{
    private const BASE_URL = 'https://api-inference.huggingface.co/models/microsoft/Phi-3.5-mini-instruct/v1/chat/completions';
    private $api_key = '';
    private $lang = '';

    /**
     * Constructor
     */
    public function __construct(string $api_key, string $learning_lang_iso)
    {
        $crypto = new SecureEncryption(ENCRYPTION_KEY);
        $this->api_key = $crypto->decrypt($api_key);
        $this->lang = Language::getNameFromIso($learning_lang_iso);
    } // end __construct()

    /**
     * Stream a reply from the AI model based on the given prompt.
     *
     * @param string $prompt The user's input prompt.
     * @return void
     */
    public function streamReply(string $prompt): void
    {
        $data = [
            "model" => "microsoft/Phi-3.5-mini-instruct",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are a helpful language tutor for the {$this->lang} language. "
                        . "Your answers should be concise, simple, and straightforward."
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ],
            "max_tokens" => 600,
            "stream" => true
        ];

        $options = [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->api_key}",
                "Content-Type: application/json"
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_WRITEFUNCTION => function ($ch, $chunk) {
                $lines = explode("\n", $chunk);
                foreach ($lines as $line) {
                    if (strpos($line, 'data: ') === 0) {
                        $json = substr($line, 6);
                        $decoded = json_decode($json, true);

                        if (isset($decoded['choices'][0]['delta']['content'])) {
                            echo $decoded['choices'][0]['delta']['content']; // Send only the content
                            if (ob_get_level() > 0) {
                                ob_flush();
                            }
                            flush();
                        }
                    }
                }
                return strlen($chunk);
            }
        ];

        ob_start();

        $ch = curl_init(self::BASE_URL);
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        curl_close($ch);

        ob_end_flush();
    }
}
