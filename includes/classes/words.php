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
use Aprelendo\Includes\Classes\DBEntity;

class Words extends DBEntity {
    private $learning_lang_id;

    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify any text: $con, $user_id & learning_lang_id
     *
     * @param mysqli_connect $con
     * @param integer $user_id
     * @param integer $learning_lang_id
     */
    public function __construct($con, $user_id, $learning_lang_id) {
        parent::__construct($con, $user_id);
        $this->learning_lang_id = $learning_lang_id;
        $this->table = 'words';
    }

    /**
     * Adds a new wod to the database
     *
     * @param string $word
     * @param integer $status
     * @param integer $isphrase It's an integer but it acts like a boolean (only uses 0 & 1)
     * @return boolean
     */
    public function add($word, $status, $isphrase) {
        // escape parameters
        $word = $this->con->real_escape_string($word);
        $status = $this->con->real_escape_string($status);
        $isphrase = $this->con->real_escape_string($isphrase);
        
        $result = $this->con->query("INSERT INTO `words` (`user_id`, `lang_id`, `word`, `status`, `is_phrase`)
            VALUES ('$this->user_id', '$this->learning_lang_id', '$word', $status, $isphrase) ON DUPLICATE KEY UPDATE
            `user_id`='$this->user_id', `lang_id`=$this->learning_lang_id, `word`='$word', `status`=$status, `is_phrase`=$isphrase, `date_modified`=NOW()");

        return $result;
    }

    /**
     * Updates status of existing words in database
     * 
     * @param string $words array containing all the words to update
     * @return boolean
     */
    public function updateByName($words) {
        $csvwords = $this->ArraytoCSV($words);
        $result = $this->con->query("UPDATE `words` SET `status`=`status`-1, `date_modified`=NOW() 
            WHERE `user_id`='$this->user_id' AND `lang_id`='$this->learning_lang_id' AND `word` IN ($csvwords) ");

        return $result;
    }

    /**
     * Deletes 1 word in database using word (not the id, the actual word) as a parameter to select it
     *
     * @param string $word
     * @return boolean
     */
    public function deleteByName($word) {
        // escape parameters
        $word = $this->con->real_escape_string($word);
        
        $result = $this->con->query("DELETE FROM `words` WHERE `word`='$word'");

        return $result;
    }

    /**
     * Deletes words in database using ids as a parameter to select them
     *
     * @param string $ids JSON that identifies the texts to be deleted
     * @return boolean
     */
    public function deleteByIds($ids) {
        $wordIDs = $this->JSONtoCSV($ids);
        $result = $this->con->query("DELETE FROM `words` WHERE `id` IN ($wordIDs)");

        return $result;
    }

    /**
     * Counts the number of rows (i.e. words) for a specific search
     *
     * @param string $search_text
     * @return integer|boolean
     */
    public function countRowsFromSearch($search_text) {
        // escape parameters
        $search_text = $this->con->real_escape_string($search_text);

        $result = $this->con->query("SELECT COUNT(`word`) FROM `words` WHERE `user_id`='$this->user_id' AND `lang_id`='$this->learning_lang_id' AND `word` LIKE '%$search_text%'");
        
        if ($result) {
            $row = $result->fetch_array();
            $total_rows = $row[0];
            return $total_rows;
        } else {
            return false;
        }
    }

    /**
     * Counts the number of rows (i.e. words) for the current user & language combination
     * It differs from countRowsFromSearch in that this function does not apply any additional filter
     *
     * @return integer|boolean
     */
    public function countAllRows() {
        $result = $this->con->query("SELECT COUNT(word) FROM words WHERE `user_id`='$this->user_id' AND `lang_id`='$this->learning_lang_id'");
        
        if ($result) {
            $row = $result->fetch_array();
            $total_rows = $row[0];
            return $total_rows;
        } else {
            return false;
        }
    }

    /**
     * Gets words by using a search pattern ($search_text).
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param string $search_text
     * @param integer $offset
     * @param integer $limit
     * @param integer $sort_by Is converted to a string using getSortSQL()
     * @return array
     */
    public function getSearch($search_text, $offset, $limit, $sort_by) {
        // escape parameters
        $search_text = $this->con->real_escape_string($search_text);
        $offset = $this->con->real_escape_string($offset);
        $limit = $this->con->real_escape_string($limit);
        $sort_sql = $this->con->real_escape_string($this->getSortSQL($sort_by));

        $result = $this->con->query("SELECT `id`, `word`, `status` 
            FROM `words` 
            WHERE `user_id`='$this->user_id' AND `lang_id`='$this->learning_lang_id' AND word LIKE '%$search_text%' 
            ORDER BY $sort_sql LIMIT $offset, $limit");

        return $result ? $result->fetch_all() : false;
    }

    /**
     * Gets all the words for the current user & language combination
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
        
        $result = $this->con->query("SELECT `id`, `word`, `status` 
            FROM `words` 
            WHERE `user_id`='$this->user_id' AND `lang_id`='$this->learning_lang_id' 
            ORDER BY $sort_sql LIMIT $offset, $limit");

        $sql = "SELECT `id`, `word`, `status` 
        FROM `words` 
        WHERE `user_id`='$this->user_id' AND `lang_id`='$this->learning_lang_id' 
        ORDER BY $sort_sql LIMIT $offset, $limit";
        return $result ? $result->fetch_all() : false;
    }   

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the sort menu) 
     * to valid SQL strings
     *
     * @param integer $sort_by
     * @return string
     */
    private function getSortSQL($sort_by) {
        switch ($sort_by) {
            case '0': // new first
                return '`id` DESC';
                break;
            case '1': // old first
                return '`id`';
                break;
            case '2': // learned first
                return '`status`';
                break;
            case '3': // learning first
                return '`status` DESC';
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * Exports words to a CSV file
     * 
     * It exports either the whole set of words corresponding to a user & language combination,
     * or the specific subset that results from applying additional filters (e.g. $search_text).
     * Results are ordered using $order_by.
     *
     * @param string $search_text
     * @param integer $order_by Is converted to a string using getSortSQL()
     * @return boolean
     */
    public function createCSVFile($search_text, $order_by) {
        //escape parameters
        $search_text = $this->con->real_escape_string($search_text);
        $sort_sql = $this->getSortSQL($order_by);
        
        $filter = !empty($search_text) ? "AND word LIKE '%$search_text%' " : '';
        $filter .= $order_by != '' ? "ORDER BY $sort_sql" : '';

        $result = $this->con->query("SELECT `word` 
            FROM `words`
            WHERE `user_id`='$this->user_id' AND `lang_id`='$this->learning_lang_id' $filter");
        if ($result) {
            $num_fields = $this->con->field_count;
            $headers = array();

            for ($i = 0; $i < $num_fields; $i++) {
                $h = $result->fetch_field_direct($i);
                $headers[] = $h->name;
            }

            $fp = fopen('php://output', 'w');
            if ($fp) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="export.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, $headers);
                while ($row = $result->fetch_array(MYSQLI_NUM)) {
                    fputcsv($fp, array_values($row));
                }
                return true;
            }
        }
        return false;
    }
}

?>