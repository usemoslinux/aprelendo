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
    } // end getAll()
}
