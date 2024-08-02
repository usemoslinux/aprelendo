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

abstract class SearchParameters
{
    public string $search_text;
    public int $offset;
    public int $limit;
    public int $sort_by;

    /**
     * Constructor
     *
     * @param string $text
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

