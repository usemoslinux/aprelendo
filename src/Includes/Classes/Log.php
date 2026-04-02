<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

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
    } 

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
    } 

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
    } 

    /**
     * Remove old log records
     *
     * @return void
     */
    private function purgeOldRecords()
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `date_created` < NOW() - INTERVAL 2 DAY";
        $this->sqlExecute($sql, []);
    } 
}
