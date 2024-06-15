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

namespace Aprelendo;

use Aprelendo\UserException;

abstract class Log extends DBEntity
{
    private int $user_id = 0;

    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     *
     */
    public function __construct(\PDO $pdo, int $user_id)
    {
        parent::__construct($pdo);
        $this->user_id = $user_id;
    } // end __construct()

    /**
     * Gets today's records for the current user in the log table
     *
     * @return int
     */
    public function countTodayRecords(): int
    {
        $sql = "SELECT COUNT(*) AS `exists`
                FROM `{$this->table}`
                WHERE `user_id` = ?
                AND `date_created` = CURRENT_DATE()";

        $row = $this->sqlFetch($sql, [$this->user_id]);

        return $row['exists'];
    } // end countTodayRecords()

    /**
     * Adds log record for current user
     *
     * @return bool
     */
    public function addRecord(): void
    {
        $sql = "INSERT INTO `{$this->table}` (`user_id`, `date_created`) VALUES (?, CURRENT_DATE())";
        $this->sqlExecute($sql, [$this->user_id]);
        
        $this->purgeOldRecords(); // if successful, purge old records
    } // end addRecord()

    /**
     * Remove old log records
     *
     * @return void
     */
    private function purgeOldRecords()
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `date_created` < NOW() - INTERVAL 2 DAY";
        $this->sqlExecute($sql, []);
    } // end purgeOldRecords()
}
