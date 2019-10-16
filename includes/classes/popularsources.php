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

use Aprelendo\Includes\Classes\DBEntity;
use Aprelendo\Includes\Classes\Languages;

class PopularSources extends DBEntity {
    
    /**
     * Constructor
     * 
     * Sets basic variables
     *
     * @param mysqli_connect $con
     * @return void
     */
    public function __construct($con) {
        $this->con = $con;
        $this->table = 'popular_sources';
    }

    /**
     * Adds a new domain to the database
     *
     * @param string $lg_iso
     * @param string $domain
     * @return boolean
     */
    public function add($lg_iso, $domain) {
        $invalid_sources = array('feedproxy.google.com',
                                 'www.youtube.com',
                                 'm.youtube.com',
                                 'youtu.be');

        if (!isset($lg_iso) || empty($lg_iso) || !isset($domain) || empty($domain))  {
            return true; // end execution
        }

        $domain = strtolower($domain);
        // if text belongs to an invalid source or is an ebook, avoid adding it to popular_sources table
        if (in_array($domain, $invalid_sources) || pathinfo($domain, PATHINFO_EXTENSION) === '.epub') {
            return true; // end execution
        }

        $sql = "INSERT INTO `{$this->table}` (`lang_iso`, `times_used`, `domain`) VALUES (?, 1, ?) ON DUPLICATE KEY UPDATE `times_used` = `times_used` + 1";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("ss", $lg_iso, $domain);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Updates existing domain in database
     *
     * @param string $lg_iso
     * @param string $domain
     * @return boolean
     */
    public function update($lg_iso, $domain) {
        if (!isset($lg_iso) || empty($lg_iso) || !isset($domain) || empty($domain))  {
            return true; // end execution
        }

        $sql = "DELETE FROM `{$this->table}` WHERE `lang_iso`=? AND `domain`=? AND `times_used` = 1";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("ss", $lg_iso, $domain);
        $result = $stmt->execute();
                
        if ($stmt->affected_rows <= 0) {
            $sql = "UPDATE `{$this->table}` SET `times_used`=`times_used` - 1 WHERE `lang_iso`=? AND `domain`=?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("ss", $lg_iso, $domain);
            $result = $stmt->execute();
        }
        
        $stmt->close();

        return $result;
    }

    /**
     * Get all rows for a given language
     *
     * @param string $lg_iso
     * @return array
     */
    public function getAllByLang($lg_iso) {
        // escape parameters
        $lg_iso = $this->con->real_escape_string($lg_iso);
        
        if (!isset($lg_iso) || empty($lg_iso))  {
            return false; // return error
        }

        $sql = "SELECT * FROM `{$this->table}` WHERE `lang_iso`=? ORDER BY `times_used` DESC LIMIT 50";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("s", $lg_iso);
        $stmt->execute();
        $result = $stmt->get_result();
                
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : false;
    }
}

?>