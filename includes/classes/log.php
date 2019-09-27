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
        $table = $this->con->real_escape_string($this->table);

        $sql_str = "SELECT `date_created` 
                    FROM `$table` 
                    WHERE `user_id` = '$user_id' 
                    AND DATE(`date_created`) = CURDATE()";

        $result = $this->con->query($sql_str);

        return $result ? $result->fetch_all() : NULL;
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

        $sql_str = "INSERT INTO `$table` 
                        (`user_id`, `date_created`)
                    VALUES
                        ('$user_id', '$today')";

        return $this->con->query($sql_str);
    }

    // public function remove();
    // public function purge_old();
}


?>