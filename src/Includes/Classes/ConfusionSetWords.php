<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class ConfusionSetWords extends DBEntity
{
    public int $user_id = 0;
    public int $lang_id = 0;

    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id)
    {
        parent::__construct($pdo);
        $this->table = 'confusion_set_words';
        $this->user_id = $user_id;
        $this->lang_id = $lang_id;
    }

    /**
     * Replaces the word membership for a set.
     *
     * @param int $set_id
     * @param array $words
     * @return void
     */
    public function replaceForSet(int $set_id, array $words): void
    {
        $this->addMissingWords($words);

        $sql = "DELETE FROM `{$this->table}` WHERE `confusion_set_id`=?";
        $this->sqlExecute($sql, [$set_id]);

        $word_ids = $this->getWordIds($words);

        if (empty($word_ids)) {
            return;
        }

        $value_placeholders = [];
        $params = [];

        foreach ($word_ids as $word_id) {
            $value_placeholders[] = "(?, ?)";
            $params[] = $set_id;
            $params[] = $word_id;
        }

        $sql = "INSERT INTO `{$this->table}` (`confusion_set_id`, `word_id`)
                VALUES " . implode(', ', $value_placeholders);
        $this->sqlExecute($sql, $params);
    }

    /**
     * Adds submitted words that are not yet in the user's vocabulary list.
     *
     * @param array $words
     * @return void
     */
    private function addMissingWords(array $words): void
    {
        $value_placeholders = [];
        $params = [];

        foreach ($words as $word) {
            $value_placeholders[] = "(?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 1 DAY))";
            $params[] = $this->user_id;
            $params[] = $this->lang_id;
            $params[] = $word;
            $params[] = WordStatus::new_word->value;
            $params[] = (int)$this->isPhrase($word);
        }

        $sql = "INSERT IGNORE INTO `words`
                    (`user_id`, `lang_id`, `word`, `status`, `is_phrase`, `date_next_review`)
                VALUES " . implode(', ', $value_placeholders);
        $this->sqlExecute($sql, $params);
    }

    /**
     * Returns word IDs for the submitted normalized words.
     *
     * @param array $words
     * @return array
     */
    private function getWordIds(array $words): array
    {
        $in_placeholders = str_repeat('?,', count($words) - 1) . '?';
        $params = array_merge([$this->user_id, $this->lang_id], $words);

        $sql = "SELECT `id`
                FROM `words`
                WHERE `user_id`=? AND `lang_id`=? AND `word` IN ($in_placeholders)
                ORDER BY `word` ASC";
        $rows = $this->sqlFetchAll($sql, $params);

        return array_map(static function (array $row): int {
            return (int)$row['id'];
        }, $rows);
    }

    /**
     * Checks if a vocabulary item is a phrase.
     *
     * @param string $word
     * @return bool
     */
    private function isPhrase(string $word): bool
    {
        return preg_match('/\s/u', trim($word)) === 1;
    }
}
