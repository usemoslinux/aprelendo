<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class SearchTextsParameters extends SearchParameters
{
    public int $filter_type;
    public int $filter_level;

    /**
     * Constructor
     *
     * @param int $filter_type: 0 = All; 1 = Article; 2 = Conversation; 3 = Letter; 4 = Lyric; 5 = YTVideo;
     * 6 = Ebook; 7 = Other
     * @param int $filter_level: 0 = All; 1 = Beginner; 2 = Intermediate; 3 = Advanced
     * @param string $search_text
     * @param int $offset
     * @param int $limit
     * @param int $sort_by
     */
    public function __construct(
        int $filter_type,
        int $filter_level,
        string $search_text,
        int $offset,
        int $limit,
        int $sort_by
    ) {
        $this->filter_level = $filter_level;
        $this->filter_type = $filter_type;
        $this->search_text = $search_text;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->sort_by = $sort_by;
    }

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the sort menu)
     * to valid SQL strings
     *
     * @return string
     */
    public function buildSortSQL(): string
    {
        return match ($this->sort_by) {
            0 => '`id` DESC', // new first
            1 => '`id`', // old first
            2 => '`total_likes` DESC', // more likes first (only for shared texts)
            3 => '`total_likes`', // less likes first (only for shared texts)
            default => '',
        };
    } 

    /**
     * Converts filter patterns selected by user (expressed as an integer value in the filter menu)
     * to valid SQL strings
     *
     * @return string
     */
    public function buildFilterTypeSQL(): string
    {
        return $this->filter_type == 0 ? '>= ?' : '= ?';
    } 

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the filter menu)
     * to valid SQL strings
     *
     * @return string
     */
    public function buildFilterLevelSQL(): string
    {
        return $this->filter_level == 0 ? '>= ?' : '= ?';
    } 
}
