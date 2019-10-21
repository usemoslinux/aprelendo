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

class Log extends DBEntity
{
    /**
     * Constructor
     *
     * @param \PDO $con
     * @param integer $user_id
     */
    public function __construct(\PDO $con, int $user_id) {
        parent::__construct($con, $user_id);
    } // end __construct()

    /**
     * Gets today's records for the current user in the log table
     *
     * @return array|bool
     */
    public function getTodayRecords() {
        $sql = "SELECT `date_created` 
                FROM `{$this->table}` 
                WHERE `user_id` = ? 
                AND DATE(`date_created`) = CURDATE()";

        $stmt = $this->con->prepare($sql);
        $stmt->execute([$user_id]);
                
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } // end getTodayRecords()

    /**
     * Adds log record for current user
     *
     * @return bool
     */
    public function addRecord(): bool {
        try {
            $today = date("Y-m-d H:i:s");

            $sql = "INSERT INTO `{$this->table}` (`user_id`, `date_created`) 
                    VALUES (?, ?)";

            $stmt = $this->con->prepare($sql);
            $stmt->execute([$user_id, $today]);
                    
            return true;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end addRecord()

    // public function remove();
    // public function purge_old();
}


?>