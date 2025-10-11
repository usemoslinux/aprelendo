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

class Texts extends DBEntity
{
    public $id            = 0;
    public $user_id       = 0;
    public $lang_id       = 0;
    public $title         = '';
    public $author        = '';
    public $text          = '';
    public $audio_uri     = '';
    public $source_uri    = '';
    public $type          = 0;
    public $word_count    = 0;
    public $level         = 0;
    public $date_created  = '';
    public $text_pos      = '';
    public $audio_pos     = '';
    
    /**
    * Constructor
    *
    * Sets 3 basic variables used to identify any text: $pdo, $user_id & lang_id
    *
    * @param \PDO $pdo
    * @param int $user_id
    * @param int $lang_id
    */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id)
    {
        parent::__construct($pdo);
        $this->table = 'texts';
        $this->user_id = $user_id;
        $this->lang_id = $lang_id;
    } // end __construct()

    /**
     * Loads text record data
     *
     * @param int $id
     * @return void
     */
    public function loadRecord(int $id): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `id` = ? AND `user_id` = ?";
        $row = $this->sqlFetch($sql, [$id, $this->user_id]);

        if ($row) {
            $this->id            = $row['id'];
            $this->user_id       = $row['user_id'];
            $this->lang_id       = $row['lang_id'];
            $this->title         = $row['title'];
            $this->author        = $row['author'];
            $this->text          = $row['text'];
            $this->audio_uri     = $row['audio_uri'] ?? '';
            $this->source_uri    = $row['source_uri'] ?? '';
            $this->type          = $row['type'];
            $this->word_count    = $row['word_count'];
            $this->level         = $row['level'];
            $this->date_created  = $row['date_created'];
            $this->text_pos      = $row['text_pos'] ?? '';
            $this->audio_pos     = $row['audio_pos'] ?? '';
        }
    } // end loadRecord()
        
    /**
    * Adds a new text to the database
    *
    * @param string $title
    * @param string $author
    * @param string $text
    * @param string $source_url
    * @param string $audio_url
    * @param int $type
    * @return int
    */
    public function add(
        string $title,
        string $author,
        string $text,
        string $source_url,
        string $audio_url,
        int $type,
        int $level
        ): int {

        // get language iso
        $lang = new Language($this->pdo, $this->user_id);
        $lang->loadRecordById($this->lang_id);
        $lang_iso = $lang->name;

        // if $text is XML code (video transcript), extract text from XML string
        $xml_text = TextsUtilities::extractFromXML($text);

        // count words in text
        $word_count = ($xml_text !== false)
            ? preg_match_all('/\w+/u', $xml_text, $words)
            : preg_match_all('/\w+/u', $text, $words);

        // fix string casings before saving
        if (mb_strtoupper($title, 'UTF-8') == $title) {
            // don't allow all upper case titles
            $title =  mb_strtoupper(mb_substr($title, 0, 1)) . mb_strtolower(mb_substr($title, 1));
        }

        $author = TextsUtilities::formatAuthorCase($author);
        
        // add text to table
        $sql = "INSERT INTO `{$this->table}` (`user_id`, `lang_id`, `title`, `author`,
                    `text`, `audio_uri`, `source_uri`, `type`, `word_count`, `level`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->sqlExecute($sql, [$this->user_id,$this->lang_id, $title, $author, $text, $audio_url,
        $source_url, $type, $word_count, $level]);

        $insert_id = $this->pdo->lastInsertId();

        // add entry to popularsources
        $pop_sources = new PopularSources($this->pdo);
        $pop_sources->add($lang_iso, Url::getDomainName($source_url));

        return $insert_id;
    } // end add()

    /**
    * Updates existing text in database
    *
    * @param int $id
    * @param array $columns
    * @return void
    */
    public function update(int $id, array $columns): void
    {
        // create sql string to use with all the columns to update
        $sql = "";

        foreach ($columns as $key => $value) {
            $sql .= empty($sql) ? "`$key`=?" : ", `$key`=?";
        }

        $sql = "UPDATE `{$this->table}` SET $sql WHERE `id`=?";
        $params = array_values($columns);
        $params[] = $id; // add $id last
        $this->sqlExecute($sql, $params);
    } // end update()
    
    /**
    * Deletes texts in database using ids as a parameter to select them
    *
    * @param array $text_ids
    * @return void
    */
    public function delete(array $text_ids): void
    {
        $id_params = str_repeat("?,", count($text_ids)-1) . "?";
    
        $sql =  "SELECT `source_uri` FROM `{$this->table}` WHERE `id` IN ($id_params)";
        $uris = $this->sqlFetchAll($sql, $text_ids);
        
        // delete entries from db
        $sql =  "DELETE FROM `{$this->table}` WHERE `id` IN ($id_params)";
        $this->sqlExecute($sql, $text_ids);

        // delete audio (mp3, oggs) & source files (epubs, etc.)
        $pop_sources = new PopularSources($this->pdo);
        $lang = new Language($this->pdo, $this->user_id);
        $lang->loadRecordById($this->lang_id);
        
        // delete associated file
        foreach ($uris as $uri) {
            if (!empty($uri['source_uri']) && (strpos($uri['source_uri'], '.epub') !== false)) {
                $file = new File($uri['source_uri']);
                $file->delete();
            }
            
            $pop_sources->update($lang->name, Url::getDomainName($uri['source_uri']));
        }
    } // end delete()
    
    /**
    * Archives texts in database using ids as a parameter to select them
    *
    * @param array $text_ids
    * @return void
    */
    public function archive(array $text_ids): void
    {
        if (empty($text_ids)) throw new InternalException("Text id is empty");

        $id_params = str_repeat("?,", count($text_ids)-1) . "?";

        $sql =  "INSERT INTO `archived_texts` SELECT * FROM `{$this->table}` WHERE `id` IN ($id_params)";
        $this->sqlExecute($sql, $text_ids);

        $sql = "DELETE FROM `{$this->table}` WHERE `id` IN ($id_params)";
        $this->sqlExecute($sql, $text_ids);
    } // end archive()

    /**
    * Shares texts in database using ids as a parameter to select them
    *
    * @param int $text_id
    * @return void
    */
    public function share(int $text_id): void
    {
        if (empty($text_id)) throw new InternalException("Text id is empty");

        // check if source_uri already exists in shared_texts table to avoid duplicates
        $shared_texts = new SharedTexts($this->pdo, $this->user_id, $this->lang_id);
        $this->loadRecord($text_id);
        if ($shared_texts->exists($this->source_uri))
                throw new UserException('Another text with the same source URL is already listed as shared.');

        // columns shared by both tables, in the exact order they appear in `shared_texts`
        $cols = [
            'user_id', 'lang_id', 'title', 'author', 'text', 'audio_uri', 'source_uri', 'type',
            'word_count', 'level', 'date_created'
        ];
        $cols_sql = implode(', ', array_map(fn($c) => "`$c`", $cols));

        $sql = "INSERT INTO `shared_texts` ($cols_sql)
                SELECT $cols_sql
                FROM `{$this->table}`
                WHERE `id` = $text_id";
        $this->sqlExecute($sql);

        $sql = "DELETE FROM `{$this->table}` WHERE `id` = $text_id";
        $this->sqlExecute($sql);
    } // end share()

    /**
     * Checks if text already exists in database, to avoid duplicate entries.
     * It does this by checking the source url of the text to be added.
     *
     * @param string $source_url
     * @return boolean
     */
    public function exists(string $source_url): bool
    {
        if (empty($source_url)) {
            return false;
        }

        // check if source_url exists in 'texts' or 'archived_texts' table
        $sql = "SELECT
                (SELECT COUNT(*) FROM `{$this->table}` WHERE `user_id` = ? AND `source_uri` = ?) +
                (SELECT COUNT(*) FROM `archived_texts` WHERE `user_id` = ? AND `source_uri` = ?)
                AS SumCount";
        return $this->sqlCount($sql, [$this->user_id, $source_url, $this->user_id, $source_url]);
    } // end exists()
    
    /**
    * Counts the number of rows (i.e. texts) for a specific search
    *
    * Used for pagination
    *
    * @param string $filter_type A string with the SQL statement to be used as a filter for the search
    * @param string $search_text
    * @return int
    */
    public function countSearchRows(int $filter_type, int $filter_level, string $search_text): int
    {
        $search_text = '%' . $search_text . '%';
        $filter_type_sql = $filter_type == 0 ? 'AND `type`>=?' : 'AND `type`=?';
        $filter_level_sql = $filter_level == 0 ? 'AND `level`>=?' : 'AND `level`=?';
        
        $sql = "SELECT COUNT(`id`) FROM `{$this->table}`
                WHERE `user_id`=?
                AND `lang_id`=? $filter_level_sql $filter_type_sql AND `title` LIKE ?";

        return $this->sqlCount($sql, [$this->user_id, $this->lang_id, $filter_level, $filter_type, $search_text]);
    } // end countSearchRows()
    
    /**
    * Returns the search results for a text using the parameters chosen by the user
    *
    * @param SearchTextsParameters $search_params
    * @return array
    */
    public function search(SearchTextsParameters $search_params): array
    {
        $sort_sql = $search_params->buildSortSQL();
        $filter_type_sql = $search_params->buildFilterTypeSQL();
        $filter_level_sql = $search_params->buildFilterLevelSQL();
        $search_text = '%' . $search_params->search_text . '%';
        
        $sql = "SELECT `id`,
                        NULL,
                        `title`,
                        `author`,
                        `audio_uri`,
                        `source_uri`,
                        `type`,
                        `word_count`,
                        CHAR_LENGTH(`text`) AS `char_length`,
                        `level`
                FROM `{$this->table}`
                WHERE `user_id` = ?
                AND `lang_id` = ?
                AND `level` $filter_level_sql
                AND `type` $filter_type_sql
                AND `title` LIKE ?
                ORDER BY $sort_sql
                LIMIT {$search_params->offset}, {$search_params->limit}";

        return $this->sqlFetchAll($sql, [
            $this->user_id, $this->lang_id, $search_params->filter_level, $search_params->filter_type, $search_text
        ]);
    } // end search()
}
