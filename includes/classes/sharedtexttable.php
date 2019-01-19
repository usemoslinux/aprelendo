<?php 
/**
 * Copyright (C) 2018 Pablo Castagnino
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

class SharedTextTable extends TextTable {
    protected $con;
    protected $active_user_liked = [];
    protected $total_user_likes = [];

    /**
     * Constructor
     *
     * @param string $headings
     * @param string $col_widths
     * @param array $rows
     * @param string $action_menu HTML to create action menu
     * @param string $sort_menu HTML to create sort menu
     */
    public function __construct($con, $headings, $col_widths, $rows, $action_menu, $sort_menu) {
        parent::__construct($headings, $col_widths, $rows, false, $action_menu, $sort_menu);
        $this->con = $con;
        $this->is_shared = true;
        $this->has_chkbox = false;

        foreach ($rows as $row) {
            $text_id = $row[0];
            
            $result = $this->con->query("SELECT SUM(likesLiked), likesLiked FROM likes WHERE likesTextId=$text_id");
            $query_rows = $result->fetch_array(MYSQLI_NUM);

            // how many likes does this article have?
            $this->total_user_likes[] = $query_rows[0] != null ? $query_rows[0] : '0';

            // did user liked this artile ?
            if ($query_rows) {
                $this->active_user_liked[] = $query_rows[1] == 1 ? true : false;
            } else {
                $this->active_user_liked[] = false;
            }
        }
    }
}

?>