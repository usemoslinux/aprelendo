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

use Aprelendo\DBEntity;
use Aprelendo\User;
use Aprelendo\Texts;
use Aprelendo\Language;
use Aprelendo\UserException;

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
                ON DUPLICATE KEY UPDATE `sentence` = ?";
        
        $this->sqlExecute($sql, [
            $this->user_id, $lang->name, $word, $sentence,
            $source_title, $source_author, $source_uri, $sentence
        ]);
    } // end addRecord()
}
