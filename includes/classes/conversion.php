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

class Conversion
{
    /**
     * Converts JSON to CSV
     *
     * @param string $json in JSON format
     * @return string in CSV format
     */
    public static function jsonToCsv(string $json)
    {
        return implode(',', json_decode($json));
    } // end jsonToCsv()

    /**
     * Converts Array to CSV
     *
     * @param array
     * @return string in CSV format
     */
    public static function arrayToCsv(array $array): string
    {
        if (is_array($array)) {
            return "'" . implode("','", $array) . "'";
        } else {
            return "'$array'";
        }
    } // end arrayToCsv()

    /**
     * Converts XML string to Array
     *
     * @param string $xmlObject
     * @return array
     */
    public static function xmlToArray(string $xmlObject): array
    {
        $out = array ();
        foreach ((array)$xmlObject as $index => $node) {
            $out[$index] = (is_object($node)) ? xmlToArray($node) : $node;
        }
        return $out;
    } // end xmlToArray()

    /**
     * Convert an array to XML
     * @param array $array
     * @param SimpleXMLElement $xml
     */
    public static function arrayToXml($array, &$xml)
    {
        foreach ($array as $key => $value) {
            if (is_int($key)) {
                $key = "e";
            }
            if (is_array($value)) {
                $label = $xml->addChild($key);
                self::arrayToXml($value, $label);
            } else {
                $xml->addChild($key, $value);
            }
        }
    }

}
