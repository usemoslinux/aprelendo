<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

abstract class SearchParameters
{
    public string $search_text;
    public int $offset;
    public int $limit;
    public int $sort_by;

    /**
     * Constructor
     *
     * @param string $search_text
     * @param int $offset
     * @param int $limit
     * @param int $sort_by
     */
    public function __construct(
        string $search_text,
        int $offset,
        int $limit,
        int $sort_by
    ) {
        $this->search_text = $search_text;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->sort_by = $sort_by;
    }
}
