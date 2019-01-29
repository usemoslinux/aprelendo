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

use Exception;
use mysqli;

class Connect 
{
    private $host, $user, $password, $db, $charset;
    
    /**
     * Constructor
     * 
     * Sets basic variables for all database connections
     * 
     */
    public function __construct() {
        $this->host = DB_SERVER;
        $this->user = DB_USER;
        $this->password = DB_PASSWORD;
        $this->db = DB_NAME;
        $this->charset = DB_CHARSET;
    }

    /**
     * Connects to database using parameters passed to the constructor
     *
     * @return mysqli_connect
     */
    public function connect() {
        $con = new  mysqli($this->host, $this->user, $this->password, $this->db);
        
        if ($con->connect_errno) {
            // error_log('MySQL Error: ' . $con->connect_error, 3, PUBLIC_PATH . 'errors.log');
            throw new Exception('Unable to connect to database!');
        } 

        $con->set_charset($this->charset);

        return $con;
    }
}

?>