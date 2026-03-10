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
    } // end buildSortSQL

    /**
     * Converts filter patterns selected by user (expressed as an integer value in the filter menu)
     * to valid SQL strings
     *
     * @return string
     */
    public function buildFilterTypeSQL(): string
    {
        return $this->filter_type == 0 ? '>= ?' : '= ?';
    } // end buildFilterTypeSQL()

    /**
     * Converts sorting patterns selected by user (expressed as an integer value in the filter menu)
     * to valid SQL strings
     *
     * @return string
     */
    public function buildFilterLevelSQL(): string
    {
        return $this->filter_level == 0 ? '>= ?' : '= ?';
    } // end buildFilterLevelSQL()
}
