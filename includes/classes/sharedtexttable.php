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

namespace Aprelendo\Includes\Classes;

use Aprelendo\Includes\Classes\TextTable;

class SharedTextTable extends TextTable
{
    /**
     * Constructor
     *
     * @param int $user_id
     * @param array $headings
     * @param array $col_widths
     * @param array $rows
     * @param array $action_menu HTML to create action menu
     * @param array $sort_menu HTML to create sort menu
     */
    public function __construct(
        int $user_id,
        array $headings,
        array $col_widths,
        array $rows,
        array $action_menu,
        array $sort_menu
        ) {
        parent::__construct($headings, $col_widths, $rows, false, $action_menu, $sort_menu);
        $this->is_shared = true;
        $this->has_chkbox = false;
    } // end __construct()
}
