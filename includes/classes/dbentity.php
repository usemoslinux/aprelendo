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
    } // end __construct()

    /**
     * Executes custom SQL
     *
     * @param string $sql sql statement to execute
     * @param array $values value parameters passed to PDO
     * @return void
     */
    protected function sqlExecute(string $sql, array $values): void
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
        } catch (\PDOException $e) {
            throw new InternalException("SQL error in {$this->table} table.");
        } finally {
            $stmt = null;
        }
    } // end sqlExecute()

    /**
     * Executes custom SQL fetch
     *
     * @param string $sql sql statement to execute
     * @param array $values value parameters passed to PDO
     * @return array
     */
    protected function sqlFetch(string $sql, array $values): array
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
    } // end sqlFetch()

    /**
     * Executes custom SQL fetchAll
     *
     * @param string $sql sql statement to execute
     * @param array $values value parameters passed to PDO
     * @return array
     */
    protected function sqlFetchAll(string $sql, array $values): array
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
    } // end sqlFetchAll()

    /**
     * Executes custom SQL count
     *
     * @param array $where list of columns and values to include in where clause
     * @return int
     */
    protected function sqlCount(string $sql, array $values): int
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
    } // end sqlCount()
}
