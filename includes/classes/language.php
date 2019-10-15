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

class Language extends DBEntity
{
    private $id = 0;
    private $name = '';
    private $dictionary_uri = '';
    private $translator_uri = '';
    private $rss_feed_1_uri = '';
    private $rss_feed_2_uri = '';
    private $rss_feed_3_uri = '';
    private $show_freq_words = false;

    private static $iso_code = array(  
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
    public function __construct (\PDO $con, int $user_id) {
        parent::__construct($con, $user_id);
        $this->table = 'languages';
    } // end __construct()

    /**
     * Loads language record data
     *
     * @param integer $id
     * @return array
     */
    public function loadRecord(int $id): bool {
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `id` = ? AND `user_id` = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$id, $this->user_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            $this->id               = $row['id']; 
            $this->user_id          = $row['user_id']; 
            $this->name             = $row['name'];
            $this->dictionary_uri   = $row['dictionary_uri']; 
            $this->translator_uri   = $row['translator_uri']; 
            $this->rss_feed_1_uri   = $row['rss_feed1_uri'];
            $this->rss_feed_2_uri   = $row['rss_feed2_uri'];
            $this->rss_feed_3_uri   = $row['rss_feed3_uri'];
            $this->show_freq_words  = $row['show_freq_words'];
            
            return $row ? true : false;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end loadRecord()

    /**
     * Updates language settings in db
     *
     * @param array $array
     * @return bool
     */
    public function editRecord(array $new_record, bool $is_premium_user): bool {
        try {
            $this->dictionary_uri = $new_record['dict-uri'];
            $this->translator_uri = $new_record['translator-uri'];
            
            if ($is_premium_user) {
                $this->rss_feed_1_uri = $new_record['rss-feed1-uri'];
                $this->rss_feed_2_uri = $new_record['rss-feed2-uri'];
                $this->rss_feed_3_uri = $new_record['rss-feed3-uri'];
                $this->show_freq_words = $new_record['freq-list'];
            }

            $sql = "UPDATE `{$this->table}` 
                    SET `dictionary_uri`=?, `translator_uri`=?, `rss_feed1_uri`=?, `rss_feed2_uri`=?, `rss_feed3_uri`=?, `show_freq_words`=? 
                    WHERE `user_id`=? AND `id`=?";
            $stmt = $this->con->prepare($sql);
            $result = $stmt->execute([$this->dictionary_uri, $this->translator_uri, $this->rss_feed_1_uri, 
                                      $this->rss_feed_2_uri, $this->rss_feed_3_uri, $this->show_freq_words, $this->user_id, $this->id]);
            return $result;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end editRecord()

    /**
     * Creates & saves default preferences for user
     *
     * @return bool
     */
    public function createInitialRecordsForUser(): bool {
        try {
            // create & save default language preferences for user
            foreach (self::$iso_code as $key => $value) {
                $translator_uri = 'https://translate.google.com/m?hl=' . $value . '&sl=' . self::$iso_code[$native_lang] . '&&ie=UTF-8&q=%s';
                $dictionary_uri = 'https://www.linguee.com/' . $value . '-' . self::$iso_code[$native_lang] . '/search?source=auto&query=%s';
                
                $sql = "INSERT INTO `{$this->table}` (`user_id`, `name`, `dictionary_uri`, `translator_uri`) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->con->prepare($sql);
                $stmt->execute([$this->user_id, $key, $dictionary_uri, $translator_uri]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end createInitialRecordsForUser()

    public function loadRecordByName(string $learning_lang): bool {
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `user_id`=? AND `name`=?";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $learning_lang]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            $this->id               = $row['id']; 
            $this->user_id          = $row['user_id']; 
            $this->name             = $row['name'];
            $this->dictionary_uri   = $row['dictionary_uri']; 
            $this->translator_uri   = $row['translator_uri']; 
            $this->rss_feed_1_uri   = $row['rss_feed1_uri'];
            $this->rss_feed_2_uri   = $row['rss_feed2_uri'];
            $this->rss_feed_3_uri   = $row['rss_feed3_uri'];
            $this->show_freq_words  = $row['show_freq_words'];
            
            return $row ? true : false;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end loadRecordByName()


    /**
     * Converts 639-1 iso codes to full language names (ie. 'en' => 'English')
     *
     * @param string $iso_code
     * @return string
     */
    public static function getNameFromIso($iso_code) {
        return self::$iso_code[$iso_code];
    } // end getNameFromIso()

    /**
     * Gives index of 639-1 iso codes in Language::$lg_iso_codes array
     *
     * @param string $lang_name
     * @return string
     */
    public static function getIndex($lang_name) {
        $keys = array_keys(self::$iso_codes);
        $count = key_count($keys)-1;
        for ($i=0; $i < $key_count; $i++) { 
            if ($keys[$i] == $lang_name) {
                return $i;
            }
        }
    }

    /**
     * Returns complete list of iso codes
     *
     * @return array
     */
    public static function getIsoCodeArray() {
        return self::$iso_code;
    } // end getIsoCodeArray()

    /**
     * Id getter
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    } // end getId()

    /**
     * Name getter
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    } // end getName()

    /**
     * Dictionary URI getter
     *
     * @return string
     */
    public function getDictionaryUri() {
        return $this->dictionary_uri;
    } // end getDictionaryUri()

    /**
     * Translator URI getter
     *
     * @return string
     */
    public function getTranslatorUri() {
        return $this->translator_uri;
    } // end getTranslatorUri()

    /**
     * RSS Feed 1 URI getter
     *
     * @return string
     */
    public function getRssFeed1Uri() {
        return $this->rss_feed_1_uri;
    } // end getRssFeed1Uri()

    /**
     * RSS Feed 2 URI getter
     *
     * @return string
     */
    public function getRssFeed2Uri() {
        return $this->rss_feed_2_uri;
    } // end getRssFeed2Uri()

    /**
     * RSS Feed 3 URI getter
     *
     * @return string
     */
    public function getRssFeed3Uri() {
        return $this->rss_feed_3_uri;
    } // end getRssFeed3Uri()

    /**
     * Show Frequency Words getter
     *
     * @return bool
     */
    public function getShowFreqWords() {
        return $this->show_freq_words;
    } // end getShowFreqWords()

}


?>