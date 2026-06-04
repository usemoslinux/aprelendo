<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class TextDifficultyClassifier
{
    public const TEXT_LEVEL_BEGINNER = 1;
    public const TEXT_LEVEL_INTERMEDIATE = 2;
    public const TEXT_LEVEL_ADVANCED = 3;

    public const DIFFICULTY_CONFIDENCE_LOW = 1;
    public const DIFFICULTY_CONFIDENCE_MEDIUM = 2;
    public const DIFFICULTY_CONFIDENCE_HIGH = 3;

    public const VERSION = 'v1';

    private const LONG_SENTENCE_TOKEN_COUNT = 25;

    private \PDO $pdo;
    private array $frequency_cache = [];
    private array $max_token_length_cache = [];

    /**
     * Constructor
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Classifies text difficulty using language frequency data.
     *
     * @param string $text
     * @param string $langIso
     * @return array
     */
    public function classify(string $text, string $langIso): array
    {
        $langIso = mb_strtolower(trim($langIso), 'UTF-8');
        $frequency_map = $this->getFrequencyMap($langIso);
        $clean_text = $this->cleanText($text);
        $tokens = $this->tokenize($clean_text, $langIso, $frequency_map);
        $metrics = $this->calculateMetrics($clean_text, $tokens, $langIso, $frequency_map);
        $score = $this->calculateScore($metrics);

        return [
            'level' => $this->scoreToLevel($score),
            'score' => $score,
            'confidence' => $this->calculateConfidence($metrics, $frequency_map),
            'version' => self::VERSION,
            'metrics' => $metrics,
        ];
    }

    /**
     * Cleans input text before tokenization.
     *
     * @param string $text
     * @return string
     */
    private function cleanText(string $text): string
    {
        $xml_text = TextsUtilities::extractFromXML($text);

        if ($xml_text !== false) {
            $text = $xml_text;
        }

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text ?? '');
    }

    /**
     * Tokenizes text while preserving one-letter words.
     *
     * @param string $text
     * @param string $langIso
     * @param array $frequencyMap
     * @return array
     */
    private function tokenize(string $text, string $langIso, array $frequencyMap): array
    {
        if ($text === '') {
            return [];
        }

        if ($this->usesCjkTokenizer($langIso)) {
            return $this->tokenizeCjk($text, $langIso, $frequencyMap);
        }

        preg_match_all('/\p{L}+/u', mb_strtolower($text, 'UTF-8'), $matches);

        return array_values(array_filter($matches[0], fn($token) => $this->isValidToken($token)));
    }

    /**
     * Tokenizes Chinese and Japanese with dictionary longest matching.
     *
     * @param string $text
     * @param string $langIso
     * @param array $frequencyMap
     * @return array
     */
    private function tokenizeCjk(string $text, string $langIso, array $frequencyMap): array
    {
        preg_match_all('/[\p{Han}\p{Hiragana}\p{Katakana}ー]+|[^\p{Han}\p{Hiragana}\p{Katakana}ー]+/u', $text, $parts);

        $tokens = [];

        foreach ($parts[0] as $part) {
            if (preg_match('/[\p{Han}\p{Hiragana}\p{Katakana}ー]/u', $part)) {
                $tokens = array_merge($tokens, $this->tokenizeCjkRun($part, $langIso, $frequencyMap));
                continue;
            }

            preg_match_all('/\p{L}+/u', mb_strtolower($part, 'UTF-8'), $matches);
            foreach ($matches[0] as $token) {
                if ($this->isValidToken($token)) {
                    $tokens[] = $token;
                }
            }
        }

        return $tokens;
    }

    /**
     * Tokenizes one CJK run using longest dictionary matches.
     *
     * @param string $text
     * @param string $langIso
     * @param array $frequencyMap
     * @return array
     */
    private function tokenizeCjkRun(string $text, string $langIso, array $frequencyMap): array
    {
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $tokens = [];
        $position = 0;
        $char_count = count($chars);
        $max_token_length = $this->getMaxTokenLength($langIso, $frequencyMap);

        while ($position < $char_count) {
            $match = '';
            $max_length = min($max_token_length, $char_count - $position);

            for ($length = $max_length; $length > 0; $length--) {
                $candidate = implode('', array_slice($chars, $position, $length));

                if (isset($frequencyMap[$candidate])) {
                    $match = $candidate;
                    break;
                }
            }

            if ($match === '') {
                $match = $chars[$position];
            }

            if ($this->isValidToken($match)) {
                $tokens[] = $match;
            }

            $position += mb_strlen($match, 'UTF-8');
        }

        return $tokens;
    }

    /**
     * Calculates all stored difficulty metrics.
     *
     * @param string $text
     * @param array $tokens
     * @param string $langIso
     * @param array $frequencyMap
     * @return array
     */
    private function calculateMetrics(string $text, array $tokens, string $langIso, array $frequencyMap): array
    {
        $total_tokens = count($tokens);
        $token_counts = array_count_values($tokens);
        $unique_tokens = count($token_counts);

        $known_tokens = 0;
        $coverage_90_tokens = 0;
        $coverage_96_tokens = 0;
        $rare_tokens = 0;
        $rare_types = 0;

        foreach ($tokens as $token) {
            $frequency_index = $frequencyMap[$token] ?? null;

            if ($frequency_index === null) {
                $rare_tokens++;
                continue;
            }

            $known_tokens++;

            if ($frequency_index <= 90) {
                $coverage_90_tokens++;
            }

            if ($frequency_index <= 96) {
                $coverage_96_tokens++;
            }

            if ($frequency_index > 90) {
                $rare_tokens++;
            }
        }

        foreach (array_keys($token_counts) as $token) {
            if (!isset($frequencyMap[$token]) || $frequencyMap[$token] > 90) {
                $rare_types++;
            }
        }

        $sentence_metrics = $this->calculateSentenceMetrics($text, $langIso, $frequencyMap);

        return [
            'total_tokens' => $total_tokens,
            'unique_tokens' => $unique_tokens,
            'known_tokens' => $known_tokens,
            'oov_tokens' => $total_tokens - $known_tokens,
            'coverage_90' => $this->percentage($coverage_90_tokens, $total_tokens),
            'coverage_96' => $this->percentage($coverage_96_tokens, $total_tokens),
            'oov_ratio' => $this->percentage($total_tokens - $known_tokens, $total_tokens),
            'rare_token_ratio' => $this->percentage($rare_tokens, $total_tokens),
            'rare_type_ratio' => $this->percentage($rare_types, $unique_tokens),
            'avg_sentence_length' => $sentence_metrics['avg_sentence_length'],
            'long_sentence_ratio' => $sentence_metrics['long_sentence_ratio'],
        ];
    }

    /**
     * Calculates sentence length metrics.
     *
     * @param string $text
     * @param string $langIso
     * @param array $frequencyMap
     * @return array
     */
    private function calculateSentenceMetrics(string $text, string $langIso, array $frequencyMap): array
    {
        $sentences = preg_split('/[.!?。！？]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $sentence_lengths = [];

        foreach ($sentences as $sentence) {
            $length = count($this->tokenize($sentence, $langIso, $frequencyMap));

            if ($length > 0) {
                $sentence_lengths[] = $length;
            }
        }

        $sentence_count = count($sentence_lengths);

        if ($sentence_count === 0) {
            return [
                'avg_sentence_length' => 0.0,
                'long_sentence_ratio' => 0.0,
            ];
        }

        $long_sentence_count = count(array_filter(
            $sentence_lengths,
            fn($length) => $length > self::LONG_SENTENCE_TOKEN_COUNT
        ));

        return [
            'avg_sentence_length' => round(array_sum($sentence_lengths) / $sentence_count, 1),
            'long_sentence_ratio' => $this->percentage($long_sentence_count, $sentence_count),
        ];
    }

    /**
     * Calculates the final 0-100 difficulty score.
     *
     * @param array $metrics
     * @return int
     */
    private function calculateScore(array $metrics): int
    {
        if ($metrics['total_tokens'] === 0) {
            return 0;
        }

        $lexical_score = 0;
        $lexical_score += max(0, 100 - $metrics['coverage_90']) * 1.2;
        $lexical_score += max(0, 100 - $metrics['coverage_96']) * 1.8;
        $lexical_score += $metrics['oov_ratio'] * 2.5;
        $lexical_score += $metrics['rare_type_ratio'] * 0.4;
        $lexical_score = min(100, $lexical_score);

        $sentence_score = 0;

        if ($metrics['avg_sentence_length'] > 12) {
            $sentence_score += ($metrics['avg_sentence_length'] - 12) * 2;
        }

        $sentence_score += $metrics['long_sentence_ratio'] * 0.8;
        $sentence_score = min(100, $sentence_score);

        return (int)round(($lexical_score * 0.80) + ($sentence_score * 0.20));
    }

    /**
     * Converts score to the stored level.
     *
     * @param int $score
     * @return int
     */
    private function scoreToLevel(int $score): int
    {
        if ($score <= 30) {
            return self::TEXT_LEVEL_BEGINNER;
        }

        if ($score <= 65) {
            return self::TEXT_LEVEL_INTERMEDIATE;
        }

        return self::TEXT_LEVEL_ADVANCED;
    }

    /**
     * Calculates confidence from available data.
     *
     * @param array $metrics
     * @param array $frequencyMap
     * @return int
     */
    private function calculateConfidence(array $metrics, array $frequencyMap): int
    {
        if (empty($frequencyMap) || $metrics['total_tokens'] < 50) {
            return self::DIFFICULTY_CONFIDENCE_LOW;
        }

        if ($metrics['total_tokens'] < 200) {
            return self::DIFFICULTY_CONFIDENCE_MEDIUM;
        }

        return self::DIFFICULTY_CONFIDENCE_HIGH;
    }

    /**
     * Loads the language frequency list once per script execution.
     *
     * @param string $langIso
     * @return array
     */
    private function getFrequencyMap(string $langIso): array
    {
        if (isset($this->frequency_cache[$langIso])) {
            return $this->frequency_cache[$langIso];
        }

        $stmt = $this->pdo->prepare("
            SELECT `word`, `frequency_index`
            FROM `frequency_lists`
            WHERE `lang_iso` = ?
        ");

        $stmt->execute([$langIso]);

        $map = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $map[mb_strtolower($row['word'], 'UTF-8')] = (int)$row['frequency_index'];
        }

        $this->frequency_cache[$langIso] = $map;

        return $map;
    }

    /**
     * Returns the longest frequency-list token for CJK dictionary matching.
     *
     * @param string $langIso
     * @param array $frequencyMap
     * @return int
     */
    private function getMaxTokenLength(string $langIso, array $frequencyMap): int
    {
        if (isset($this->max_token_length_cache[$langIso])) {
            return $this->max_token_length_cache[$langIso];
        }

        $max_length = 1;

        foreach (array_keys($frequencyMap) as $word) {
            $max_length = max($max_length, mb_strlen($word, 'UTF-8'));
        }

        $this->max_token_length_cache[$langIso] = $max_length;

        return $max_length;
    }

    /**
     * Checks whether a token is usable for difficulty metrics.
     *
     * @param string $token
     * @return bool
     */
    private function isValidToken(string $token): bool
    {
        if ($token === '') {
            return false;
        }

        if (preg_match('/\d/u', $token)) {
            return false;
        }

        return true;
    }

    /**
     * Returns whether the language needs CJK tokenization.
     *
     * @param string $langIso
     * @return bool
     */
    private function usesCjkTokenizer(string $langIso): bool
    {
        return in_array($langIso, ['zh', 'ja'], true);
    }

    /**
     * Calculates a rounded percentage.
     *
     * @param int $part
     * @param int $total
     * @return float
     */
    private function percentage(int $part, int $total): float
    {
        if ($total === 0) {
            return 0.0;
        }

        return round(($part / $total) * 100, 1);
    }
}
