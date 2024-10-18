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

class Words extends DBEntity
{
    public $id              = 0;
    public $user_id         = 0;
    public $lang_id         = 0;
    public $word            = '';
    public $status          = 0;
    public $is_phrase       = false;
    public $date_created    = '';
    public $date_modified   = '';
    public $review_interval = 1;
    public $easiness        = 2.5;
    public $repetitions     = 0;

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
        $this->table = 'words';
        $this->user_id = $user_id;
        $this->lang_id = $lang_id;
    } // end __construct()

    /**
     * Adds a new wod to the database
     *
     * @param string $word
     * @param int $status
     * @param int $is_phrase It's an integer but it acts like a boolean (only uses 0 & 1)
     * @return void
     */
    public function add(string $word, int $status, bool $is_phrase): void
    {
        $word = mb_strtolower($word);
        
        $sql = "INSERT INTO `{$this->table}` (`user_id`, `lang_id`, `word`, `status`, `is_phrase`)
                VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
                `user_id`=?, `lang_id`=?, `word`=?, `status`=?, `date_modified`=NOW()";

        $this->sqlExecute($sql, [
            $this->user_id, $this->lang_id, $word, $status, (int)$is_phrase,
            $this->user_id, $this->lang_id, $word, $status
        ]);
    } // end add()

    /**
     * Updates status of existing words in database
     *
     * @param array $words array containing all the words to update
     * @return void
     */
    public function updateByName(array $words): void
    {
        $in  = str_repeat('?,', count($words) - 1) . '?';

        $sql = "UPDATE `{$this->table}`
                SET `date_modified`=NOW(), `status`=CASE WHEN `status` > 0 THEN `status`-1 ELSE 0 END
                WHERE `user_id`=? AND `lang_id`=? AND `word` IN ($in)";
        
        $this->sqlExecute($sql, array_merge([$this->user_id, $this->lang_id], $words));
    } // end updateByName()

    /**
     * Updates status of existing words in database
     *
     * @param array $words array containing all the words to update
     * @return void
     */
    public function updateStatus(string $word, int $status): void
    {
        $sql = "UPDATE `{$this->table}` SET `status`=$status, `date_modified`=NOW()
                WHERE `user_id`=? AND `lang_id`=? AND `word`=?";

        $this->sqlExecute($sql, [$this->user_id, $this->lang_id, $word]);
    } // end updateByName()

    /**
     * Deletes 1 word in database using word (not the id, the actual word) as a parameter to select it
     *
     * @param string $word
     * @return void
     */
    public function deleteByName(string $word): void
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `word`=?";
        $this->sqlExecute($sql, [$this->user_id, $this->lang_id, $word]);
    } // end deleteByName()

    /**
     * Deletes words in database using ids as a parameter to select them
     *
     * @param string $ids JSON that identifies the texts to be deleted
     * @return void
     */
    public function delete(string $ids): void
    {
        $ids_array = json_decode($ids);
        $id_params = str_repeat("?,", count($ids_array) - 1) . "?";

        $sql = "DELETE FROM `{$this->table}` WHERE `id` IN ($id_params)";
        $this->sqlExecute($sql, $ids_array);
    } // delete()

    /**
     * Counts the number of rows (i.e. words) for a specific search
     *
     * @param string $search_text
     * @return int
     */
    public function countSearchRows(string $search_text): int
    {
        $like_str = '%' . $search_text . '%';

        $sql = "SELECT COUNT(`word`) AS `count_search`
                FROM `{$this->table}`
                WHERE `user_id`=? AND `lang_id`=?
                AND `word` LIKE ?";
        $row = $this->sqlFetch($sql, [$this->user_id, $this->lang_id, $like_str]);
        return $row['count_search'];
    } // end countSearchRows()

    /**
    * Returns the search results for a text using the parameters chosen by the user
    *
    * @param SearchWordsParameters $search_params
    * @return array
    */
    public function search(SearchWordsParameters $search_params): array
    {
        $sort_sql = $search_params->buildSortSQL();
        $search_text = '%' . $search_params->search_text . '%';
        
        $lang = new Language($this->pdo, $this->user_id);
        $lang->loadRecordById($this->lang_id);
        $lang_iso = $lang->name;
        $freq_table = 'frequency_list_' . $lang_iso;

        $sql = "SELECT w.`id`, w.`word`, w.`status`,
                    DATEDIFF(CURRENT_DATE(), w.`date_modified`) AS `diff_today_modif`,
                    DATEDIFF(`date_modified`, w.`date_created`) AS `frequency`,
                    CASE
                        WHEN f.`frequency_index` < 81 THEN 'very high'
                        WHEN f.`frequency_index` < 97 THEN 'high'
                        ELSE ''
                    END AS `freq_level`
                FROM `{$this->table}` w
                LEFT JOIN
                    `$freq_table` f ON w.`word` = f.`word`
                WHERE
                    w.`user_id` = ?
                    AND w.`lang_id` = ?
                    AND w.`word` LIKE ?
                ORDER BY $sort_sql LIMIT {$search_params->offset}, {$search_params->limit}";

        return $this->sqlFetchAll($sql, [
            $this->user_id, $this->lang_id, $search_text
        ]);
    } // end search()

    /**
     * Checks if word exists
     *
     * @param string $word
     * @return bool
     */
    public function exists(string $word): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `word`=?";
        return $this->sqlCount($sql, [$this->user_id, $this->lang_id, $word]) > 0;
    } // end exists()

    /**
     * Gets all the words for the current user & language combination
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param int $sort_by Is converted to a string using buildSortSQL()
     * @return array
     */
    public function getAll(int $sort_by): array
    {
        $search_params = new SearchWordsParameters('', $sort_by);
        $sort_sql = $search_params->buildSortSQL();

        $sql = "SELECT `id`, `word`, `status`, `is_phrase`
                FROM `{$this->table}`
                WHERE `user_id`=? AND `lang_id`=?
                ORDER BY $sort_sql";
        return $this->sqlFetchAll($sql, [$this->user_id, $this->lang_id]);
    } // end getAll()

    /**
     * Gets words user is still learning
     *
     * @return array
     */
    public function getLearning(): array
    {
        $sql = "SELECT `word`
                FROM `words`
                WHERE `user_id`=? AND `lang_id`=? AND `status`>0
                ORDER BY `is_phrase` ASC";
        return $this->sqlFetchAll($sql, [$this->user_id, $this->lang_id]);
    } // end getLearned()

    /**
     * Gets words already learned by user
     *
     * @return array
     */
    public function getLearned(): array
    {
        $sql = "SELECT `word`
                FROM `words`
                WHERE `user_id`=? AND `lang_id`=? AND `status`=0
                ORDER BY `is_phrase` ASC";
        return $this->sqlFetchAll($sql, [$this->user_id, $this->lang_id]);
    } // end getLearned()

}
