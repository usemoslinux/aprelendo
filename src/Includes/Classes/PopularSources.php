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

class PopularSources extends DBEntity
{
    /**
     * Constructor
     *
     * Sets basic variables
     *
     * @param \PDO $pdo
     * @return void
     */
    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->table = 'popular_sources';
    } // end __construct()

    /**
     * Adds a new domain to the database
     *
     * @param string $lg_iso
     * @param string $domain
     * @return void
     */
    public function add(string $lg_iso, string $domain): void
    {
        $invalid_sources = [
            'feedproxy.google.com',
            'www.youtube.com',
            'm.youtube.com',
            'youtu.be'
        ];

        if (!isset($lg_iso) || empty($lg_iso) || !isset($domain) || empty($domain)) {
            return;
        }

        $domain = mb_strtolower($domain);
        // if text belongs to an invalid source or is an ebook, avoid adding it to popular_sources table
        if (in_array($domain, $invalid_sources) || pathinfo($domain, PATHINFO_EXTENSION) === '.epub') {
            return;
        }

        $sql = "INSERT INTO `{$this->table}` (`lang_iso`, `times_used`, `domain`)
                VALUES (?, 1, ?) ON DUPLICATE KEY UPDATE `times_used` = `times_used` + 1";
        $this->sqlExecute($sql, [$lg_iso, $domain]);
    } // end add()

    /**
     * Updates existing domain in database
     *
     * @param string $lg_iso
     * @param string $domain
     * @return void
     */
    public function update(string $lg_iso, string $domain): void
    {
        if (!isset($lg_iso) || empty($lg_iso) || !isset($domain) || empty($domain)) {
            return;
        }

        $sql = "DELETE FROM `{$this->table}` WHERE `lang_iso`=? AND `domain`=? AND `times_used` = 1";
        $this->sqlExecute($sql, [$lg_iso, $domain]);
                
        $sql = "UPDATE `{$this->table}` SET `times_used`=`times_used` - 1 WHERE `lang_iso`=? AND `domain`=?";
        $this->sqlExecute($sql, [$lg_iso, $domain]);
    } // end update()

    /**
     * Get all rows for a given language
     *
     * @param string $lg_iso
     * @return array
     */
    public function getAllByLang(string $lg_iso): array
    {
        if (!isset($lg_iso) || empty($lg_iso)) {
            throw new UserException('Wrong parameters provided to update record in the popular sources list.');
        }

        $sql = "SELECT * FROM `{$this->table}` WHERE `lang_iso`=? ORDER BY `times_used` DESC LIMIT 50";
        
        return $this->sqlFetchAll($sql, [$lg_iso]);
    } // end getAllByLang()
}
