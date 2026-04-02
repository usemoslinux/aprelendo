<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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
            match ((int)$dictionary['type']) {
                1 => $this->monolingual[] = $dictionary,
                2 => $this->bilingual[] = $this->bindLanguageParamsToUri(
                    $dictionary,
                    $native_lang_iso,
                    $learning_lang_iso
                ),
                3 => $this->visual[] = $this->bindLanguageParamsToUri(
                    $dictionary,
                    $native_lang_iso,
                    $learning_lang_iso
                ),
                4 => $this->translators[] = $this->bindLanguageParamsToUri(
                    $dictionary,
                    $native_lang_iso,
                    $learning_lang_iso
                ),
                default => null,
            };
        }
    } 

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
    
} 
