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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Aprelendo;

class Language extends DBEntity
{
    public int $id                      = 0;
    public int $user_id                 = 0;
    public string $name                 = '';
    public string $dictionary_uri       = '';
    public string $img_dictionary_uri   = '';
    public string $translator_uri       = '';
    public ?string $rss_feed1_uri       = null;
    public ?string $rss_feed2_uri       = null;
    public ?string $rss_feed3_uri       = null;
    public bool $show_freq_words        = false;
    public int $level                   = 0;

    private static $iso_code = [
        'ar' => 'arabic',
        'bg' => 'bulgarian',
        'ca' => 'catalan',
        'cs' => 'czech',
        'da' => 'danish',
        'de' => 'german',
        'el' => 'greek',
        'en' => 'english',
        'es' => 'spanish',
        'fr' => 'french',
        'he' => 'hebrew',
        'hi' => 'hindi',
        'hr' => 'croatian',
        'hu' => 'hungarian',
        'it' => 'italian',
        'ja' => 'japanese',
        'ko' => 'korean',
        'nl' => 'dutch',
        'no' => 'norwegian',
        'pl' => 'polish',
        'pt' => 'portuguese',
        'ro' => 'romanian',
        'ru' => 'russian',
        'sk' => 'slovak',
        'sl' => 'slovenian',
        'sv' => 'swedish',
        'tr' => 'turkish',
        'vi' => 'vietnamese',
        'zh' => 'chinese'
    ];

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
        parent::__construct($pdo);
        $this->table   = 'languages';
        $this->user_id = $user_id;
    } // end __construct()

    /**
     * Loads record data in object
     *
     * @param array $record
     * @return void
     */
    private function loadRecord(array $record): void
    {
        if ($record) {
            $this->id                 = $record['id'];
            $this->user_id            = $record['user_id'];
            $this->name               = $record['name'];
            $this->dictionary_uri     = $record['dictionary_uri'];
            $this->img_dictionary_uri = $record['img_dictionary_uri'];
            $this->translator_uri     = $record['translator_uri'];
            $this->rss_feed1_uri      = $this->setUri($record['rss_feed1_uri']);
            $this->rss_feed2_uri      = $this->setUri($record['rss_feed2_uri']);
            $this->rss_feed3_uri      = $this->setUri($record['rss_feed3_uri']);
            $this->show_freq_words    = $record['show_freq_words'];
            $this->level              = $record['level'];
        }
    } // end loadRecord()

    /**
     * Loads record data in object properties by id
     *
     * @param int $id
     * @return void
     */
    public function loadRecordById(int $id): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `id` = ?";
        $this->loadRecord($this->sqlFetch($sql, [$id]));
    } // end loadRecord()

    /**
     * Loads record data in object properties by name
     *
     * @param string $lang
     * @return void
     */
    public function loadRecordByName(string $lang): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `user_id`=? AND `name`=?";
        $this->loadRecord($this->sqlFetch($sql, [$this->user_id, $lang]));
    } // end loadRecordByName()

    /**
     * Updates language settings in db
     *
     * @param array $array
     * @return void
     */
    public function editRecord(array $new_record): void
    {
        // check for errors first
        if (empty($new_record['dict-uri'])) {
            throw new UserException('You need to specify the URL of the dictionary you want to use.');
        } elseif (strpos($new_record['dict-uri'], '%s') === false) {
            throw new UserException("The dictionary URL needs to include the position of the lookup word '
            . 'or phrase. For this, use '%s' (without quotation marks).");
        } elseif (empty($new_record['translator-uri'])) {
            throw new UserException('You need to specify the URL of the translator you want to use.');
        } elseif (strpos($new_record['translator-uri'], '%s') === false) {
            throw new UserException("The translator URL needs to include the position of the lookup word '
            . 'or phrase. For this, use '%s' (without quotation marks).");
        }

        // if everything is fine, proceed editing the record

        $this->dictionary_uri     = $new_record['dict-uri'];
        $this->img_dictionary_uri = $new_record['img-dict-uri'];
        $this->translator_uri     = $new_record['translator-uri'];
        $this->level              = $new_record['level'];
        $this->rss_feed1_uri      = $this->setUri($new_record['rss-feed1-uri']);
        $this->rss_feed2_uri      = $this->setUri($new_record['rss-feed2-uri']);
        $this->rss_feed3_uri      = $this->setUri($new_record['rss-feed3-uri']);
        $this->show_freq_words    = (bool)$new_record['show-freq-words'];

        $sql = "UPDATE `{$this->table}`
                SET `dictionary_uri`=?, `img_dictionary_uri`=?, `translator_uri`=?, `rss_feed1_uri`=?,
                    `rss_feed2_uri`=?, `rss_feed3_uri`=?, `show_freq_words`=?, `level`=?
                WHERE `user_id`=? AND `id`=?";
        $this->sqlExecute($sql, [
            $this->dictionary_uri, $this->img_dictionary_uri, $this->translator_uri, $this->rss_feed1_uri,
            $this->rss_feed2_uri, $this->rss_feed3_uri, (int)$this->show_freq_words, $this->level,
            $this->user_id, $this->id
        ]);
    } // end editRecord()

    /**
     * Creates & saves default preferences for user
     *
     * @param string $lang
     * @return void
     */
    public function createInitialRecordsForUser(string $native_lang): void
    {
        // create & save default language preferences for user
        foreach (self::$iso_code as $key => $value) {
            // 'NB' (Norwegian Bokmål) is more specific than 'NO' (general Norwegian).
            // Bing and MS Translator use 'NB', while Wikipedia redirects 'NB' to 'NO'.
            $uri_key = $key == 'no' ? 'nb' : $key;

            $translator_uri     = 'https://www.bing.com/translator/?from='
                . $uri_key
                . '&to='
                . $native_lang
                . '&text=%s'
                . '&setLang='
                . $native_lang;
            $dictionary_uri     = 'https://' . $uri_key . '.m.wiktionary.org/wiki/%s';
            $img_dictionary_uri = 'https://www.bing.com/images/search?q=%s&setLang=' . $uri_key;

            $sql = "INSERT INTO `{$this->table}` (`user_id`, `name`, `dictionary_uri`,
                    `img_dictionary_uri`, `translator_uri`)
                    VALUES (?, ?, ?, ?, ?)";
            $this->sqlExecute($sql, [$this->user_id, $key, $dictionary_uri, $img_dictionary_uri, $translator_uri]);
        }
    } // end createInitialRecordsForUser()

    /**
     * Converts 639-1 iso codes to full language names (ie. 'en' => 'English')
     *
     * @param string $iso_code
     * @return string
     */
    public static function getNameFromIso(string $iso_code): string
    {
        return array_key_exists($iso_code, self::$iso_code) ? self::$iso_code[$iso_code] : '';
    } // end getNameFromIso()

    /**
     * Returns the index of an ISO 639-1 code in Language::$iso_codes array
     *
     * @param string $lang_name
     * @return int|null Index if found, null otherwise
     */
    public static function getIndex(string $lang_name): ?int
    {
        $keys = array_keys(self::$iso_code);
        $index = array_search($lang_name, $keys, true);

        return $index !== false ? $index : null;
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
        $sql = "SELECT `id`, `name` FROM `{$this->table}` WHERE `user_id`=? ORDER BY `name` ASC";
        return $this->sqlFetchAll($sql, [$this->user_id]);
    } // end getAvailableLangs()

    /**
     * Save URI as string, even if null
     *
     * @return string
     */
    private function setUri(?string $uri): string
    {
        return is_null($uri) ? '' : $uri;
    } // end setUri()
}
