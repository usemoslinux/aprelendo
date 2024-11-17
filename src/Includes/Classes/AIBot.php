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
     * Get AI reply from Hugging Face
     *
     * @param string $question Question to pose to the AI LLM
     * @throws UserException If there's a problem fetching or parsing the request
     * @return string AI reply
     */
    public function fetchReply(string $prompt): string
    {
        $data = [
            "model" => "microsoft/Phi-3.5-mini-instruct",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are a helpful language tutor for the {$this->lang} language. "
                        . "Your answers should be concise, simple and straightforward."
                ],
                [
                    "role" => "user",
                    "content" => "$prompt"
                ]
            ],
            "max_tokens" => 600,
            "stream" => false
        ];

        $options = [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->api_key}",
                "Content-Type: application/json"
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
        ];

        $ai_reply = Curl::getUrlContents($this::BASE_URL, $options);

        if (!$ai_reply) {
            throw new UserException('Error fetching AI reply.');
        }

        return $ai_reply;
    } // end fetchReply()
}
