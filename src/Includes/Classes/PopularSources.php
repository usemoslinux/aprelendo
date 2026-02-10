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
    public const SHOW_COUNT = 50;
    private const INVALID_SOURCES = [
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
        'm.youtube.com',
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
    private const INVALID_EXTENSIONS = [
        // document formats and archives
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

        try {
            $this->pdo->beginTransaction();

            // Delete if it's currently at 1 use (or safety check for <= 1)
            $sql_delete = "DELETE FROM `{$this->table}` WHERE `lang_iso`=? AND `domain`=? AND `times_used` <= 1";
            $this->sqlExecute($sql_delete, [$lg_iso, $domain]);

            // Decrement the rest
            $sql_update = "UPDATE `{$this->table}` SET `times_used`=`times_used` - 1 WHERE `lang_iso`=? AND `domain`=?";
            $this->sqlExecute($sql_update, [$lg_iso, $domain]);

            $this->pdo->commit();
        } catch (\Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw new InternalException('Could not update popular source usage.');
        }
    }

    /**
     * Rebuilds the popular sources table from shared texts.
     *
     * @return void
     */
    public function rebuild(): void
    {
        $sql_insert = $this->buildRebuildInsertSql();
        $sql_delete = "DELETE FROM `{$this->table}`";

        try {
            $this->pdo->beginTransaction();
            $this->sqlExecute($sql_delete);
            $this->sqlExecute($sql_insert);
            $this->pdo->commit();
        } catch (\Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw new InternalException('Could not rebuild popular sources table.');
        }
    }

    /**
     * Builds the insert query used to rebuild the popular sources table.
     *
     * @return string
     */
    private function buildRebuildInsertSql(): string
    {
        $invalid_sources_sql = $this->buildCteRows(self::INVALID_SOURCES, 'domain');
        $invalid_extensions_sql = $this->buildCteRows(self::INVALID_EXTENSIONS, 'ext');

        return <<<SQL
            INSERT INTO `{$this->table}` (`lang_iso`, `domain`, `times_used`)
            WITH
            invalid_sources AS (
                {$invalid_sources_sql}
            ),
            invalid_extensions AS (
                {$invalid_extensions_sql}
            ),
            normalized_rows AS (
                SELECT
                    l.name AS lang_iso,
                    REGEXP_REPLACE(
                        SUBSTRING_INDEX(
                            REGEXP_SUBSTR(
                                REGEXP_REPLACE(LOWER(TRIM(st.source_uri)), '^https?://', ''),
                                '^[^/?#]+'
                            ),
                            ':',
                            1
                        ),
                        '^www[0-9]*\\.',
                        ''
                    ) AS domain
                FROM shared_texts st
                INNER JOIN languages l
                    ON l.id = st.lang_id
                WHERE st.source_uri IS NOT NULL
                AND TRIM(st.source_uri) <> ''
            ),
            filtered_rows AS (
                SELECT
                    n.lang_iso,
                    n.domain
                FROM normalized_rows n
                LEFT JOIN invalid_extensions ie
                    ON ie.ext = SUBSTRING_INDEX(n.domain, '.', -1)
                WHERE n.domain IS NOT NULL
                AND n.domain <> ''
                AND ie.ext IS NULL
                AND NOT EXISTS (
                    SELECT 1
                    FROM invalid_sources i
                    WHERE n.domain = i.domain
                        OR n.domain LIKE CONCAT('%.', i.domain)
                )
            )
            SELECT
                lang_iso,
                domain,
                COUNT(*) AS times_used
            FROM filtered_rows
            GROUP BY lang_iso, domain
            SQL;
    }

    /**
     * Builds SQL rows for CTE value lists.
     *
     * @param array $values
     * @param string $column_name
     * @return string
     */
    private function buildCteRows(array $values, string $column_name): string
    {
        $cte_rows = [];

        foreach ($values as $value) {
            $escaped_value = str_replace("'", "''", (string)$value);
            $cte_rows[] = "SELECT '{$escaped_value}'";
        }

        if (empty($cte_rows)) {
            return "SELECT '' AS {$column_name}";
        }

        $cte_rows[0] .= " AS {$column_name}";

        return implode(" UNION ALL\n                ", $cte_rows);
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

        $show_count = (int)self::SHOW_COUNT;
        $sql = "SELECT * FROM `{$this->table}` WHERE `lang_iso`=? ORDER BY `times_used` DESC LIMIT {$show_count}";

        return $this->sqlFetchAll($sql, [$lg_iso]);
    } // end getAllByLang()

    /**
     * Counts shared video texts for a given language.
     *
     * All shared texts with type = 5 are considered YouTube sources.
     *
     * @param string $lg_iso
     * @return int
     */
    public function getYoutubeVideoCountByLang(string $lg_iso): int
    {
        if (!isset($lg_iso) || empty($lg_iso)) {
            throw new UserException('Wrong parameters provided to get YouTube video count in the popular sources list.');
        }

        $sql = "SELECT COUNT(st.`id`)
                FROM `shared_texts` st
                INNER JOIN `languages` lg ON lg.`id` = st.`lang_id`
                WHERE lg.`name` = ?
                AND st.`type` = 5";

        return $this->sqlCount($sql, [$lg_iso]);
    }

    /**
     * Returns shared text insights for a specific language.
     *
     * @param string $lg_iso
     * @return array
     */
    public function getInsightsByLang(string $lg_iso): array
    {
        if (!isset($lg_iso) || empty($lg_iso)) {
            throw new UserException('Wrong parameters provided to get language insights in the popular sources list.');
        }

        $sql = "SELECT st.`source_uri`, st.`type`, st.`level`, st.`audio_uri`, st.`word_count`
                FROM `shared_texts` st
                INNER JOIN `languages` lg ON lg.`id` = st.`lang_id`
                WHERE lg.`name` = ?";
        $rows = $this->sqlFetchAll($sql, [$lg_iso]);

        $type_names = $this->getSharedTypeNames();
        $level_names = [1 => 'Beginner', 2 => 'Intermediate', 3 => 'Advanced'];
        $type_counts = [];
        $level_counts = [1 => 0, 2 => 0, 3 => 0];
        $domain_metrics = [];
        $texts_with_audio = 0;
        $total_word_count = 0;
        $total_texts_with_words = 0;
        $total_texts = count($rows);

        foreach ($rows as $row) {
            $type_id = (int)$row['type'];
            $level_id = (int)$row['level'];
            $audio_uri = trim((string)($row['audio_uri'] ?? ''));
            $word_count = (int)($row['word_count'] ?? 0);

            if (isset($type_names[$type_id])) {
                $type_counts[$type_id] = ($type_counts[$type_id] ?? 0) + 1;
            }

            if (isset($level_counts[$level_id])) {
                $level_counts[$level_id]++;
            }

            if ($audio_uri !== '') {
                $texts_with_audio++;
            }

            if ($word_count > 0) {
                $total_word_count += $word_count;
                $total_texts_with_words++;
            }

            $source_uri = (string)($row['source_uri'] ?? '');
            $domain = $this->normalizeDomain($source_uri);

            if ($domain === '') {
                continue;
            }

            if (!isset($domain_metrics[$domain])) {
                $domain_metrics[$domain] = [
                    'text_count' => 0,
                    'type_counts' => [],
                    'level_counts' => [1 => 0, 2 => 0, 3 => 0],
                    'audio_count' => 0
                ];
            }

            $domain_metrics[$domain]['text_count']++;
            $domain_metrics[$domain]['type_counts'][$type_id] = ($domain_metrics[$domain]['type_counts'][$type_id] ?? 0) + 1;

            if (isset($domain_metrics[$domain]['level_counts'][$level_id])) {
                $domain_metrics[$domain]['level_counts'][$level_id]++;
            }

            if ($audio_uri !== '') {
                $domain_metrics[$domain]['audio_count']++;
            }
        }

        $type_distribution = [];
        foreach ($type_names as $type_id => $type_name) {
            $count = $type_counts[$type_id] ?? 0;
            $percentage = $total_texts > 0 ? round(($count / $total_texts) * 100, 1) : 0.0;

            $type_distribution[] = [
                'label' => $type_name,
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        $level_distribution = [];
        foreach ($level_names as $level_id => $level_name) {
            $count = $level_counts[$level_id] ?? 0;
            $percentage = $total_texts > 0 ? round(($count / $total_texts) * 100, 1) : 0.0;

            $level_distribution[] = [
                'label' => $level_name,
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        $domain_insights = [];
        foreach ($domain_metrics as $domain => $domain_metric) {
            $domain_text_count = (int)$domain_metric['text_count'];
            $dominant_type_id = $this->getDominantTypeId($domain_metric['type_counts']);
            $beginner_share = $domain_text_count > 0
                ? (int)round((($domain_metric['level_counts'][1] ?? 0) / $domain_text_count) * 100)
                : 0;
            $intermediate_share = $domain_text_count > 0
                ? (int)round((($domain_metric['level_counts'][2] ?? 0) / $domain_text_count) * 100)
                : 0;
            $advanced_share = $domain_text_count > 0
                ? (int)round((($domain_metric['level_counts'][3] ?? 0) / $domain_text_count) * 100)
                : 0;

            $domain_insights[$domain] = [
                'dominant_type' => $type_names[$dominant_type_id] ?? 'Mixed',
                'level_shares' => [
                    'beginner' => $beginner_share,
                    'intermediate' => $intermediate_share,
                    'advanced' => $advanced_share
                ],
                'audio_share' => $domain_text_count > 0
                    ? (int)round((($domain_metric['audio_count'] ?? 0) / $domain_text_count) * 100)
                    : 0
            ];
        }

        return [
            'total_texts' => $total_texts,
            'with_audio_percentage' => $total_texts > 0 ? (int)round(($texts_with_audio / $total_texts) * 100) : 0,
            'avg_word_count' => $total_texts_with_words > 0 ? (int)round($total_word_count / $total_texts_with_words) : 0,
            'type_distribution' => $type_distribution,
            'level_distribution' => $level_distribution,
            'domain_insights' => $domain_insights
        ];
    }

    /**
     * Gets the shared text type labels indexed by type id.
     *
     * @return array
     */
    private function getSharedTypeNames(): array
    {
        $sql = "SELECT `id`, `name` FROM `text_types` WHERE `is_shared` = 1 ORDER BY `id` ASC";
        $rows = $this->sqlFetchAll($sql);
        $type_names = [];

        foreach ($rows as $row) {
            $type_names[(int)$row['id']] = (string)$row['name'];
        }

        return $type_names;
    }

    /**
     * Returns the type id with the highest count.
     *
     * @param array $type_counts
     * @return int
     */
    private function getDominantTypeId(array $type_counts): int
    {
        if (empty($type_counts)) {
            return 0;
        }

        arsort($type_counts);
        $dominant_type_id = array_key_first($type_counts);

        return (int)$dominant_type_id;
    }

    /**
     * Checks if a domain or extension is on the blacklist.
     * 
     * @param string $domain
     * @return bool
     */
    private function isInvalid(string $domain): bool
    {
        $extension = pathinfo($domain, PATHINFO_EXTENSION);

        // check direct match in blacklist
        if (in_array($domain, self::INVALID_SOURCES)) {
            return true;
        }

        // check forbidden extensions
        if (in_array($extension, self::INVALID_EXTENSIONS)) {
            return true;
        }

        // catch-all for subdomains (e.g., "anything.facebook.com")
        foreach (self::INVALID_SOURCES as $bad_domain) {
            if (str_ends_with($domain, '.' . $bad_domain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalizes the domain for consistency across the database.
     * Handles protocols, www variations (www1, www2), and strips paths.
     * @param string $domain
     * @return string
     */
    private function normalizeDomain(string $domain): string
    {
        // lowercase and trim whitespace
        $domain = mb_strtolower(trim($domain));

        // remove protocol and www variants (www, www1, etc.) from the start
        $domain = preg_replace('/^https?:\/\/(www[0-9]*\.)?/', '', $domain);

        // remove everything after the first forward slash (the path)
        $domain = explode('/', $domain)[0];

        return $domain;
    }
}
