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

        $domain = \strtolower($domain);
        if (\in_array($domain, $invalid_sources)) {
            return true; // end execution
        }

        // escape parameters
        $lg_iso = $this->con->real_escape_string($lg_iso);
        $domain = $this->con->real_escape_string($domain);
        
        $result = $this->con->query("INSERT INTO `popularsources` (popsources_lg_iso, popsources_times_used, popsources_domain) VALUES ('$lg_iso', 1, '$domain') ON DUPLICATE KEY UPDATE popsources_times_used = popsources_times_used + 1");
        
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
        
        $result = $this->con->query("DELETE FROM `popularsources` WHERE popsources_lg_iso='$lg_iso' AND popsources_domain='$domain' AND popsources_times_used = 1");

        if ($this->con->affected_rows <= 0) {
            $result = $this->con->query("UPDATE `popularsources` SET popsources_times_used = popsources_times_used - 1 WHERE popsources_lg_iso='$lg_iso' AND popsources_domain='$domain'");
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

        $result = $this->con->query("SELECT * FROM `popularsources` WHERE popsources_lg_iso='$lg_iso' LIMIT 50");

        if (!$result) {
            return false;
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

?>