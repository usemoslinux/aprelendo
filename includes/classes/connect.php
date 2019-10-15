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

use PDO;

class Connect 
{
    private $type, $host, $user, $password, $db, $charset;
    
    /**
     * Constructor
     * 
     * Sets basic variables for all database connections
     * 
     */
    public function __construct() {
        $this->driver = DB_DRIVER;
        $this->host = DB_SERVER;
        $this->user = DB_USER;
        $this->password = DB_PASSWORD;
        $this->db = DB_NAME;
        $this->charset = DB_CHARSET;

        $this->options = [
            PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];
    }

    /**
     * Connects to database using parameters passed to the constructor
     *
     * @return mysqli_connect
     */
    public function connect() {
        
        try {
            $dsn = $this->driver . ':host=' . $this->host . ';dbname=' . $this->db . ';charset=' . $this->charset;
            $con = new \PDO($dsn, $this->user, $this->password, $this->options);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return $con;
    }
}

?>