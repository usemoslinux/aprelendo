<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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
    public $is_archived   = false;
    protected $archive_filter = null;
    
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
    } 

    /**
     * Sets the archive-state filter used by archive-aware queries.
     *
     * @param ?bool $is_archived
     * @return void
     */
    public function setArchiveFilter(?bool $is_archived): void
    {
        $this->archive_filter = $is_archived;
    }

    /**
     * Builds the SQL fragment used to filter by archive state.
     *
     * @param string $column_name
     * @return string
     */
    protected function getArchiveFilterSql(string $column_name = '`is_archived`'): string
    {
        if ($this->archive_filter === null) {
            return '';
        }

        return " AND {$column_name} = ?";
    }

    /**
     * Returns the parameter list used by the archive-state filter.
     *
     * @return array
     */
    protected function getArchiveFilterParams(): array
    {
        if ($this->archive_filter === null) {
            return [];
        }

        return [(int)$this->archive_filter];
    }

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
            $this->is_archived   = !empty($row['is_archived']);
        }
    } 
        
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
    } 

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

        $sql = "UPDATE `{$this->table}` SET $sql WHERE `id`=? AND `user_id`=?";
        $params = array_values($columns);
        $params[] = $id;
        $params[] = $this->user_id;
        $this->sqlExecute($sql, $params);
    } 
    
    /**
    * Deletes texts in database using ids as a parameter to select them
    *
    * @param array $text_ids
    * @return void
    */
    public function delete(array $text_ids): void
    {
        $id_params = str_repeat("?,", count($text_ids)-1) . "?";

        $archive_filter_sql = $this->getArchiveFilterSql();
        $params = array_merge($text_ids, [$this->user_id], $this->getArchiveFilterParams());

        $sql =  "SELECT `source_uri` FROM `{$this->table}` WHERE `id` IN ($id_params) AND `user_id`=?{$archive_filter_sql}";
        $uris = $this->sqlFetchAll($sql, $params);
        
        // delete entries from db
        $sql =  "DELETE FROM `{$this->table}` WHERE `id` IN ($id_params) AND `user_id`=?{$archive_filter_sql}";
        $this->sqlExecute($sql, $params);

        // delete audio (mp3, oggs) & source files (epubs, etc.)
        $pop_sources = new PopularSources($this->pdo);
        $lang = new Language($this->pdo, $this->user_id);
        $lang->loadRecordById($this->lang_id);
        
        // delete associated file
        foreach ($uris as $uri) {
            if (!empty($uri['source_uri']) && str_ends_with(strtolower($uri['source_uri']), '.epub')) {
                $file = new File($uri['source_uri']);
                $file->delete();
            }
            
            $pop_sources->update($lang->name, Url::getDomainName($uri['source_uri']));
        }
    } 
    
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
        $params = array_merge($text_ids, [$this->user_id]);

        $sql = "UPDATE `{$this->table}` SET `is_archived` = 1
                WHERE `id` IN ($id_params) AND `user_id`=? AND `is_archived` = 0";

        try {
            $this->sqlExecute($sql, $params);
        } catch (\Throwable $throwable) {
            throw new InternalException('Could not archive texts.');
        }
    } 

    /**
    * Unarchives texts in database using ids as a parameter to select them
    *
    * @param array $text_ids
    * @return void
    */
    public function unarchive(array $text_ids): void
    {
        if (empty($text_ids)) throw new InternalException("Text id is empty");

        $id_params = str_repeat("?,", count($text_ids)-1) . "?";
        $params = array_merge($text_ids, [$this->user_id]);

        $sql = "UPDATE `{$this->table}` SET `is_archived` = 0
                WHERE `id` IN ($id_params) AND `user_id`=? AND `is_archived` = 1";

        try {
            $this->sqlExecute($sql, $params);
        } catch (\Throwable $throwable) {
            throw new InternalException('Could not unarchive texts.');
        }
    } 

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

        $sql_insert = "INSERT INTO `shared_texts` ($cols_sql)
                SELECT $cols_sql
                FROM `{$this->table}`
                WHERE `id` = ? AND `user_id` = ?";
        $sql_delete = "DELETE FROM `{$this->table}` WHERE `id` = ? AND `user_id` = ?";
        $params = [$text_id, $this->user_id];

        try {
            $this->pdo->beginTransaction();
            $this->sqlExecute($sql_insert, $params);
            $this->sqlExecute($sql_delete, $params);
            $this->pdo->commit();
        } catch (\Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw new InternalException('Could not share text.');
        }
    } 

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

        $sql = "SELECT COUNT(*) FROM `texts` WHERE `user_id` = ? AND `source_uri` = ?";
        return $this->sqlCount($sql, [$this->user_id, $source_url]) > 0;
    } 
    
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
        $archive_filter_sql = $this->getArchiveFilterSql();
        
        $sql = "SELECT COUNT(`id`) FROM `{$this->table}`
                WHERE `user_id`=?
                AND `lang_id`=? $filter_level_sql $filter_type_sql AND `title` LIKE ?{$archive_filter_sql}";

        return $this->sqlCount($sql, array_merge(
            [$this->user_id, $this->lang_id, $filter_level, $filter_type, $search_text],
            $this->getArchiveFilterParams()
        ));
    } 
    
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
        $archive_filter_sql = $this->getArchiveFilterSql('texts.`is_archived`');
        
        $sql = "SELECT texts.`id`,
                        NULL,
                        texts.`title`,
                        texts.`author`,
                        texts.`audio_uri`,
                        texts.`source_uri`,
                        texts.`type`,
                        texts.`word_count`,
                        CHAR_LENGTH(texts.`text`) AS `char_length`,
                        texts.`level`,
                        text_types.`icon_html`
                FROM `{$this->table}` texts
                INNER JOIN `text_types` text_types
                    ON text_types.`id` = texts.`type`
                WHERE texts.`user_id` = ?
                AND texts.`lang_id` = ?
                AND texts.`level` $filter_level_sql
                AND texts.`type` $filter_type_sql
                AND texts.`title` LIKE ?{$archive_filter_sql}
                ORDER BY $sort_sql
                LIMIT {$search_params->offset}, {$search_params->limit}";

        return $this->sqlFetchAll($sql, array_merge(
            [$this->user_id, $this->lang_id, $search_params->filter_level, $search_params->filter_type, $search_text],
            $this->getArchiveFilterParams()
        ));
    } 
}
