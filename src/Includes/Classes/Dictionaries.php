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

use Aprelendo\SupportedLanguages;

class Dictionaries extends DBEntity
{
    private $monolingual = [];
    private $bilingual = [];
    private $visual = [];
    private $translators = [];

    /**
     * Initialize dictionary lists for the target language.
     *
     * @param \PDO $pdo
     * @param string $native_lang_iso
     * @param string $learning_lang_iso
     */
    public function __construct(\PDO $pdo, string $native_lang_iso, string $learning_lang_iso)
    {
        parent::__construct($pdo);
        $this->table = 'dictionaries';
        $dictionaries = $this->load($learning_lang_iso);

        foreach ($dictionaries as $dictionary) {
            switch ($dictionary['type']) {
                case 1:
                    array_push($this->monolingual, $dictionary);
                    break;
                case 2:
                    $dictionary = $this->bindLanguageParamsToUri($dictionary, $native_lang_iso, $learning_lang_iso);
                    array_push($this->bilingual, $dictionary);
                    break;
                case 3:
                    $dictionary = $this->bindLanguageParamsToUri($dictionary, $native_lang_iso, $learning_lang_iso);
                    array_push($this->visual, $dictionary);
                    break;
                case 4:
                    $dictionary = $this->bindLanguageParamsToUri($dictionary, $native_lang_iso, $learning_lang_iso);
                    array_push($this->translators, $dictionary);
                    break;
                default:
                    break;
            }
        }
    } // end __construct()

    /**
     * Load dictionaries matching the learning language (or all).
     *
     * @param string $learning_lang_iso
     * @return array
     */
    private function load(string $learning_lang_iso): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE FIND_IN_SET(?, `lang_iso`) > 0 OR `lang_iso` = 'all' ORDER BY `name`";

        return $this->sqlFetchAll($sql, [
            $learning_lang_iso
        ]);
    }

    /**
     * Replace URI template tokens with language names and ISO codes.
     *
     * @param array $dictionary
     * @param string $native_lang_iso
     * @param string $learning_lang_iso
     * @return array
     */
    private function bindLanguageParamsToUri(array $dictionary, string $native_lang_iso,
        string $learning_lang_iso): array
    {
        $native_lang_name = SupportedLanguages::get($native_lang_iso, 'name');
        $learning_lang_name = SupportedLanguages::get($learning_lang_iso, 'name');
        
        $dictionary['uri'] = str_replace('{native-lang-iso}', $native_lang_iso, $dictionary['uri']);
        $dictionary['uri'] = str_replace('{native-lang}', $native_lang_name, $dictionary['uri']);
        $dictionary['uri'] = str_replace('{learning-lang-iso}', $learning_lang_iso, $dictionary['uri']);
        $dictionary['uri'] = str_replace('{learning-lang}', $learning_lang_name, $dictionary['uri']);

        return $dictionary;
    }

    /**
     * Get items by type, optionally filtering by iframe support.
     *
     * @param string $type
     * @param bool $iframe_support_only
     * @return array
     */
    private function getItems(string $type, bool $iframe_support_only): array
    {
        $items = $this->$type;

        return $iframe_support_only
            ? array_filter($items, function($var) { return $var['iframe_support']; })
            : $items;
    }

    /**
     * Return monolingual dictionaries.
     *
     * @param bool $iframe_support_only
     * @return array
     */
    public function getMonolingual(bool $iframe_support_only = false): array
    {
        return $this->getItems('monolingual', $iframe_support_only);
    }

    /**
     * Return bilingual dictionaries.
     *
     * @param bool $iframe_support_only
     * @return array
     */
    public function getBilingual(bool $iframe_support_only = false): array
    {
        return $this->getItems('bilingual', $iframe_support_only);
    }

    /**
     * Return visual dictionaries.
     *
     * @param bool $iframe_support_only
     * @return array
     */
    public function getVisual(bool $iframe_support_only = false): array
    {
        return $this->getItems('visual', $iframe_support_only);
    }

    /**
     * Return translator dictionaries.
     *
     * @param bool $iframe_support_only
     * @return array
     */
    public function getTranslators(bool $iframe_support_only = false): array
    {
        return $this->getItems('translators', $iframe_support_only);
    }

    /**
     * Return all dictionaries in a single list.
     *
     * @return array
     */
    public function getAll(): array
    {
        return array_merge($this->monolingual, $this->bilingual, $this->visual, $this->translators);
    }
    
} // end checkByType()
