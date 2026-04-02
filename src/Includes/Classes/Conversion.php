<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

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
    } 

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
    } 

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
