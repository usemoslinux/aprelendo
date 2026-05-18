<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

use Aprelendo\SupportedLanguages;

class AIBot
{
    private const BASE_URL = 'https://router.huggingface.co/v1/chat/completions';
    private const NUANCE_BLANK = '____';
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
        $this->lang = SupportedLanguages::get($learning_lang_iso, 'name');
        $this->native_lang = SupportedLanguages::get($native_lang_iso, 'name');
    } 

    /**
     * Stream a reply from the AI model based on the given prompt.
     *
     * @param string $prompt The user's input prompt.
     * @return void
     */
    public function streamReply(string $prompt): void
    {
        $STOP = "<END>";

        $data = [
            // "model" => "Qwen/Qwen3-VL-8B-Instruct",
            "model" => "deepseek-ai/DeepSeek-V3.2-Exp",
            // "model" => "google/gemma-4-26B-A4B-it",
            // "model" => "Qwen/Qwen3-VL-30B-A3B-Instruct",
            "provider" => "auto",
            "messages" => [
                [
                    "role" => "system",
                    "content" =>
                        "You are a language tutor. Keep every answer extremely concise: at most 3 sentences or 80 words. "
                        . "If the user asks for examples, give at most 2. End every reply with the marker {$STOP}. "
                        . "Your role is to explain vocabulary, usage, and subtle distinctions in {$this->lang}. "
                        . "Always assume questions refer to {$this->lang}, even if written in English. "
                        . "Write explanations in English, but the vocabulary under analysis must appear in {$this->lang}. "
                        . "The user is a native {$this->native_lang} speaker, so include helpful translations "
                        . "to {$this->native_lang} when relevant."
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ],
            "max_tokens" => 160,  // conservative cap; raise to ~220 only if you see frequent length stops
            "temperature" => 0.1, // low = terse, less rambling
            "top_p" => 0.9,       // optional; keeps sampling stable
            "stop" => [$STOP],    // the model must end with this marker
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
        // curl_close($ch);

        ob_end_flush();
    }

    /**
     * Generates contrastive cloze cards for Nuance Battle.
     *
     * @param array $words
     * @return array
     */
    public function generateNuanceBattleCards(array $words): array
    {
        $words_json = json_encode(array_values($words), JSON_UNESCAPED_UNICODE);
        $system_prompt = "You are a language tutor creating a contrastive cloze exercise in {$this->lang}. "
            . "Return only valid JSON. Do not use Markdown. Do not add commentary. "
            . "The learner is a native {$this->native_lang} speaker.";
        $user_prompt = "Create one card for each target word in this JSON array: {$words_json}.\n"
            . "Every card must test subtle meaning differences between all words in the set.\n"
            . "For each target word, write one natural {$this->lang} sentence where only that target word is "
            . "the clearly best choice from the full set. Replace the target word with exactly "
            . self::NUANCE_BLANK . ".\n"
            . "Avoid generic or short sentences. Include enough context so the answer is fair.\n"
            . "The other words should be plausible near-misses, but less natural.\n"
            . "Return this exact JSON shape: "
            . '{"cards":[{"target_word":"word","sentence":"Sentence with ____ blank.","explanation":"Brief English explanation of why this word fits best."}]}';

        $max_tokens = min(5000, max(1400, count($words) * 220));
        $raw_reply = $this->requestReply($system_prompt, $user_prompt, $max_tokens, 0.2);
        return $this->parseNuanceBattleCards($raw_reply, $words);
    }

    /**
     * Requests a non-streaming reply from the AI model.
     *
     * @param string $system_prompt
     * @param string $user_prompt
     * @param int $max_tokens
     * @param float $temperature
     * @return string
     */
    private function requestReply(
        string $system_prompt,
        string $user_prompt,
        int $max_tokens = 800,
        float $temperature = 0.1
    ): string {
        $data = [
            "model" => "deepseek-ai/DeepSeek-V3.2-Exp",
            "provider" => "auto",
            "messages" => [
                [
                    "role" => "system",
                    "content" => $system_prompt
                ],
                [
                    "role" => "user",
                    "content" => $user_prompt
                ]
            ],
            "max_tokens" => $max_tokens,
            "temperature" => $temperature,
            "top_p" => 0.9,
            "stream" => false
        ];

        $ch = curl_init(self::BASE_URL);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->api_key}",
                "Content-Type: application/json"
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 45
        ]);

        $reply = curl_exec($ch);

        if ($reply === false) {
            curl_close($ch);
            throw new InternalException('Unable to get AI response.');
        }

        curl_close($ch);
        $decoded_reply = json_decode($reply, true);

        if (!isset($decoded_reply['choices'][0]['message']['content'])) {
            throw new InternalException('Malformed AI response.');
        }

        return $decoded_reply['choices'][0]['message']['content'];
    }

    /**
     * Parses and validates Nuance Battle cards returned by Lingobot.
     *
     * @param string $raw_reply
     * @param array $words
     * @return array
     */
    private function parseNuanceBattleCards(string $raw_reply, array $words): array
    {
        $json = trim($raw_reply);
        $json = preg_replace('/^```(?:json)?\s*|\s*```$/u', '', $json) ?? $json;
        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            $json_start = strpos($json, '{');
            $json_end = strrpos($json, '}');

            if ($json_start !== false && $json_end !== false && $json_end > $json_start) {
                $decoded = json_decode(substr($json, $json_start, $json_end - $json_start + 1), true);
            }
        }

        if (!is_array($decoded) || !isset($decoded['cards']) || !is_array($decoded['cards'])) {
            throw new InternalException('Malformed AI response.');
        }

        $word_lookup = array_fill_keys($words, true);
        $cards_by_word = [];

        foreach ($decoded['cards'] as $card) {
            if (!is_array($card)) {
                continue;
            }

            $target_word = mb_strtolower(trim((string)($card['target_word'] ?? '')));
            $sentence = trim((string)($card['sentence'] ?? ''));
            $explanation = trim((string)($card['explanation'] ?? ''));

            if (
                !isset($word_lookup[$target_word])
                || !str_contains($sentence, self::NUANCE_BLANK)
                || $explanation === ''
            ) {
                continue;
            }

            $cards_by_word[$target_word] = [
                'target_word' => $target_word,
                'sentence' => $sentence,
                'explanation' => $explanation
            ];
        }

        if (count($cards_by_word) !== count($words)) {
            throw new InternalException('Incomplete AI response.');
        }

        $cards = [];

        foreach ($words as $word) {
            $cards[] = $cards_by_word[$word];
        }

        return $cards;
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
        return str_starts_with($line, 'data: ');
    }

    /**
     * Check if a line contains an error.
     *
     * @param string $line
     * @return bool
     */
    private function isErrorLine(string $line): bool
    {
        return str_starts_with($line, '{"error":');
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
            echo "Hugging Face Error: " . print_r($decoded['error']); // Send the error message
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
