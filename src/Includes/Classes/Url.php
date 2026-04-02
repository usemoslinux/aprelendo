<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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
        $this->sort_by = $sort_by ?? 0;
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
        $show_archived = $this->show_archived;
        
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
        
        $host = $parseUrl['host'] ?? '';
        $path = $parseUrl['path'] ?? '';

        if ($host === '' && $path === '') {
            return '';
        }

        $path_parts = explode('/', $path, 2);
        $first_path_segment = $path_parts[0] ?? '';

        return trim($host !== '' ? $host : $first_path_segment);
    } 
}
