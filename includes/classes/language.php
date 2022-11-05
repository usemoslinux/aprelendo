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

use Aprelendo\Includes\Classes\DBEntity;

class Language extends DBEntity
{
    private $id              = 0;
    private $name            = '';
    private $dictionary_uri  = '';
    private $translator_uri  = '';
    private $rss_feed_1_uri  = '';
    private $rss_feed_2_uri  = '';
    private $rss_feed_3_uri  = '';
    private $show_freq_words = false;
    private $level           = 0;

    private static $iso_code = array(
        'ar' => 'arabic',
        'zh' => 'chinese',
        'nl' => 'dutch',
        'en' => 'english',
        'fr' => 'french',
        'de' => 'german',
        'el' => 'greek',
        'he' => 'hebrew',
        'hi' => 'hindi',
        'it' => 'italian',
        'ja' => 'japanese',
        'ko' => 'korean',
        'pt' => 'portuguese',
        'ru' => 'russian',
        'es' => 'spanish'
        );

    /**
     * Constructor
     * Initializes class variables (id, name, etc.)
     *
     * @param \PDO $pdo
     * @param int $id
     * @param int $user_id
     */
    public function __construct(\PDO $pdo, int $user_id)
    {
        parent::__construct($pdo, $user_id);
        $this->table = 'languages';
    } // end __construct()

    /**
     * Loads Record data in object properties (looks record in db by id)
     *
     * @param int $id
     * @return void
     */
    public function loadRecord(int $id): void
    {
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `id` = ? AND `user_id` = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id, $this->user_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->id               = $row['id'];
                $this->user_id          = $row['user_id'];
                $this->name             = $row['name'];
                $this->dictionary_uri   = $row['dictionary_uri'];
                $this->translator_uri   = $row['translator_uri'];
                $this->rss_feed_1_uri   = $row['rss_feed1_uri'];
                $this->rss_feed_2_uri   = $row['rss_feed2_uri'];
                $this->rss_feed_3_uri   = $row['rss_feed3_uri'];
                $this->show_freq_words  = $row['show_freq_words'];
                $this->level            = $row['level'];
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to load this record.');
        } finally {
            $stmt = null;
        }
    } // end loadRecord()

    /**
     * Updates language settings in db
     *
     * @param array $array
     * @return void
     */
    public function editRecord(array $new_record): void
    {
        try {
            // check for errors first
            if (empty($new_record['dict-uri'])) {
                throw new \Exception('You need to specify the URL of the dictionary you want to use.');
            } elseif (strpos($new_record['dict-uri'], '%s') === false) {
                throw new \Exception("The dictionary URL needs to include the position of the lookup word or phrase. '
                    . 'For this, use '%s' (without quotation marks).");
            } elseif (empty($new_record['translator-uri'])) {
                throw new \Exception('You need to specify the URL of the translator you want to use.');
            } elseif (strpos($new_record['translator-uri'], '%s') === false) {
                throw new \Exception("The translator URL needs to include the position of the lookup word or phrase. '
                . 'For this, use '%s' (without quotation marks).");
            }

            // if everything is fine, proceed editing the record
            
            $this->dictionary_uri  = $new_record['dict-uri'];
            $this->translator_uri  = $new_record['translator-uri'];
            $this->level           = $new_record['level'];
            $this->rss_feed_1_uri  = $new_record['rss-feed1-uri'];
            $this->rss_feed_2_uri  = $new_record['rss-feed2-uri'];
            $this->rss_feed_3_uri  = $new_record['rss-feed3-uri'];
            $this->show_freq_words = $new_record['freq-list'];

            $sql = "UPDATE `{$this->table}`
                    SET `dictionary_uri`=?, `translator_uri`=?, `rss_feed1_uri`=?, `rss_feed2_uri`=?,
                        `rss_feed3_uri`=?, `show_freq_words`=?, `level`=?
                    WHERE `user_id`=? AND `id`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->dictionary_uri, $this->translator_uri, $this->rss_feed_1_uri,
                            $this->rss_feed_2_uri, $this->rss_feed_3_uri, $this->show_freq_words,
                            $this->level, $this->user_id, $this->id]);
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to edit this record.');
        } finally {
            $stmt = null;
        }
    } // end editRecord()

    /**
     * Creates & saves default preferences for user
     *
     * @param string $lang
     * @return void
     */
    public function createInitialRecordsForUser(string $native_lang): void
    {
        try {
            // create & save default language preferences for user
            foreach (self::$iso_code as $key => $value) {
                $translator_uri = 'https://translate.google.com/?hl='
                    . $native_lang
                    . '&sl='
                    . $key
                    . '&tl='
                    . $native_lang
                    . '&text=%s';
                $dictionary_uri = 'https://' . $key . '.m.wiktionary.org/wiki/%s';

                $sql = "INSERT INTO `{$this->table}` (`user_id`, `name`, `dictionary_uri`, `translator_uri`)
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$this->user_id, $key, $dictionary_uri, $translator_uri]);
            }
            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to create initial record for this user.');
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to create initial record for this user.');
        } finally {
            $stmt = null;
        }
    } // end createInitialRecordsForUser()

    /**
     * Loads Record data in object properties (looks record in db by name)
     *
     * @param string $lang
     * @return void
     */
    public function loadRecordByName(string $lang): void
    {
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `user_id`=? AND `name`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $lang]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                throw new \Exception('The record does not exist.');
            }

            $this->id               = $row['id'];
            $this->user_id          = $row['user_id'];
            $this->name             = $row['name'];
            $this->dictionary_uri   = $row['dictionary_uri'];
            $this->translator_uri   = $row['translator_uri'];
            $this->rss_feed_1_uri   = $row['rss_feed1_uri'];
            $this->rss_feed_2_uri   = $row['rss_feed2_uri'];
            $this->rss_feed_3_uri   = $row['rss_feed3_uri'];
            $this->show_freq_words  = $row['show_freq_words'];
            $this->level            = $row['level'];
        } catch (\Exception $e) {
            throw new \Exception('There was an unexpected error trying to load this record.');
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
    public static function getNameFromIso(string $iso_code): string
    {
        return self::$iso_code[$iso_code];
    } // end getNameFromIso()

    /**
     * Gives index of 639-1 iso codes in Language::$iso_codes array
     *
     * @param string $lang_name
     * @return int
     */
    public static function getIndex(string $lang_name): int
    {
        $keys = array_keys(self::$iso_code);
        $keys_count = count($keys)-1;
        for ($i=0; $i <= $keys_count; $i++) {
            if ($keys[$i] == $lang_name) {
                return $i;
            }
        }
    } // end getIndex()

    /**
     * Returns complete list of iso codes
     *
     * @return array
     */
    public static function getIsoCodeArray(): array
    {
        return self::$iso_code;
    } // end getIsoCodeArray()

    /**
     * Returns list of available languages for active user
     *
     * @return array
     */
    public function getAvailableLangs(): array
    {
        try {
            $sql = "SELECT `id`, `name` FROM `{$this->table}` WHERE `user_id`=? ORDER BY `name` ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id]);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!$result || empty($result)) {
                throw new \Exception('There was an unexpected error trying to get available languages for user.');
            }
            return $result;
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to get available languages for user.');
        } finally {
            $stmt = null;
        }
    } // end getAvailableLangs()

    /**
     * Id getter
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    } // end getId()

    /**
     * Name getter
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    } // end getName()

    /**
     * Dictionary URI getter
     *
     * @return string
     */
    public function getDictionaryUri(): string
    {
        return $this->dictionary_uri;
    } // end getDictionaryUri()

    /**
     * Translator URI getter
     *
     * @return string
     */
    public function getTranslatorUri(): string
    {
        return is_null($this->translator_uri) ? '' : $this->translator_uri;
    } // end getTranslatorUri()

    /**
     * RSS Feed 1 URI getter
     *
     * @return string
     */
    public function getRssFeed1Uri(): string
    {
        return is_null($this->rss_feed_1_uri) ? '' : $this->rss_feed_1_uri;
    } // end getRssFeed1Uri()

    /**
     * RSS Feed 2 URI getter
     *
     * @return string
     */
    public function getRssFeed2Uri(): string
    {
        return is_null($this->rss_feed_2_uri) ? '' : $this->rss_feed_2_uri;
    } // end getRssFeed2Uri()

    /**
     * RSS Feed 3 URI getter
     *
     * @return string
     */
    public function getRssFeed3Uri(): string
    {
        return is_null($this->rss_feed_3_uri) ? '' :  $this->rss_feed_3_uri;
    } // end getRssFeed3Uri()

    /**
     * Show Frequency Words getter
     *
     * @return bool
     */
    public function getShowFreqWords(): bool
    {
        return $this->show_freq_words;
    } // end getShowFreqWords()

    /**
     * Language learning level getter
     *
     * @return bool
     */
    public function getLevel(): int
    {
        return $this->level;
    } // end getShowFreqWords()

}
