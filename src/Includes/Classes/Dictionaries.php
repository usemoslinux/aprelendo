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

use Aprelendo\DBEntity;
use Aprelendo\Language;

class Dictionaries extends DBEntity
{
    private $monolingual = [];
    private $bilingual = [];
    private $visual = [];
    private $translators = [];

    /**
    * Constructor
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
                    $dictionary = $this->replaceNativeLangInURI($dictionary, $native_lang_iso, $learning_lang_iso);
                    array_push($this->bilingual, $dictionary);
                    break;
                case 3:
                    $dictionary = $this->replaceNativeLangInURI($dictionary, $native_lang_iso, $learning_lang_iso);
                    array_push($this->visual, $dictionary);
                    break;
                case 4:
                    $dictionary = $this->replaceNativeLangInURI($dictionary, $native_lang_iso, $learning_lang_iso);
                    array_push($this->translators, $dictionary);
                    break;
                default:
                    break;
            }
        }
    } // end __construct()

    private function load(string $learning_lang_iso)
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `lang_iso` = ? OR `lang_iso` = 'all' ORDER BY `name`";

        return $this->sqlFetchAll($sql, [
            $learning_lang_iso
        ]);
    }

    private function replaceNativeLangInURI(array $dictionary, string $native_lang_iso,
        string $learning_lang_iso): array
    {
        $iso_codes = Language::getIsoCodeArray();
        $dictionary['uri'] = str_replace('{native-lang-iso}', $native_lang_iso, $dictionary['uri']);
        $dictionary['uri'] = str_replace('{native-lang}', $iso_codes[$native_lang_iso], $dictionary['uri']);
        $dictionary['uri'] = str_replace('{learning-lang-iso}', $learning_lang_iso, $dictionary['uri']);
        $dictionary['uri'] = str_replace('{learning-lang}', $iso_codes[$learning_lang_iso], $dictionary['uri']);
        return $dictionary;
    }

    private function getItems(string $type, bool $iframe_support_only)
    {
        $items = $this->$type;

        return $iframe_support_only
            ? array_filter($items, function($var) { return $var['iframe_support']; })
            : $items;
    }

    public function getMonolingual(bool $iframe_support_only = false)
    {
        return $this->getItems('monolingual', $iframe_support_only);
    }

    public function getBilingual(bool $iframe_support_only = false)
    {
        return $this->getItems('bilingual', $iframe_support_only);
    }

    public function getVisual(bool $iframe_support_only = false)
    {
        return $this->getItems('visual', $iframe_support_only);
    }

    public function getTranslators(bool $iframe_support_only = false)
    {
        return $this->getItems('translators', $iframe_support_only);
    }

    public function getAll()
    {
        return array_merge($this->monolingual, $this->bilingual, $this->visual, $this->translators);
    }
    
} // end checkByType()
