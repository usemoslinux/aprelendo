<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class ExampleSentences extends DBEntity
{
    public int $id                      = 0;
    public int $user_id                 = 0;
    public string $source_title         = '';
    public string $source_author        = '';
    public string $source_uri           = '';
    public string $word                 = '';
    public string $sentence             = '';

    /**
     * Constructor
     * Initializes class variables (id, name, etc.)
     *
     * @param \PDO $pdo
     * @param int $id
     * @param int $user_id
     */
    public function __construct(\PDO $pdo, int $user_id)
    {
        parent::__construct($pdo);
        $this->table   = 'example_sentences';
        $this->user_id = $user_id;
    } // end __construct()

    /**
     * Loads record data in object properties by id
     *
     * @param array $new_record
     * @return void
     */
    public function addRecord(array $record): void
    {
        $word = $record['word'];
        $sentence = $record['sentence'];
        $source_id = $record['source_id'];
        $source_title = $source_author = $source_uri = '';

        $user = new User($this->pdo);
        $user->loadRecordById($this->user_id);

        $lang = new Language($this->pdo, $this->user_id);
        $lang->loadRecordById($user->lang_id);

        if (!empty($source_id)) {
            $text = $record['source_table'] === 'texts'
                ? new Texts($this->pdo, $this->user_id, $user->lang_id)
                : new SharedTexts($this->pdo, $this->user_id, $user->lang_id);

            $text->loadRecord($source_id);
            $source_title = $text->title;
            $source_author = $text->author;
            $source_uri = $text->source_uri;
        }

        $sql = "INSERT INTO `{$this->table}`
                (`user_id`, `lang_iso`, `word`, `sentence`, `source_title`, `source_author`, `source_uri`)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE `sentence` = ?, `source_title` = ?, `source_author` = ?, `source_uri` = ?";
        
        $this->sqlExecute($sql, [
            $this->user_id, $lang->name, $word, $sentence, $source_title, $source_author, $source_uri,
                $sentence, $source_title, $source_author, $source_uri
        ]);
    } // end addRecord()
}
