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

class SharedTexts extends Texts
{
    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id)
    {
        parent::__construct($pdo, $user_id, $lang_id);
        $this->table = 'shared_texts';
    } // end __construct()

    /**
     * Loads text record data
     *
     * @param int $id
     * @return void
     */
    public function loadRecord(int $id): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `id` = ?";
        $row = $this->sqlFetch($sql, [$id]);

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
    * Returns the search results for a text using the parameters chosen by the user
    *
    * @param SearchTextsParameters $search_params
    * @return array
    */
    public function search(SearchTextsParameters $search_params): array
    {
        $sort_sql = $search_params->buildSortSQL();
        $sort_sql = str_replace('`id`', 't.`id`', $sort_sql);
        $filter_type_sql = $search_params->buildFilterTypeSQL();
        $filter_level_sql = $search_params->buildFilterLevelSQL();
        $search_text = '%' . $search_params->search_text . '%';

        $lang = new Language($this->pdo, $this->user_id);
        $lang->loadRecordById($this->lang_id);

        $sql = "SELECT t.id,
                    u.`name` AS `user_name`,
                    t.title,
                    t.author,
                    t.audio_uri,
                    t.source_uri,
                    t.type,
                    t.word_count,
                    CHAR_LENGTH(t.text) AS `char_length`,
                    t.level,
                    tt.`icon_html`,
                    l.name,
                    COALESCE(lc.`total_likes`, 0) AS `total_likes`,
                    CASE WHEN ul.`text_id` IS NULL THEN 0 ELSE 1 END AS `user_liked`
                FROM `{$this->table}` t
                INNER JOIN `users` u ON u.`id` = t.`user_id`
                INNER JOIN `text_types` tt ON tt.`id` = t.`type`
                INNER JOIN `languages` l ON t.lang_id = l.id
                LEFT JOIN (
                    SELECT `text_id`, COUNT(*) AS `total_likes`
                    FROM `likes`
                    GROUP BY `text_id`
                ) lc ON lc.`text_id` = t.`id`
                LEFT JOIN `likes` ul ON ul.`text_id` = t.`id`
                    AND ul.`user_id` = ?
                WHERE l.`name` = ?
                AND t.level $filter_level_sql
                AND t.type $filter_type_sql
                AND t.`title` LIKE ?
                ORDER BY $sort_sql
                LIMIT {$search_params->offset}, {$search_params->limit}";

        return $this->sqlFetchAll($sql, [
            $this->user_id, $lang->name, $search_params->filter_level, $search_params->filter_type, $search_text
        ]);
    } // end search()

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
        $filter_type_sql = $filter_type == 0 ? 'AND t.type>=?' : 'AND t.type=?';
        $filter_level_sql = $filter_level == 0 ? 'AND t.level>=?' : 'AND t.level=?';

        $lang = new Language($this->pdo, $this->user_id);
        $lang->loadRecordById($this->lang_id);
        
        $sql = "SELECT COUNT(t.id) FROM `{$this->table}` t
                INNER JOIN `languages` l ON t.lang_id = l.id
                WHERE l.name= ?
                $filter_level_sql $filter_type_sql AND t.title LIKE ?";

        return $this->sqlCount($sql, [$lang->name, $filter_level, $filter_type, $search_text]);
    } // end countSearchRows()

    /**
     * Checks if text was already exists in database, to avoid duplicate entries.
     * It does this by checking the source url of the text to be added.
     *
     * @param string $source_url
     * @return bool
     */
    public function exists(string $source_url): bool
    {
        if (empty($source_url)) {
            return false;
        }

        $sql = "SELECT COUNT(*) AS `exists`
                FROM `{$this->table}`
                WHERE `source_uri` = ?";

        return $this->sqlCount($sql, [$source_url]) > 0;
    } // end exists()
}
