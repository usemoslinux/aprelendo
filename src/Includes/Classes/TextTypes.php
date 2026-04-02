<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

use PDO;

class TextTypes extends DBEntity
{
    private $type_all = [
        'id' => 0,
        'name' => 'All',
        'Description' => 'All',
        'is_private' => 1,
        'is_shared' => 1,
        'icon_html' => ''
    ];

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->table = 'text_types';
    }

    public function getById(int $id) {
        $cache_key = "text_type_{$id}";
        $cached = Cache::get($cache_key);

        if ($cached !== null) {
            return $cached;
        }

        $sql = "SELECT * FROM `{$this->table}` WHERE `id` = ?";
        $row = $this->sqlFetch($sql, [$id]);

        Cache::set($cache_key, $row);

        return $row;
    } // getById()

    public function getAll(?bool $is_shared = null): array
    {
        $is_shared_str = ($is_shared === null) ? 'null' : (string)(int)$is_shared;
        $cache_key = "text_types_all_{$is_shared_str}";
        $cached = Cache::get($cache_key);

        if ($cached !== null) {
            return $cached;
        }

        $filter_sql = '';

        if ($is_shared !== null) {
            $filter_sql = $is_shared ? 'WHERE `is_shared` = 1' : 'WHERE `is_private` = 1';
        }

        $sql = "SELECT * FROM `{$this->table}` $filter_sql ORDER BY `id` ASC";
        $rows = array_merge([$this->type_all], $this->sqlFetchAll($sql));

        Cache::set($cache_key, $rows);

        return $rows;
    } 
}
