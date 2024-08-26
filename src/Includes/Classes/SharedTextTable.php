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

class SharedTextTable extends TextTable
{
    /**
     * Constructor
     *
     * @param array $rows
     */
    public function __construct(array $rows)
    {
        parent::__construct($rows, false);
        $this->headings = ['Title'];
        $this->col_widths = ['69px', ''];
        $this->action_menu = [];
        $this->sort_menu = [
            'mSortByNew' => 'New first',
            'mSortByOld' => 'Old first',
            'mSortByMoreLikes' => 'More likes first',
            'mSortByLessLikes' => 'Less likes first'
        ];
        $this->is_shared = true;
        $this->has_chkbox = false;
    } // end __construct()
}
