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
     * @param mysqli_connect $con
     * @param integer $user_id
     * @param integer $learning_lang_id
     */
    public function __construct($con, $user_id, $learning_lang_id) {
        parent::__construct($con, $user_id, $learning_lang_id);
        $this->table = 'shared_texts';
    } // end __construct()

    /**
     * Gets texts by using a search pattern ($search_text) and a filter ($filter_sql).
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param string $filter_sql SQL statement specifying the filter to be used
     * @param string $search_text
     * @param integer $offset
     * @param integer $limit
     * @param integer $sort_by Is converted to a string using buildSortSQL()
     * @return array
     */
    public function getSearch($filter_sql, $search_text, $offset, $limit, $sort_by) {
        $filter_sql = $this->con->real_escape_string($filter_sql);
        $sort_sql = $this->con->real_escape_string($this->buildSortSQL($sort_by));
        $like_str = '%' . $search_text . '%';

        $lang = new Language($this->con, $this->user_id);
        $lang->loadRecord($this->learning_lang_id);

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
                WHERE `name`=? $filter_sql 
                AND `title` 
                LIKE ? 
                ORDER BY $sort_sql 
                LIMIT ?, ?";

        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("sssss", $this->user_id, $lang->getName(), $like_str, $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result ? $result->fetch_all() : false;
    } // end getSearch()

    /**
     * Gets all the texts for the current user & language combination
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param integer $offset
     * @param integer $limit
     * @param integer $sort_by Is converted to a string using buildSortSQL()
     * @return array
     */
    public function getAll($offset, $limit, $sort_by) {
        $sort_sql = $this->con->real_escape_string($this->buildSortSQL($sort_by));

        $lang = new Language($this->con, $this->user_id);
        $lang->loadRecord($this->learning_lang_id);

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
        $stmt->bind_param("ssss", $this->user_id, $lang->getName(), $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result ? $result->fetch_all() : false;
    } // end getAll()

    /**
     * Checks if text was already exists in database, to avoid duplicate entries.
     * It does this by checking the source url of the text to be added.
     *
     * @param string $source_url
     * @return boolean
     */
    public function exists($source_url)
    {
        if (empty($source_url)) {
            return false;
        }

        $sql = "SELECT 1
                FROM `{$this->table}`
                WHERE `source_uri` = ?";
        
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("s", $source_url);
        $stmt->execute();
        $result = $stmt->get_result();
            
        return ($result) && ($result->num_rows > 0);
    } // end exists() 

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the sort menu) 
     * to valid SQL strings
     *
     * @param integer $sort_by
     * @return string
     */
    protected function buildSortSQL($sort_by) {
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