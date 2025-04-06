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
    private $native_lang = '';

    /**
     * Constructor
     */
    public function __construct(string $api_key, string $learning_lang_iso, string $native_lang_iso)
    {
        $crypto = new SecureEncryption(ENCRYPTION_KEY);
        $this->api_key = $crypto->decrypt($api_key);
        $this->lang = Language::getNameFromIso($learning_lang_iso);
        $this->native_lang = Language::getNameFromIso($native_lang_iso);
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
            "model" => "google/madlad400-8b-lm",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are a {$this->lang} language tutor. Provide clear, concise explanations and "
                        . "guidance in English. The user is a native {$this->native_lang} speaker, so include relevant "
                        . "comparisons to {$this->native_lang} when helpful. Focus on practical examples and avoid "
                        . "unnecessary complexity. Respond in plain text only."
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ],
            "max_tokens" => 500,
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
            CURLOPT_WRITEFUNCTION => $this->createWriteFunction()
        ];

        ob_start();

        $ch = curl_init(self::BASE_URL);
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        curl_close($ch);

        ob_end_flush();
    }

    /**
     * Create a reusable write function for handling API responses.
     *
     * @return callable
     */
    private function createWriteFunction(): callable
    {
        return function ($ch, $chunk) {
            $lines = explode("\n", $chunk);
            foreach ($lines as $line) {
                if ($this->isDataLine($line)) {
                    $this->processDataLine($line);
                } elseif ($this->isErrorLine($line)) {
                    $this->processErrorLine($line);
                    return strlen($chunk); // Stop further processing
                }
            }
            return strlen($chunk);
        };
    }

    /**
     * Check if a line contains data.
     *
     * @param string $line
     * @return bool
     */
    private function isDataLine(string $line): bool
    {
        return strpos($line, 'data: ') === 0;
    }

    /**
     * Check if a line contains an error.
     *
     * @param string $line
     * @return bool
     */
    private function isErrorLine(string $line): bool
    {
        return strpos($line, '{"error":') === 0;
    }

    /**
     * Process a data line and flush the content.
     *
     * @param string $line
     * @return void
     */
    private function processDataLine(string $line): void
    {
        $json = substr($line, 6);
        $decoded = json_decode($json, true);

        if (isset($decoded['choices'][0]['delta']['content'])) {
            echo $decoded['choices'][0]['delta']['content']; // Send only the content
            $this->flushOutput();
        }
    }

    /**
     * Process an error line and flush the error message.
     *
     * @param string $line
     * @return void
     */
    private function processErrorLine(string $line): void
    {
        $decoded = json_decode($line, true);
        if (isset($decoded['error'])) {
            echo "Hugging Face Error: " . $decoded['error']; // Send the error message
            $this->flushOutput();
        }
    }

    /**
     * Flush the output buffer.
     *
     * @return void
     */
    private function flushOutput(): void
    {
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }
}
