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
        $con = new mysqli($this->host, $this->user, $this->password, $this->db);
        
        if ($con->connect_errno) {
            // error_log('MySQL Error: ' . $con->connect_error, 3, PUBLIC_PATH . 'errors.log');
            throw new Exception('Unable to connect to database!');
        } 

        $con->set_charset($this->charset);

        return $con;
    }
}

class DBEntity {
    protected $con;
    protected $user_id;
    protected $table;
    
    /**
     * Constructor
     *
     * @param mysqli_connect $con
     * @param integer $user_id
     */
    public function __construct($con, $user_id) {
        $this->con = $con;
        $this->user_id = $user_id;
    }

    /**
     * Converts JSON to CSV
     *
     * @param string $json in JSON format
     * @return string in CSV format
     */
    protected function convertJSONtoCSV($json) {
        $json = json_decode($json);
        $result = implode(',', $json);
        return $this->con->real_escape_string($result);
    }

    /**
     * Converts Array to CSV
     *
     * @param array 
     * @return string in CSV format
     */
    protected function convertArraytoCSV($array) {
        if (is_array($array)) {
            // escape all array elements
            foreach ($array as $value) {
                $value = $this->con->real_escape_string($value);
            }
            
            return "'" . implode("','",$array) . "'";
        } else {
            return "'$array'";
        }
    }

    protected function xml2array($xmlObject)
    {
        $out = array ();
        foreach ( (array)$xmlObject as $index => $node ) {
            $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;
        }
        return $out;
    }

    /**
     * Builds AND/OR SQL statement by using elements in $array
     *
     * @todo it is currently unused... deperecate it?
     * 
     * @param array $array elements to stack
     * @param string $and_or 'AND' or 'OR'
     * @return string resulting SQL statement
     */
    protected function buildAndOrStatement($array, $and_or) {
        $result = '';
        foreach ($array as $element) {
            $result .= !empty($element) ? " $and_or $element" : '';
        }
        return $result;
    }

}

?>