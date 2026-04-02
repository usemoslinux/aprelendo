<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

abstract class DBEntity
{
    protected $pdo;
    protected $table   = '';
    
    /**
     * Constructor
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    } 

    /**
     * Executes custom SQL
     *
     * @param string $sql sql statement to execute
     * @param array $values value parameters passed to PDO
     * @return void
     */
    protected function sqlExecute(string $sql, array $values = []): void
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
        } catch (\PDOException $e) {
            throw new InternalException("SQL error in {$this->table} table.");
        } finally {
            $stmt = null;
        }
    } 

    /**
     * Executes custom SQL fetch
     *
     * @param string $sql sql statement to execute
     * @param array $values value parameters passed to PDO
     * @return array
     */
    protected function sqlFetch(string $sql, array $values = []): array
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                return [];
            }

            return $row;
        } catch (\PDOException $e) {
            throw new InternalException("Error fetching row in {$this->table} table.");
        } finally {
            $stmt = null;
        }
    } 

    /**
     * Executes custom SQL fetchAll
     *
     * @param string $sql sql statement to execute
     * @param array $values value parameters passed to PDO
     * @return array
     */
    protected function sqlFetchAll(string $sql, array $values = []): array
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!$rows) {
                return [];
            }

            return $rows;
        } catch (\PDOException $e) {
            throw new InternalException("Error fetching rows in {$this->table} table.");
        } finally {
            $stmt = null;
        }
    } 

    /**
     * Returns first column of first row (typically used with COUNT sql statementscount)
     *
     * @param string $sql sql statement to execute
     * @param array $values value parameters passed to PDO
     * @return int
     */
    protected function sqlCount(string $sql, array $values = []): int
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            return 0;
        } finally {
            $stmt = null;
        }
    } 
}
