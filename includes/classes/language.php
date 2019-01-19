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

class Language
{
    public $con;
    public $id;
    public $user_id;
    
    public $name;
    public $dictionary_uri;
    public $translator_uri;
    public $rss_feed_1_uri;
    public $rss_feed_2_uri;
    public $rss_feed_3_uri;
    public $show_freq_list;

    public static $lg_iso_codes = array(  
        'en' => 'english',
        'es' => 'spanish',
        'pt' => 'portuguese',
        'fr' => 'french',
        'it' => 'italian',
        'de' => 'german');

    /**
     * Constructor
     * Initializes class variables (id, name, etc.)
     *
     * @param mysqli_connect $con
     * @param integer $id
     * @param integer $user_id
     */
    public function __construct ($con, $id, $user_id) {
        $result = $con->query("SELECT * FROM languages WHERE LgID='$id'");
        if ($result) {
            $row = $result->fetch_assoc();
            
            $this->con = $con;
            $this->id = $con->real_escape_string($id);
            $this->user_id = $con->real_escape_string($user_id);
            $this->name = $con->real_escape_string($row['LgName']);
            $this->dictionary_uri = $con->real_escape_string($row['LgDict1URI']);
            $this->translator_uri = $con->real_escape_string($row['LgTranslatorURI']);
            $this->rss_feed_1_uri = $con->real_escape_string($row['LgRSSFeed1URI']);
            $this->rss_feed_2_uri = $con->real_escape_string($row['LgRSSFeed2URI']);
            $this->rss_feed_3_uri = $con->real_escape_string($row['LgRSSFeed3URI']);
            $this->show_freq_list = $con->real_escape_string($row['LgShowFreqList']);
        }
    }

    /**
     * Updates language settings in db
     *
     * @param array $array
     * @return boolean
     */
    public function edit($array, $is_premium_user) {
        $id = $this->id;
        $user_id = $this->user_id;
        $name = $this->name;
        $dictionary_uri = $array['dictionaryURI'];
        $translator_uri = $array['translatorURI'];
        
        if ($is_premium_user) {
            $rss_feed_1_uri = $array['rssfeedURI1'];
            $rss_feed_2_uri = $array['rssfeedURI2'];
            $rss_feed_3_uri = $array['rssfeedURI3'];
            $show_freq_list = $array['freq-list'];
            
            $sql_str = "UPDATE languages SET LgName='$name', LgDict1URI='$dictionary_uri',
                LgTranslatorURI='$translator_uri', LgRSSFeed1URI='$rss_feed_1_uri', LgRSSFeed2URI='$rss_feed_2_uri', 
                LgRSSFeed3URI='$rss_feed_3_uri', LgShowFreqList=$show_freq_list WHERE LgUserId='$user_id' AND LgID='$id'";
        } else {
            $sql_str = "UPDATE languages SET LgName='$name', LgDict1URI='$dictionary_uri',
                LgTranslatorURI='$translator_uri' WHERE LgUserId='$user_id' AND LgID='$id'";
        }
        
        $result = $this->con->query($sql_str);

        return $result;
    }

    /**
     * Gets language by Id
     *
     * @param integer $id
     * @return array
     */
    public function getById($id) {
        $result = $this->con->query("SELECT * FROM languages WHERE LgUserId='$this->user_id' AND LgID = '$id'");
               
        return $result ? $result->fetch_all() : false;
    }

    /**
     * Converts full language names to 639-1 iso codes (ie. 'English' => 'en')
     *
     * @param string $iso_code
     * @return string
     */
    public static function getLanguageName($iso_code) {
        return self::$lg_iso_codes[$iso_code];
    }
}


?>