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

class Log extends DBEntity
{
    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     */
    public function __construct(\PDO $pdo, int $user_id) {
        parent::__construct($pdo, $user_id);
    } // end __construct()

    /**
     * Gets today's records for the current user in the log table
     *
     * @return int
     */
    public function countTodayRecords(): int {
        try {
            $sql = "SELECT COUNT(*) AS `exists` 
                    FROM `{$this->table}` 
                    WHERE `user_id` = ? 
                    AND DATE(`date_created`) = CURDATE()";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                throw new \Exception('There was an unexpected error trying to get today\'s log records.');
            }
            return $row['exists'];
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to get today\'s log records.');
        }
    } // end countTodayRecords()

    /**
     * Adds log record for current user
     *
     * @return bool
     */
    public function addRecord(): void {
        try {
            $today = date("Y-m-d H:i:s");

            $sql = "INSERT INTO `{$this->table}` (`user_id`, `date_created`) 
                    VALUES (?, ?)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $today]);
                    
            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to add log record.');
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to add log record.');
        } finally {
            $stmt = null;
        }
    } // end addRecord()

    // public function remove();
    // public function purge_old();
}


?>