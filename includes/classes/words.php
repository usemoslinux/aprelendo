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
    private $id = 0;
    private $lang_id = 0;
    private $word = '';
    private $status = 0;
    private $is_phrase = false;
    private $date_created = '';
    private $date_modified = '';

    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify any text: $con, $user_id & lang_id
     *
     * @param mysqli_connect $con
     * @param integer $user_id
     * @param integer $lang_id
     */
    public function __construct(\PDO $con, int $user_id, int $lang_id) {
        parent::__construct($con, $user_id);
        $this->lang_id = $lang_id;
        $this->table = 'words';
    } // end __construct()

    /**
     * Adds a new wod to the database
     *
     * @param string $word
     * @param integer $status
     * @param integer $isphrase It's an integer but it acts like a boolean (only uses 0 & 1)
     * @return boolean
     */
    public function add(string $word, int $status, bool $isphrase): bool {
        try {
            $sql = "INSERT INTO `{$this->table}` (`user_id`, `lang_id`, `word`, `status`, `is_phrase`)
                    VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
                    `user_id`=?, `lang_id`=?, `word`=?, `status`=?, `is_phrase`=?, `date_modified`=NOW()";

            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $word, $status, $isphrase, 
                            $this->user_id, $this->lang_id, $word, $status, $isphrase]);
            
            return true;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end add()

    /**
     * Updates status of existing words in database
     * 
     * @param string $words array containing all the words to update
     * @return boolean
     */
    public function updateByName(array $words): bool {
        try {
            // $csvwords = Conversion::ArraytoCSV($words);
            $user_id = $this->user_id;
            $lang_id = $this->lang_id;

            $sql = "UPDATE `{$this->table}` SET `status`=`status`-1, `date_modified`=NOW() 
                    WHERE `user_id`=? AND `lang_id`=? AND `word` 
                    IN (?)";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $words]);
                    
            return true;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end updateByName()

    /**
     * Deletes 1 word in database using word (not the id, the actual word) as a parameter to select it
     *
     * @param string $word
     * @return boolean
     */
    public function deleteByName(string $word): bool {
        try {
            $sql = "DELETE FROM `{$this->table}` WHERE `word`=?";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$word]);
            return true;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end deleteByName()

    /**
     * Deletes words in database using ids as a parameter to select them
     *
     * @param string $ids JSON that identifies the texts to be deleted
     * @return boolean
     */
    public function delete(string $ids) {
        try {
            $cs_ids = Conversion::JSONtoCSV($ids);

            $sql = "DELETE FROM `{$this->table}` WHERE `id` IN (?)";
            $stmt = $this->con->prepare($sql);
            $stmt->execute(array($cs_ids));
            return true;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // delete()

    /**
     * Counts the number of rows (i.e. words) for a specific search
     *
     * @param string $search_text
     * @return integer|boolean
     */
    public function countSearchRows(string $search_text) {
        try {
            $like_str = '%' . $search_text . '%';
            
            $sql = "SELECT COUNT(`word`) AS `count_search`
                    FROM `{$this->table}` 
                    WHERE `user_id`=? AND `lang_id`=?  
                    AND `word` LIKE ?";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $like_str]);
            $row = $stmt->fetch();
            $total_rows = $row['count_search'];
            return $total_rows;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end countSearchRows()

    /**
     * Counts the number of rows (i.e. words) for the current user & language combination
     * It differs from countSearchRows in that this function does not apply any additional filter
     *
     * @return integer|boolean
     */
    public function countAllRows() {
        try {
            $sql = "SELECT COUNT(word) AS `count_all`
                    FROM `{$this->table}`  
                    WHERE `user_id`=? AND `lang_id`=?";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id]);
            $row = $stmt->fetch();
            $total_rows = $row['count_all'];
            return $total_rows;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end countAllRows()

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
    public function getSearch(string $search_text, int $offset, int $limit, int $sort_by) {
        try {
            $like_str = '%' . $search_text . '%';
            $sort_sql = $this->buildSortSQL($sort_by);

            $sql = "SELECT `id`, `word`, `status` 
                    FROM `{$this->table}` 
                    WHERE `user_id`=? AND `lang_id`=? AND word LIKE ? 
                    ORDER BY $sort_sql LIMIT ?, ?";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $like_str, $offset, $limit]);
            
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }        
    } // end getSearch()

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
    public function getAll(int $offset, int $limit, int $sort_by) {
        try {
            $sort_sql = $this->buildSortSQL($sort_by);
            
            $sql = "SELECT `id`, `word`, `status` 
                    FROM `{$this->table}` 
                    WHERE `user_id`=? AND `lang_id`=?  
                    ORDER BY $sort_sql LIMIT ?, ?";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $offset, $limit]);
            
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end getAll()  

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the sort menu) 
     * to valid SQL strings
     *
     * @param integer $sort_by
     * @return string
     */
    private function buildSortSQL(int $sort_by): string {
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
    } // end buildSortSQL()

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
    public function createCSVFile(string $search_text, int $order_by): bool {
        try {
            $sort_sql = $this->buildSortSQL($order_by);
            
            $filter = !empty($search_text) ? "AND word LIKE '%$search_text%' " : '';
            $filter .= $order_by !== -1 ? "ORDER BY $sort_sql" : '';

            $sql = "SELECT `word` 
                    FROM `{$this->table}`
                    WHERE `user_id`=? AND `lang_id`=? $filter";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id]);

            $num_fields = $stmt->columnCount();
            $headers = [];

            for ($i = 0; $i < $num_fields; $i++) {
                $h = $stmt->getColumnMeta($i);
                $headers[] = $h['name'];
            }

            $fp = fopen('php://output', 'w');
            
            if ($fp) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="export.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
                fputcsv($fp, $headers);

                while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                    fputcsv($fp, array_values($row));
                }

                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end createCSVFile()
}

?>