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

class Url {
    
    /**
     * Extracts domain name from url
     *
     * @param string $url
     * @return string
     */
    public static function getDomainName(string $url): string { 
        if (!isset($url) || empty($url)) {
            return '';
        }
        
        $parseUrl = parse_url(trim($url));
        
        if (!isset($parseUrl['host']) || !isset($parseUrl['path'])) {
            return '';
        } 

        return trim($parseUrl['host'] ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2))); 
    } // end getDomainName()
}

?>