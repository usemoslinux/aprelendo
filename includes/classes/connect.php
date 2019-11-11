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

use PDO;

class Connect 
{
    private $driver    = '';
    private $host      = '';
    private $user      = '';
    private $password  = '';
    private $db        = '';
    private $charset   = '';
    private $options   = [];
    
    /**
     * Constructor
     * 
     * Sets basic variables for all database connections
     * 
     */
    public function __construct(string $driver, string $host, string $user, string $password, 
                                string $db_name, string $charset) {
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
    public function connect(): \PDO {
        
        try {
            $dsn = $this->driver . ':host=' . $this->host . ';dbname=' . $this->db . ';charset=' . $this->charset;
            $pdo = new \PDO($dsn, $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return $pdo;
    }
}

?>