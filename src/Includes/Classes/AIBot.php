<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

use Aprelendo\SupportedLanguages;

class AIBot
{
    private const BASE_URL = 'https://router.huggingface.co/v1/chat/completions';
    private const MODEL = 'Qwen/Qwen3.8-27B:novita';
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
            "model" => self::MODEL,
            "chat_template_kwargs" => ["enable_thinking" => false],
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
            "max_tokens" => 512,  // Allow translations while the prompt keeps the answer concise.
            "temperature" => 0.1, // low = terse, less rambling
            "top_p" => 0.9,       // optional; keeps sampling stable
            "stop" => [$STOP],    // the model must end with this marker
            "stream" => true
        ];

        $state = ['content' => false, 'finished' => false, 'finish_reason' => null, 'error' => false];
        $options = [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->api_key}",
                "Content-Type: application/json"
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_WRITEFUNCTION => $this->createWriteFunction($state)
        ];

        ob_start();

        $ch = curl_init(self::BASE_URL);
        curl_setopt_array($ch, $options);
        try {
            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($result === false) {
                throw new UserException(curl_errno($ch) === CURLE_OPERATION_TIMEDOUT
                    ? 'Lingobot timed out. Please try again.' : 'Unable to connect to the AI provider. Please try again.');
            }
            $this->checkResponseStatus($status);
            if ($state['error']) {
                throw new UserException('The AI provider reported an error. Please try again.');
            }
            if ($state['finish_reason'] === 'length') {
                throw new UserException('Lingobot reached its response token limit. Please try a shorter question.');
            }
            if (!$state['content']) {
                throw new UserException('Lingobot returned no answer. The model may have used its token budget on reasoning. Please try again.');
            }
            if (!$state['finished']) {
                throw new UserException('The AI response was interrupted. Please try again.');
            }
            $this->emitStreamEvent(['type' => 'done']);
        } finally {
            curl_close($ch);
            ob_end_flush();
        }
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
            "model" => self::MODEL,
            "chat_template_kwargs" => ["enable_thinking" => false],
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
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 45
        ]);

        $reply = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($reply === false) {
            $timed_out = curl_errno($ch) === CURLE_OPERATION_TIMEDOUT;
            curl_close($ch);
            throw new UserException($timed_out ? 'Lingobot timed out. Please try again.'
                : 'Unable to connect to the AI provider. Please try again.');
        }

        curl_close($ch);
        $this->checkResponseStatus($status);
        $decoded_reply = json_decode($reply, true);

        if (isset($decoded_reply['error'])) {
            throw new UserException('The AI provider reported an error. Please try again.');
        }
        if (($decoded_reply['choices'][0]['finish_reason'] ?? null) === 'length') {
            throw new UserException('Lingobot reached its response token limit. Please try fewer words.');
        }
        if (!is_string($decoded_reply['choices'][0]['message']['content'] ?? null)
            || trim($decoded_reply['choices'][0]['message']['content']) === '') {
            throw new UserException('Lingobot returned an empty or malformed answer. Please try again.');
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
     * Buffer SSE events across arbitrary cURL chunks and track stream completion.
     *
     * @param array $state
     * @return callable
     */
    private function createWriteFunction(array &$state): callable
    {
        $buffer = '';
        $data_lines = [];
        return function ($ch, $chunk) use (&$buffer, &$data_lines, &$state) {
            // HTTP error bodies are not SSE and must not be forwarded as answer text.
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) {
                return strlen($chunk);
            }
            $buffer .= $chunk;
            while (($newline = strpos($buffer, "\n")) !== false) {
                $line = rtrim(substr($buffer, 0, $newline), "\r");
                $buffer = substr($buffer, $newline + 1);
                if ($line === '') {
                    if ($data_lines) {
                        $this->processStreamData(implode("\n", $data_lines), $state);
                        $data_lines = [];
                    }
                } elseif (str_starts_with($line, 'data:')) {
                    $data_lines[] = preg_replace('/^ /', '', substr($line, 5));
                }
            }
            return strlen($chunk);
        };
    }

    /**
     * Forward answer text, keeping reasoning and provider errors out of the answer.
     *
     * @param string $data
     * @param array $state
     * @return void
     */
    private function processStreamData(string $data, array &$state): void
    {
        if ($data === '[DONE]') {
            $state['finished'] = true;
            return;
        }
        $decoded = json_decode($data, true);
        if (!is_array($decoded) || isset($decoded['error'])) {
            $state['error'] = true;
            return;
        }
        $choice = $decoded['choices'][0] ?? [];
        if (isset($choice['finish_reason'])) {
            $state['finish_reason'] = $choice['finish_reason'];
            $state['finished'] = true;
        }
        $content = $choice['delta']['content'] ?? null;
        if (is_string($content) && $content !== '') {
            $state['content'] = $state['content'] || trim($content) !== '';
            $this->emitStreamEvent(['type' => 'delta', 'content' => $content]);
        }
    }

    /**
     * Send a newline-delimited JSON event to the browser.
     *
     * @param array $event
     * @return void
     */
    private function emitStreamEvent(array $event): void
    {
        echo json_encode($event, JSON_INVALID_UTF8_SUBSTITUTE) . "\n";
        $this->flushOutput();
    }

    private function checkResponseStatus(int $status): void
    {
        if ($status >= 200 && $status < 300) {
            return;
        }
        $message = match ($status) {
            401, 403 => 'Check your Hugging Face token and its Inference Providers permission in your profile.',
            402 => 'Your Hugging Face inference credits are exhausted. Check your Hugging Face billing settings.',
            429 => 'The AI provider is rate limiting requests. Please try again shortly.',
            404, 410, 503 => 'The selected AI model or provider is unavailable. Please try again later.',
            default => 'The AI provider could not complete the request. Please try again.'
        };
        throw new UserException("{$message} (HTTP {$status})");
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
