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

// require_once('connect.php');
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
        $this->table = 'sharedtexts';
        $this->cols = array(
            'id' => 'stextId',
            'userid' => 'stextUserId', 
            'lgid' => 'stextLgId', 
            'title' => 'stextTitle', 
            'author' => 'stextAuthor', 
            'text' => 'stext', 
            'sourceURI' => 'stextSourceURI', 
            'type' => 'stextType', 
            'nrofwords' => 'stextNrOfWords',
            'level' => 'stextLevel',
            'totallikes' => 'totallikes',
            'userliked' => 'userliked');
    }

    /**
     * Gets texts by using a search pattern ($search_text) and a filter ($filter_sql).
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param string $filter_sql SQL statement specifying the filter to be used
     * @param string $search_text
     * @param integer $offset
     * @param integer $limit
     * @param integer $sort_by Is converted to a string using getSortSQL()
     * @return array
     */
    public function getSearch($filter_sql, $search_text, $offset, $limit, $sort_by) {
        // escape parameters
        $filter_sql = $this->con->real_escape_string($filter_sql);
        $search_text = $this->con->real_escape_string($search_text);
        $offset = $this->con->real_escape_string($offset);
        $limit = $this->con->real_escape_string($limit);
        $sort_sql = $this->con->real_escape_string($this->getSortSQL($sort_by));

        $lang = new Language($this->con, $this->learning_lang_id, $this->user_id);

        $sql = "SELECT {$this->cols['id']}, 
                (SELECT userName FROM users WHERE userId = {$this->cols['userid']}), 
                {$this->cols['title']}, 
                {$this->cols['author']}, 
                {$this->cols['type']}, 
                {$this->cols['nrofwords']}, 
                {$this->cols['level']}, 
                languages.LgName, 
                (SELECT COUNT(likesId) FROM likes WHERE likesTextId = {$this->cols['id']}) AS {$this->cols['totallikes']},
                (SELECT COUNT(likesId) FROM likes WHERE likesTextId = {$this->cols['id']} AND likesUserId = $this->user_id) AS {$this->cols['userliked']}
                FROM {$this->table} 
                INNER JOIN languages ON {$this->table}.stextLgId = languages.LgID
                WHERE LgName = '{$lang->name}' $filter_sql 
                AND {$this->cols['title']} 
                LIKE '%$search_text%' 
                ORDER BY $sort_sql 
                LIMIT $offset, $limit";
        
        $result = $this->con->query($sql);

        return $result ? $result->fetch_all() : false;
    }

    /**
     * Gets all the texts for the current user & language combination
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param integer $offset
     * @param integer $limit
     * @param integer $sort_by Is converted to a string using getSortSQL()
     * @return array
     */
    public function getAll($offset, $limit, $sort_by) {
        // escape parameters
        $offset = $this->con->real_escape_string($offset);
        $limit = $this->con->real_escape_string($limit);
        $sort_sql = $this->con->real_escape_string($this->getSortSQL($sort_by));

        $lang = new Language($this->con, $this->learning_lang_id, $this->user_id);

        $sql = "SELECT {$this->cols['id']}, 
                (SELECT userName FROM users WHERE userId = {$this->cols['userid']}), 
                {$this->cols['title']}, 
                {$this->cols['author']}, 
                {$this->cols['type']}, 
                {$this->cols['nrofwords']}, 
                {$this->cols['level']}, 
                languages.LgName
                FROM {$this->table} 
                INNER JOIN languages ON {$this->table}.stextLgId = languages.LgID
                WHERE LgName = '{$lang->name}'
                ORDER BY $sort_sql 
                LIMIT $offset, $limit";

        $result = $this->con->query($sql);
        
        return $result ? $result->fetch_all() : false;
    }

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the sort menu) 
     * to valid SQL strings
     *
     * @param integer $sort_by
     * @return string
     */
    public function getSortSQL($sort_by) {
        $result = parent::getSortSQL($sort_by);

        if (!empty($result)) {
            return $result;
        } else {
            switch ($sort_by) {
                case '2': // more likes first
                    return $this->cols['totallikes'] . ' DESC';
                    break;
                case '3': // less likes first
                    return $this->cols['totallikes'];
                    break;
                default:
                    return '';
                    break;
            }
        }
    }
}


?>