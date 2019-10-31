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

use Aprelendo\Includes\Classes\Connect;
use Aprelendo\Includes\Classes\DBEntity;

class WordFrequency {
    
    /** 
     * Gets word frequency
     * The number stored in frequency_index column is the result of processing all the subtitles available in opensubtitles (2018)
     * for the language in question. This data was then filtered with MS Windows and LibreOffice (hunspell) spellcheckers, and 
     * entries with characters strange to the language, numbers, names, etc. were all removed. From that filtered list, the 
     * percentage of use of each word was then calculated. By adding these percentages, it was possible to determine what 
     * percentage of a text a person can understand if he or she knows that word and all the words that appear before in the list. 
     * In other words, a frequency_index of 80 means that if a person knows that word and the previous ones, he or she will understand 
     * 80% of the text. 
     * 
     * @param string $word
     * @param string $lg_iso
     * @return int
     */
    public static function get(\PDO $pdo, string $word, string $lg_iso) {
        try {
            $table = 'frequency_list_' . $lg_iso;
            $word = mb_strtolower($word, "UTF-8");

            $sql = "SELECT COUNT(*) AS `exists` FROM {$table} WHERE word=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$word]);
            $row = $stmt->fetch();

            if (!$row || $row['exists'] == 0) {
                return 0;
            }

            $sql = "SELECT * FROM {$table} WHERE word=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$word]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                throw new \Exception('There was an unexpected error trying to load record from frequency list table.');
            }

            $word_freq = $row['frequency_index'];
            return $word_freq;
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to load record from frequency list table.');
        } finally {
            $stmt = null;
        }
    } // end get()

    /**
     * Gets High Frequency List for language
     *
     * @param string $lg_iso
     * @return array
     */
    public static function getHighFrequencyList(\PDO $pdo, string $lg_iso): array {
        try {
            $table = 'frequency_list_' . $lg_iso;
            $sql = "SELECT `word`, `frequency_index` 
                    FROM `$table`   
                    WHERE `frequency_index` < 80";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();

            if (!$result || empty($result)) {
                throw new \Exception('There was an unexpected error trying to load records from frequency list table.');
            }

            return $result;
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to load records from frequency list table.');
        } finally {
            $stmt = null;
        }
    } // end getHighFrequencyList()
}


?>