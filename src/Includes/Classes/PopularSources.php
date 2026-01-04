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
        if (empty(trim($lg_iso)) || empty(trim($domain))) {
            return;
        }

        $domain = $this->normalizeDomain($domain);

        if ($this->isInvalid($domain)) {
            return;
        }

        $sql = "INSERT INTO `{$this->table}` (`lang_iso`, `times_used`, `domain`)
                VALUES (?, 1, ?) ON DUPLICATE KEY UPDATE `times_used` = `times_used` + 1";
        $this->sqlExecute($sql, [$lg_iso, $domain]);
    }

    /**
     * Updates existing domain in database
     *
     * @param string $lg_iso
     * @param string $domain
     * @return void
     */
    public function update(string $lg_iso, string $domain): void
    {
        if (empty(trim($lg_iso)) || empty(trim($domain))) {
            return;
        }

        $domain = $this->normalizeDomain($domain);

        // Delete if it's currently at 1 use (or safety check for <= 1)
        $sql = "DELETE FROM `{$this->table}` WHERE `lang_iso`=? AND `domain`=? AND `times_used` <= 1";
        $this->sqlExecute($sql, [$lg_iso, $domain]);

        // Decrement the rest
        $sql = "UPDATE `{$this->table}` SET `times_used`=`times_used` - 1 WHERE `lang_iso`=? AND `domain`=?";
        $this->sqlExecute($sql, [$lg_iso, $domain]);
    }

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

    /**
     * Checks if a domain or extension is on the blacklist.
     * 
     * @param string $domain
     * @return bool
     */
    private function isInvalid(string $domain): bool
    {
        $invalid_sources = [
            // RSS / Search
            'feedproxy.google.com',
            'google.com',
            'bing.com',
            'yahoo.com',
            'duckduckgo.com',
            'baidu.com',
            'yandex.ru',
            // Video / Media
            'youtube.com',
            'youtu.be',
            'vimeo.com',
            'dailymotion.com',
            'twitch.tv',
            'soundcloud.com',
            // Social / Aggregators
            'facebook.com',
            'm.facebook.com',
            't.me',
            'instagram.com',
            'reddit.com',
            'pinterest.com',
            'linkedin.com',
            'tiktok.com',
            'twitter.com',
            'x.com',
            // URL Shorteners
            'bit.ly',
            't.co',
            'goo.gl',
            'tinyurl.com',
            'ow.ly',
            'is.gd',
            'buff.ly',
            'lnkd.in',
            'bit.do',
            // CDNs & Infrastructure (Usually non-content sources)
            'cloudfront.net',
            'akamaihd.net',
            'fastly.net',
            'cloudinary.com',
            'wp.com',
            's3.amazonaws.com',
            // Email & Internal
            'gmail.com',
            'outlook.com',
            'hotmail.com',
            'localhost',
            '127.0.0.1'
        ];

        // document formats and archives
        $invalid_extensions = [
            'epub',
            'pdf',
            'zip',
            'mp3',
            'mp4',
            'onion',
            'docx',
            'xlsx',
            'pptx',
            'tar',
            'gz',
            '7z',
            'rar',
            'iso'
        ];

        $extension = pathinfo($domain, PATHINFO_EXTENSION);

        // check direct match in blacklist
        if (in_array($domain, $invalid_sources)) {
            return true;
        }

        // check forbidden extensions
        if (in_array($extension, $invalid_extensions)) {
            return true;
        }

        // catch-all for subdomains (e.g., "anything.facebook.com")
        foreach ($invalid_sources as $bad_domain) {
            if (str_ends_with($domain, '.' . $bad_domain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalizes the domain for consistency across the database.
     * 
     * @param string $domin
     * @return string
     */
    private function normalizeDomain(string $domain): string
    {
        $domain = mb_strtolower(trim($domain));
        return preg_replace('/^www\./', '', $domain);
    }
}
