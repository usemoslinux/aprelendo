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
    public $show_freq_words;

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
    public function __construct ($con) {
        $this->con = $con;
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
        $dictionary_uri = $array['dict-uri'];
        $translator_uri = $array['translator-uri'];
        
        if ($is_premium_user) {
            $rss_feed_1_uri = $array['rss-feed1-uri'];
            $rss_feed_2_uri = $array['rss-feed2-uri'];
            $rss_feed_3_uri = $array['rss-feed3-uri'];
            $show_freq_words = $array['freq-list'];

            $sql = "UPDATE `languages` SET `name`=?, `dict_uri`=?, `translator_uri`=?, `rss_feed1_uri`=?, `rss_feed2_uri`=?, 
                `rss_feed3_uri`=?, `show_freq_words`=? WHERE `user_id`=? AND `id`=?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("ssssssiss", $name, $dictionary_uri, $translator_uri, $rss_feed_1_uri, $rss_feed_2_uri, $rss_feed_3_uri, 
                $show_freq_words, $user_id, $id);
        } else {
            $sql = "UPDATE `languages` SET `name`=?, `dict_uri`=?, `translator_uri`=? WHERE `user_id`=? AND `id`=?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("sssss", $name, $dictionary_uri, $translator_uri, $user_id, $id);
        }
        
        $result = $stmt->execute();
        $stmt->close();
                
        return $result;
    }

    /**
     * Gets language by Id
     *
     * @param integer $id
     * @return array
     */
    public function get($id) {
        $sql = "SELECT * FROM `languages` WHERE `id` = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();
        $result = $stmt->bind_result(
            $this->id, 
            $this->user_id, 
            $this->name, 
            $this->dictionary_uri, 
            $this->translator_uri, 
            $this->rss_feed_1_uri, 
            $this->rss_feed_2_uri, 
            $this->rss_feed_3_uri, 
            $this->show_freq_words
        );

        $stmt->fetch();
        
        return $result ? true : false;
    }

    /**
     * Converts 639-1 iso codes to full language names (ie. 'en' => 'English')
     *
     * @param string $iso_code
     * @return string
     */
    public static function getLanguageName($iso_code) {
        return self::$lg_iso_codes[$iso_code];
    }
}


?>