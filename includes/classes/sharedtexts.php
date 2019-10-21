<?php 
/**
 * Copyright (C) 2018 Pablo Castagnino
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

use Aprelendo\Includes\Classes\Connect;
use Aprelendo\Includes\Classes\Texts;
use Aprelendo\Includes\Classes\Language;

class SharedTexts extends Texts
{
    /**
     * Constructor
     *
     * @param \PDO $con
     * @param integer $user_id
     * @param integer $lang_id
     */
    public function __construct(\PDO $con, int $user_id, int $lang_id) {
        parent::__construct($con, $user_id, $lang_id);
        $this->table = 'shared_texts';
    } // end __construct()

    /**
     * Gets texts by using a search pattern ($search_text) and a filter ($search_filter).
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param string $search_filter SQL statement specifying the filter to be used
     * @param string $search_text
     * @param integer $offset
     * @param integer $limit
     * @param integer $sort_by Is converted to a string using buildSortSQL()
     * @return array|bool
     */
    public function getSearch(string $search_filter, string $search_text, int $offset, int $limit, int $sort_by) {
        try {
            $sort_sql = $this->buildSortSQL($sort_by);
            $filter_sql = empty($search_filter) ? '' : 'AND `type`=?';
            $like_str = '%' . $search_text . '%';

            $lang = new Language($this->con, $this->user_id);
            $lang->loadRecord($this->lang_id);

            $sql = sprintf("SELECT t.id, 
                           (SELECT `name` FROM `users` WHERE `id` = t.user_id) AS `user_name`, 
                           t.title, 
                           t.author, 
                           t.source_uri,
                           t.type, 
                           t.word_count, 
                           t.level, 
                           l.name, 
                           (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id) AS `total_likes`,
                           (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id AND `user_id` = ?) AS `user_liked` 
                           FROM `%s` t 
                           INNER JOIN `languages` l ON t.lang_id = l.id
                           WHERE `name`=? 
                           AND `title` LIKE ? %s 
                           ORDER BY %s 
                           LIMIT ?, ?", $this->table, $filter_sql, $sort_sql);

            $stmt = $this->con->prepare($sql);
            if (empty($search_filter)) {
                $stmt->execute([$this->user_id, $lang->getName(), $like_str, $offset, $limit]);
            } else {
                $stmt->execute([$this->user_id, $lang->getName(), $like_str, $search_filter, $offset, $limit]);
            }
            
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end getSearch()

    /**
     * Gets all the texts for the current user & language combination
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param integer $offset
     * @param integer $limit
     * @param integer $sort_by Is converted to a string using buildSortSQL()
     * @return array|bool
     */
    public function getAll(int $offset, int $limit, int $sort_by) {
        try {
            $sort_sql = $this->buildSortSQL($sort_by);

            $lang = new Language($this->con, $this->user_id);
            $lang->loadRecord($this->lang_id);

            $sql = "SELECT t.id, 
                    (SELECT `name` FROM `users` WHERE `id` = t.user_id) AS `user_name`, 
                    t.title, 
                    t.author, 
                    t.source_uri,
                    t.type, 
                    t.word_count, 
                    t.level, 
                    l.name,
                    (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id) AS `total_likes`,
                    (SELECT COUNT(`id`) FROM `likes` WHERE `text_id` = t.id AND `user_id` = ?) AS `user_liked`
                    FROM `{$this->table}` t
                    INNER JOIN `languages` l ON t.lang_id = l.id
                    WHERE `name`=? 
                    ORDER BY $sort_sql 
                    LIMIT ?, ?";

            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $lang->getName(), $offset, $limit]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end getAll()

    /**
     * Checks if text was already exists in database, to avoid duplicate entries.
     * It does this by checking the source url of the text to be added.
     *
     * @param string $source_url
     * @return bool
     */
    public function exists(string $source_url): bool {
        try {
            if (empty($source_url)) {
                return false;
            }
    
            $sql = "SELECT COUNT(*) AS `exists`
                    FROM `{$this->table}`
                    WHERE `source_uri` = ?";
            
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$source_url]);
            $row = $stmt->fetch();
                
            return ($row) && ($row['exists'] > 0);
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end exists() 

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the sort menu) 
     * to valid SQL strings
     *
     * @param integer $sort_by
     * @return string
     */
    protected function buildSortSQL(int $sort_by): string {
        $result = parent::buildSortSQL($sort_by);

        if (!empty($result)) {
            return $result;
        } else {
            switch ($sort_by) {
                case '2': // more likes first
                    return '`total_likes` DESC';
                    break;
                case '3': // less likes first
                    return '`total_likes`';
                    break;
                default:
                    return '';
                    break;
            }
        }
    } // end buildSortSQL()
}


?>