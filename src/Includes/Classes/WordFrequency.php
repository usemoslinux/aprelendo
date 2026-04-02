<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class WordFrequency extends DBEntity
{
    private string $lg_iso;
    private array  $local_cache = [];

    /**
     * Constructor
     *
     * @param string $lg_iso
     */
    public function __construct(\PDO $pdo, string $lg_iso) {
        parent::__construct($pdo);
        $this->lg_iso = $lg_iso;
        $this->table = 'frequency_lists';
    }

    /**
     * Gets word frequency
     * The number stored in frequency_index column is the result of processing all the subtitles available in
     * opensubtitles (2018) for the language in question. This data was then filtered with MS Windows and LibreOffice
     * (hunspell) spellcheckers, and entries with characters strange to the language, numbers, names, etc. were all
     * removed. From that filtered list, the percentage of use of each word was then calculated. By adding these
     * percentages, it was possible to determine what percentage of a text a person can understand if he or she knows
     * that word and all the words that appear before in the list. In other words, a frequency_index of 80 means
     * that if a person knows that word and the previous ones, he or she will understand 80% of the text.
     *
     * @param string $word
     * @return int
     */
    public function get(string $word): int
    {
        $word = mb_strtolower($word);

        if (isset($this->local_cache[$word])) {
            return $this->local_cache[$word];
        }

        $cache_key = "freq_{$this->lg_iso}_" . md5($word);
        $cached_val = Cache::get($cache_key);

        if ($cached_val !== null) {
            $this->local_cache[$word] = (int)$cached_val;
            return $this->local_cache[$word];
        }

        $sql = "SELECT `frequency_index`
                FROM `{$this->table}`
                WHERE `lang_iso`=? AND `word`=?";

        $row = self::sqlFetch($sql, [$this->lg_iso, $word]);

        $val = $row ? (int)$row['frequency_index'] : 0;
        
        $this->local_cache[$word] = $val;
        Cache::set($cache_key, $val);

        return $val;
    } 

    /**
     * Gets High Frequency List for language
     *
     * @return array
     */
    public function getHighFrequencyList(): array
    {
        $cache_key = "high_freq_list_{$this->lg_iso}";
        $cached_list = Cache::get($cache_key);

        if ($cached_list !== null) {
            return $cached_list;
        }

        $sql = "SELECT `word`, `frequency_index`
                FROM `{$this->table}`
                WHERE `lang_iso`=? AND `frequency_index` < 81";
        $rows = self::sqlFetchAll($sql, [$this->lg_iso]);

        Cache::set($cache_key, $rows);

        return $rows;
    } 
}
