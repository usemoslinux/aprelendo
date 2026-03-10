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

class SearchWordsParameters extends SearchParameters
{
    /**
     * Constructor
     *
     * @param string $text
     */
    public function __construct(
        string $search_text,
        int $sort_by,
        int $offset = 0,
        int $limit = 1000000
    ) {
        parent::__construct($search_text, $offset, $limit, $sort_by);
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
            2 => '`status`', // learned first
            3 => '`status` DESC', // learning first
            10 => '`is_phrase` DESC', // words first
            11 => '`is_phrase`', // phrases first
            default => '',
        };
    } // end buildSortSQL()
}
