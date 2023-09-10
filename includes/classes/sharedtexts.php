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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aprelendo\Includes\Classes;

use Aprelendo\Includes\Classes\Texts;
use Aprelendo\Includes\Classes\Language;
use Aprelendo\Includes\Classes\SearchTextParameters;
use Aprelendo\Includes\Classes\UserException;

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
        $search_sql = !empty($search_params->search_text) ? '"%' . $search_params->search_text . '%"' : '"%"';

        $lang = new Language($this->pdo, $this->user_id);
        $lang->loadRecordById($this->lang_id);

        $sql = "SELECT t.id,
                    (SELECT `name` FROM `users` WHERE `id` = t.user_id) AS `user_name`,
                    t.title,
                    t.author,
                    t.audio_uri,
                    t.source_uri,
                    t.type,
                    t.word_count,
                    CHAR_LENGTH(t.text) AS `char_length`,
                    t.level,
                    l.name,
                    (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id) AS `total_likes`,
                    (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id
                    AND `user_id` = ?) AS `user_liked`
                FROM `{$this->table}` t
                INNER JOIN `languages` l ON t.lang_id = l.id
                WHERE `name`= ?
                AND t.level $filter_level_sql
                AND t.type $filter_type_sql
                AND `title` LIKE $search_sql
                ORDER BY $sort_sql
                LIMIT {$search_params->offset}, {$search_params->limit}";

        return $this->sqlFetchAll($sql, [
            $this->user_id, $lang->name, $search_params->filter_level, $search_params->filter_type
        ]);
    } // end search()

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
