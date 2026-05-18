<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class ConfusionSets extends DBEntity
{
    private const MAX_TITLE_LENGTH = 255;
    private const MAX_WORD_LENGTH = 200;
    private const MAX_WORDS_PER_SET = 50;

    public int $user_id = 0;
    public int $lang_id = 0;
    private ConfusionSetWords $set_words;

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
        $this->table = 'confusion_sets';
        $this->user_id = $user_id;
        $this->lang_id = $lang_id;
        $this->set_words = new ConfusionSetWords($pdo, $user_id, $lang_id);
    }

    /**
     * Returns all confusion sets for the active user and language.
     *
     * @return array
     */
    public function getAll(): array
    {
        $sql = "SELECT cs.`id`, cs.`title`, cs.`created_at`, cs.`date_modified`, w.`word`
                FROM `confusion_sets` cs
                LEFT JOIN `confusion_set_words` csw ON cs.`id` = csw.`confusion_set_id`
                LEFT JOIN `words` w ON csw.`word_id` = w.`id`
                WHERE cs.`user_id`=? AND cs.`lang_id`=?
                ORDER BY cs.`date_modified` DESC, cs.`id` DESC, w.`word` ASC";

        $rows = $this->sqlFetchAll($sql, [$this->user_id, $this->lang_id]);
        $sets = [];

        foreach ($rows as $row) {
            $set_id = (int)$row['id'];

            if (!isset($sets[$set_id])) {
                $sets[$set_id] = [
                    'id' => $set_id,
                    'title' => $row['title'],
                    'created_at' => $row['created_at'],
                    'date_modified' => $row['date_modified'],
                    'words' => []
                ];
            }

            if (!empty($row['word'])) {
                $sets[$set_id]['words'][] = $row['word'];
            }
        }

        return array_values($sets);
    }

    /**
     * Returns one confusion set for the active user and language.
     *
     * @param int $set_id
     * @return array
     */
    public function getById(int $set_id): array
    {
        $this->validateSetId($set_id);

        foreach ($this->getAll() as $set) {
            if ((int)$set['id'] === $set_id) {
                return $set;
            }
        }

        throw new UserException('Set not found.');
    }

    /**
     * Creates a confusion set and saves its words.
     *
     * @param string $title
     * @param array $words
     * @return int
     */
    public function create(string $title, array $words): int
    {
        $title = $this->validateTitle($title);
        $words = $this->validateWords($words);

        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO `confusion_sets` (`user_id`, `lang_id`, `title`) VALUES (?, ?, ?)";
            $this->sqlExecute($sql, [$this->user_id, $this->lang_id, $title]);
            $set_id = (int)$this->pdo->lastInsertId();
            $this->set_words->replaceForSet($set_id, $words);

            $this->pdo->commit();
            return $set_id;
        } catch (\Throwable $e) {
            $this->rollBackTransaction();
            throw $e;
        }
    }

    /**
     * Updates a confusion set and replaces its word membership.
     *
     * @param int $set_id
     * @param string $title
     * @param array $words
     * @return void
     */
    public function update(int $set_id, string $title, array $words): void
    {
        $this->validateSetId($set_id);
        $title = $this->validateTitle($title);
        $words = $this->validateWords($words);

        try {
            $this->pdo->beginTransaction();
            $this->ensureSetBelongsToUser($set_id);

            $sql = "UPDATE `confusion_sets`
                    SET `title`=?
                    WHERE `id`=? AND `user_id`=? AND `lang_id`=?";
            $this->sqlExecute($sql, [$title, $set_id, $this->user_id, $this->lang_id]);
            $this->set_words->replaceForSet($set_id, $words);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->rollBackTransaction();
            throw $e;
        }
    }

    /**
     * Deletes a confusion set.
     *
     * @param int $set_id
     * @return void
     */
    public function delete(int $set_id): void
    {
        $this->validateSetId($set_id);
        $this->ensureSetBelongsToUser($set_id);

        $sql = "DELETE FROM `confusion_sets`
                WHERE `id`=? AND `user_id`=? AND `lang_id`=?";
        $this->sqlExecute($sql, [$set_id, $this->user_id, $this->lang_id]);
    }

    /**
     * Parses submitted word text into a normalized list.
     *
     * @param string $words_text
     * @return array
     */
    public static function parseWordsText(string $words_text): array
    {
        $raw_words = preg_split('/[\r\n,;]+/u', $words_text);

        if ($raw_words === false) {
            return [];
        }

        return array_values(array_filter(array_map('trim', $raw_words), static function (string $word): bool {
            return $word !== '';
        }));
    }

    /**
     * Ensures a set exists for the active user and language.
     *
     * @param int $set_id
     * @return void
     */
    private function ensureSetBelongsToUser(int $set_id): void
    {
        $sql = "SELECT COUNT(*)
                FROM `confusion_sets`
                WHERE `id`=? AND `user_id`=? AND `lang_id`=?";

        if ($this->sqlCount($sql, [$set_id, $this->user_id, $this->lang_id]) < 1) {
            throw new UserException('Set not found.');
        }
    }

    /**
     * Validates and normalizes a set title.
     *
     * @param string $title
     * @return string
     */
    private function validateTitle(string $title): string
    {
        $title = trim($title);

        if ($title === '') {
            throw new UserException('Add a title for this set.');
        }

        if (mb_strlen($title) > self::MAX_TITLE_LENGTH) {
            throw new UserException('The set title is too long.');
        }

        return $title;
    }

    /**
     * Validates and normalizes submitted words.
     *
     * @param array $words
     * @return array
     */
    private function validateWords(array $words): array
    {
        $normalized_words = [];

        foreach ($words as $word) {
            $normalized_word = mb_strtolower(trim((string)$word));

            if ($normalized_word === '') {
                continue;
            }

            if (mb_strlen($normalized_word) > self::MAX_WORD_LENGTH) {
                throw new UserException('One of the words is too long.');
            }

            $normalized_words[$normalized_word] = $normalized_word;
        }

        if (count($normalized_words) < 2) {
            throw new UserException('Add at least two words to compare.');
        }

        if (count($normalized_words) > self::MAX_WORDS_PER_SET) {
            throw new UserException('A set can contain up to 50 words.');
        }

        return array_values($normalized_words);
    }

    /**
     * Validates a set ID.
     *
     * @param int $set_id
     * @return void
     */
    private function validateSetId(int $set_id): void
    {
        if ($set_id <= 0) {
            throw new UserException('Invalid set selection.');
        }
    }

    /**
     * Rolls back an active transaction.
     *
     * @return void
     */
    private function rollBackTransaction(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}
