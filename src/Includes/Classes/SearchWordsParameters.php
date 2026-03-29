<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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
