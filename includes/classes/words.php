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
use Aprelendo\Includes\Classes\Conversion;

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
        $sql = "INSERT INTO `words` (`user_id`, `lang_id`, `word`, `status`, `is_phrase`)
                VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
                `user_id`=?, `lang_id`=?, `word`=?, `status`=?, `is_phrase`=?, `date_modified`=NOW()";

        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("sssiisssii", $this->user_id, $this->learning_lang_id, $word, $status, $isphrase, 
            $this->user_id, $this->learning_lang_id, $word, $status, $isphrase);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Updates status of existing words in database
     * 
     * @param string $words array containing all the words to update
     * @return boolean
     */
    public function updateByName($words) {
        $csvwords = $this->con->real_escape_string(Conversion::ArraytoCSV($words));
        $user_id = $this->con->real_escape_string($this->user_id);
        $lang_id = $this->con->real_escape_string($this->learning_lang_id);

        $sql = "UPDATE `words` SET `status`=`status`-1, `date_modified`=NOW() 
                WHERE `user_id`=$user_id AND `lang_id`=$lang_id AND `word` 
                IN ($csvwords)";
        $result = $this->con->query($sql);
                
        return $result;
    }

    /**
     * Deletes 1 word in database using word (not the id, the actual word) as a parameter to select it
     *
     * @param string $word
     * @return boolean
     */
    public function deleteByName($word) {
        $sql = "DELETE FROM `words` WHERE `word`=?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("s", $word);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Deletes words in database using ids as a parameter to select them
     *
     * @param string $ids JSON that identifies the texts to be deleted
     * @return boolean
     */
    public function delete($ids) {
        $cs_ids = $this->con->real_escape_string(Conversion::JSONtoCSV($ids));

        $sql = "DELETE FROM `words` WHERE `id` IN ($cs_ids)";
        $stmt = $this->con->query($sql);
                
        return $result;
    }

    /**
     * Counts the number of rows (i.e. words) for a specific search
     *
     * @param string $search_text
     * @return integer|boolean
     */
    public function countSearchRows($search_text) {
        $search_text = $this->con->real_escape_string($search_text);
        $like_str = '%' . $search_text . '%';
        
        $sql = "SELECT COUNT(`word`) 
                FROM `words` 
                WHERE `user_id`=? AND `lang_id`=?  
                AND `word` LIKE ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("sss", $this->user_id, $this->learning_lang_id, $like_str);
        $stmt->execute();
        $result = $stmt->get_result();
                
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
     * It differs from countSearchRows in that this function does not apply any additional filter
     *
     * @return integer|boolean
     */
    public function countAllRows() {
        $sql = "SELECT COUNT(word) 
                FROM `words`  
                WHERE `user_id`=? AND `lang_id`=?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("ss", $this->user_id, $this->learning_lang_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
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
     * @param integer $sort_by Is converted to a string using buildSortSQL()
     * @return array
     */
    public function getSearch($search_text, $offset, $limit, $sort_by) {
        $like_str = '%' . $search_text . '%';
        $sort_sql = $this->con->real_escape_string($this->buildSortSQL($sort_by));

        $sql = "SELECT `id`, `word`, `status` 
                FROM `words` 
                WHERE `user_id`=? AND `lang_id`=? AND word LIKE ? 
                ORDER BY $sort_sql LIMIT ?, ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("sssss", $this->user_id, $this->learning_lang_id, $like_str, $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result ? $result->fetch_all() : false;
    }

    /**
     * Gets all the words for the current user & language combination
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
        
        $sql = "SELECT `id`, `word`, `status` 
                FROM `words` 
                WHERE `user_id`=? AND `lang_id`=?  
                ORDER BY $sort_sql LIMIT ?, ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("ssss", $this->user_id, $this->learning_lang_id, $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result ? $result->fetch_all() : false;
    }   

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the sort menu) 
     * to valid SQL strings
     *
     * @param integer $sort_by
     * @return string
     */
    private function buildSortSQL($sort_by) {
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
     * @param integer $order_by Is converted to a string using buildSortSQL()
     * @return boolean
     */
    public function createCSVFile($search_text, $order_by) {
        //escape parameters
        $search_text = $this->con->real_escape_string($search_text);
        $sort_sql = $this->buildSortSQL($order_by);
        
        $filter = !empty($search_text) ? "AND word LIKE '%$search_text%' " : '';
        $filter .= $order_by != '' ? "ORDER BY $sort_sql" : '';

        $sql = "SELECT `word` 
                FROM `words`
                WHERE `user_id`=? AND `lang_id`=? $filter";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("ss", $this->user_id, $this->learning_lang_id);
        $stmt->execute();
        $result = $stmt->get_result();
                
        if ($result) {
            $num_fields = $stmt->field_count;
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