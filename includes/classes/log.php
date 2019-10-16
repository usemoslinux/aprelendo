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
     * @param mysqli_connect $con
     * @param integer $user_id
     */
    public function __construct($con, $user_id) {
        parent::__construct($con, $user_id);
    }

    /**
     * Gets today's records for the current user in the log table
     *
     * @return array if successful or NULL if unsuccessful
     */
    public function getTodayRecords() {
        $user_id = $this->con->real_escape_string($this->user_id);

        $sql = "SELECT `date_created` 
                FROM `{$this->table}` 
                WHERE `user_id` = ? 
                AND DATE(`date_created`) = CURDATE()";

        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
                
        return $result ? $result->fetch_all() : false;
    }

    /**
     * Adds log record for current user
     *
     * @return mysqli_result if successful, FALSE if unsuccessful
     */
    public function addRecord() {
        $user_id = $this->con->real_escape_string($this->user_id);
        $table = $this->con->real_escape_string($this->table);
        $today = date("Y-m-d H:i:s");

        $sql = "INSERT INTO `$table` (`user_id`, `date_created`) 
                VALUES (?, ?)";

        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("ss", $user_id, $today);
        $result = $stmt->execute();
                
        return $result;
    }

    // public function remove();
    // public function purge_old();
}


?>