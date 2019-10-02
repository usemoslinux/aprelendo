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

        // escape parameters
        $lg_iso = $this->con->real_escape_string($lg_iso);
        $domain = $this->con->real_escape_string($domain);
        
        $result = $this->con->query("INSERT INTO `popular_sources` (`lang_iso`, `times_used`, `domain`) VALUES ('$lg_iso', 1, '$domain') ON DUPLICATE KEY UPDATE `times_used` = `times_used` + 1");
        
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

        // escape parameters
        $lg_iso = $this->con->real_escape_string($lg_iso);
        $domain = $this->con->real_escape_string($domain);
        
        $result = $this->con->query("DELETE FROM `popular_sources` WHERE `lang_iso`='$lg_iso' AND `domain`='$domain' AND `times_used` = 1");

        if ($this->con->affected_rows <= 0) {
            $result = $this->con->query("UPDATE `popular_sources` SET `times_used`=`times_used` - 1 WHERE `lang_iso`='$lg_iso' AND `domain`='$domain'");
        }
        
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

        $result = $this->con->query("SELECT * FROM `popular_sources` WHERE `lang_iso`='$lg_iso' ORDER BY `times_used` DESC LIMIT 50");

        if (!$result) {
            return false;
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

?>