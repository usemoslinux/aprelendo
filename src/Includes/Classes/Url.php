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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aprelendo;

class Url
{
    public string $base_url;
    public string $search_text;
    public int $sort_by;
    public int $filter_type;
    public int $filter_level;
    public int $show_archived;
    public string $query_str;

    /**
     * Constructor
     *
     * @param string $base_url
     * @param array $query_options
     */
    public function __construct(string $base_url, array $query_options) {
        $this->base_url = $base_url;

        extract($query_options);
        $this->search_text = $search_text ?? "";
        $this->sort_by = $sort_by ?? "";
        $this->filter_type = $filter_type ?? 0;
        $this->filter_level = $filter_level ?? 0;
        $this->show_archived = $show_archived ?? -1;

        $this->query_str = $this->buildQueryStr();
    }

    /**
     * Build URL string including all query parameters
     *
     * @param array $query_options
     * @return string
     */
    private function buildQueryStr(): string {
        $search_text = urlencode($this->search_text);
        $sort_by = $this->sort_by;
        $filter_type = $this->filter_type;
        $filter_level = $this->filter_level;
        $show_archived = $this->show_archived ?? -1;
        
        if (-1 !== $this->show_archived) {
            $show_archived = $this->show_archived ? 1 : 0;
        }

        // build query string
        $s  = !empty($search_text)     ? "s=$search_text&"    : '';
        $o  = !empty($sort_by)         ? "o=$sort_by&"        : '';
        $ft = ($filter_type !== 0)     ? "ft=$filter_type&"   : '';
        $fl = ($filter_level !== 0)    ? "fl=$filter_level&"  : '';
        $sa = ($show_archived !== -1)  ? "sa=$show_archived&" : '';

        return "?$s$o$ft$fl$sa";
    }

    /**
     * Extracts domain name from url
     *
     * @param string $url
     * @return string
     */
    public static function getDomainName(string $url): string
    {
        if (!isset($url) || empty($url)) {
            return '';
        }
        
        $parseUrl = parse_url(trim($url));
        
        if (!isset($parseUrl['host']) || !isset($parseUrl['path'])) {
            return '';
        }

        return trim($parseUrl['host'] ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2)));
    } // end getDomainName()
}
