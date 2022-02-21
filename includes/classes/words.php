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

use Aprelendo\Includes\Classes\Connect;
use Aprelendo\Includes\Classes\DBEntity;
use Aprelendo\Includes\Classes\Conversion;

class Words extends DBEntity {
    private $id            = 0;
    private $lang_id       = 0;
    private $word          = '';
    private $status        = 0;
    private $is_phrase     = false;
    private $date_created  = '';
    private $date_modified = '';

    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify any text: $pdo, $user_id & lang_id
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id) {
        parent::__construct($pdo, $user_id);
        $this->lang_id = $lang_id;
        $this->table = 'words';
    } // end __construct()

    /**
     * Adds a new wod to the database
     *
     * @param string $word
     * @param int $status
     * @param int $is_phrase It's an integer but it acts like a boolean (only uses 0 & 1)
     * @return void
     */
    public function add(string $word, int $status, bool $is_phrase): void {
        try {
            $word = mb_strtolower($word);
            $sql = "INSERT INTO `{$this->table}` (`user_id`, `lang_id`, `word`, `status`, `is_phrase`)
                    VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
                    `user_id`=?, `lang_id`=?, `word`=?, `status`=?, `date_modified`=NOW()";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $word, $status, (int)$is_phrase, 
                            $this->user_id, $this->lang_id, $word, $status]);
            
            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to add record to words table.');
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to add record to words table.');
        } finally {
            $stmt = null;
        }
    } // end add()

    /**
     * Updates status of existing words in database
     * 
     * @param array $words array containing all the words to update
     * @return void
     */
    public function updateByName(array $words): void {
        try {
            $in  = str_repeat('?,', count($words) - 1) . '?';

            $sql = "UPDATE `{$this->table}` SET `status`=`status`-1, `date_modified`=NOW() 
                    WHERE `user_id`=? AND `lang_id`=? AND `word` 
                    IN ($in)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_merge([$this->user_id, $this->lang_id], $words));
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to update record from words table.');
        } finally {
            $stmt = null;
        }
    } // end updateByName()

    /**
     * Updates status of existing words in database
     * 
     * @param array $words array containing all the words to update
     * @return void
     */
    public function updateStatus(string $word, bool $forgot): void {
        try {
            $forgot = $forgot ? "3" : "`status`-1";
            $sql = "UPDATE `{$this->table}` SET `status`=$forgot, `date_modified`=NOW() 
                    WHERE `user_id`=? AND `lang_id`=? AND `word`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $word]);
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to update record from words table.');
        } finally {
            $stmt = null;
        }
    } // end updateByName()

    /**
     * Deletes 1 word in database using word (not the id, the actual word) as a parameter to select it
     *
     * @param string $word
     * @return void
     */
    public function deleteByName(string $word): void {
        try {
            $sql = "DELETE FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `word`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $word]);
            
            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to delete record from words table.');
            }
        } catch (\Exception $e) {
            throw new \Exception('There was an unexpected error trying to delete record from words table.');
        } finally {
            $stmt = null;
        }
    } // end deleteByName()

    /**
     * Deletes words in database using ids as a parameter to select them
     *
     * @param string $ids JSON that identifies the texts to be deleted
     * @return void
     */
    public function delete(string $ids): void {
        try {
            $ids_array = json_decode($ids);
            $id_params = str_repeat("?,", count($ids_array)-1) . "?";

            $sql = "DELETE FROM `{$this->table}` WHERE `id` IN ($id_params)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($ids_array);
            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to delete record from words table.');
            }
        } catch (\Exception $e) {
            throw new \Exception('There was an unexpected error trying to delete record from words table.');
        } finally {
            $stmt = null;
        }
    } // delete()

    /**
     * Counts the number of rows (i.e. words) for a specific search
     *
     * @param string $search_text
     * @return int
     */
    public function countSearchRows(string $search_text): int {
        try {
            $like_str = '%' . $search_text . '%';
            
            $sql = "SELECT COUNT(`word`) AS `count_search`
                    FROM `{$this->table}` 
                    WHERE `user_id`=? AND `lang_id`=?  
                    AND `word` LIKE ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $like_str]);
            $row = $stmt->fetch();
            $total_rows = $row['count_search'];
            return $total_rows;
        } catch (\PDOException $e) {
            return 0;
        } finally {
            $stmt = null;
        }
    } // end countSearchRows()

    /**
     * Counts the number of rows (i.e. words) for the current user & language combination
     * It differs from countSearchRows in that this function does not apply any additional filter
     *
     * @return int
     */
    public function countAllRows(): int {
        try {
            $sql = "SELECT COUNT(word) AS `count_all`
                    FROM `{$this->table}`  
                    WHERE `user_id`=? AND `lang_id`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id]);
            $row = $stmt->fetch();
            $total_rows = $row['count_all'];
            return $total_rows;
        } catch (\PDOException $e) {
            return 0;
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
     * @param int $offset
     * @param int $limit
     * @param int $sort_by Is converted to a string using buildSortSQL()
     * @return array
     */
    public function getSearch(string $search_text, int $offset, int $limit, int $sort_by): array {
        try {
            $sort_sql = $this->buildSortSQL($sort_by);

            $sql = "SELECT `id`, `word`, `status` 
                    FROM `{$this->table}` 
                    WHERE `user_id`=:user_id AND `lang_id`=:lang_id AND word LIKE :search_str 
                    ORDER BY $sort_sql LIMIT :offset, :limit";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':user_id', $this->user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':lang_id', $this->lang_id, \PDO::PARAM_INT);
            $stmt->bindValue(':search_str', "%$search_text%");
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!$result || empty($result)) {
                throw new \Exception('Oops! There are no words meeting your search criteria.');
            }

            return $result;
        } catch (\PDOException $e) {
            throw new \Exception('Oops! There was an unexpected error trying to process your search request.');
        } finally {
            $stmt = null;
        }        
    } // end getSearch()

     /**
     * Checks if word exists
     *
     * @param string $word
     * @return bool
     */
    public function exists(string $word): bool {
        try {
            $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `word`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$word]);
            $num_rows = $stmt->fetchColumn();
           
            return ($num_rows) && ($num_rows > 0) ? true : false;
        } catch (\PDOException $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end exists()

    /**
     * Gets all the words for the current user & language combination
     * It returns only specific ranges by using an $offset (specifies where to start) and a $limit (how many rows to get)
     * Values are returned using a sort pattern ($sort_by)
     *
     * @param int $offset
     * @param int $limit
     * @param int $sort_by Is converted to a string using buildSortSQL()
     * @return array
     */
    public function getAll(int $offset, int $limit, int $sort_by): array {
        try {
            $sort_sql = $this->buildSortSQL($sort_by);
            
            $sql = "SELECT `id`, `word`, `status`, `is_phrase`   
                    FROM `{$this->table}` 
                    WHERE `user_id`=:user_id AND `lang_id`=:lang_id  
                    ORDER BY $sort_sql LIMIT :offset, :limit";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':user_id', $this->user_id, \PDO::PARAM_INT);
            $stmt->bindParam(':lang_id', $this->lang_id, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            throw new \Exception('Oops! There was an unexpected error trying to process your search request.');
        } finally {
            $stmt = null;
        }
    } // end getAll()  

    /**
     * Gets words user is still learning
     *
     * @return array
     */
    public function getLearning(): array {
        try {
            $sql = "SELECT `word` 
                    FROM `words` 
                    WHERE `user_id`=? AND `lang_id`=? AND `status`>0 
                    ORDER BY `is_phrase` ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id]);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            throw new \Exception('Oops! There was an unexpected error trying to process your search request.');
        } finally {
            $stmt = null;
        }
    } // end getLearned()

    /**
     * Gets words already learned by user
     *
     * @return array
     */
    public function getLearned(): array {
        try {
            $sql = "SELECT `word` 
                    FROM `words` 
                    WHERE `user_id`=? AND `lang_id`=? AND `status`=0 
                    ORDER BY `is_phrase` ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id]);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            throw new \Exception('Oops! There was an unexpected error trying to process your search request.');
        } finally {
            $stmt = null;
        }
    } // end getLearned()

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the sort menu) 
     * to valid SQL strings
     *
     * @param int $sort_by
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
            case '4': // words first
                return '`is_phrase` DESC';
                break;
            case '5': // phrases first
                return '`is_phrase`';
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
     * @param int $order_by Is converted to a string using buildSortSQL()
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
            $stmt = $this->pdo->prepare($sql);
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

    /**
     * Get the value of id
     */ 
    public function getId(): int
    {
        return $this->id;
    } // end getId()

    /**
     * Get the value of lang_id
     */ 
    public function getLangId(): int
    {
        return $this->lang_id;
    } // end getLangId()

    /**
     * Get the value of word
     */ 
    public function getWord(): string
    {
        return $this->word;
    } // end getWord()

    /**
     * Get the value of status
     */ 
    public function getStatus(): int
    {
        return $this->status;
    } // end getStatus()

    /**
     * Get the value of is_phrase
     */ 
    public function getIsPhrase(): bool
    {
        return $this->is_phrase;
    } // end getIsPhrase()

    /**
     * Get the value of date_created
     */ 
    public function getDateCreated(): string
    {
        return $this->date_created;
    } // end getDateCreated()

    /**
     * Get the value of date_modified
     */ 
    public function getDateModified(): string
    {
        return is_null($this->date_modified) ? '' : $this->date_modified;
    } // end getDateModified()
}

?>