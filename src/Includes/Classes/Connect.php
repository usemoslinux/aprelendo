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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Aprelendo;

use PDO;

class Connect
{
    private $driver    = '';
    private $host      = '';
    private $user      = '';
    private $password  = '';
    private $db        = '';
    private $charset   = '';
    
    /**
     * Constructor
     *
     * Sets basic variables for all database connections
     *
     */
    public function __construct(
        string $driver,
        string $host,
        string $user,
        string $password,
        string $db_name,
        string $charset
        )
    {
        $this->driver   = $driver;
        $this->host     = $host;
        $this->user     = $user;
        $this->password = $password;
        $this->db       = $db_name;
        $this->charset  = $charset;
    } // end __construct()

    /**
     * Connects to database using parameters passed to the constructor
     *
     * @return PDO
     */
    public function connect(): \PDO
    {
        try {
            $dsn = $this->driver . ':host=' . $this->host . ';charset=' . $this->charset;
            $pdo = new \PDO($dsn, $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // make sure db exists (otherwise, create it) and select it
            $sql = "CREATE DATABASE IF NOT EXISTS `$this->db`";
            $pdo->exec($sql);
            $sql = "USE `$this->db`";
            $pdo->exec($sql);

            // make sure db tables already exist, otherwise run aprelendo-schema.sql
            // this imports table structure and relationships together with data for frequency list tables

            $db_tables = $pdo->query('SHOW TABLES')->fetchAll();

            if (!$db_tables) {
                // read SQL file
                $sql_schema_file = file_get_contents(APP_ROOT . 'config/aprelendo-schema.sql');

                // execute SQL script
                $pdo->query($sql_schema_file);
            }
        } catch (\PDOException $e) {
            throw new InternalException($e->getMessage());
        }

        return $pdo;
    }
}
