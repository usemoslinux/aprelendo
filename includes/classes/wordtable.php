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

use Aprelendo\Includes\Classes\Table;

class WordTable extends Table {
    
    /**
     * Constructor
     *
     * @param string $headings
     * @param string $col_widths
     * @param array $rows
     * @param string $action_menu HTML to create action menu
     * @param string $sort_menu HTML to create sort menu
     */
    public function __construct(array $headings, array $col_widths, array $rows, 
                                array $action_menu, array $sort_menu) {
        parent::__construct($headings, $col_widths, $rows, $action_menu, $sort_menu);
        $this->has_chkbox = true;
    }

    /**
     * Prints table content (only for Words table)
     *
     * @return string HTML for table content
     */
    protected function print_content(): string {
        $html = '';
            
        for ($i=0; $i < sizeof($this->rows); $i++) { 
            $word_id = $this->rows[$i]['id'];
            $word = $this->rows[$i]['word'];
            $word_status = $this->rows[$i]['status'];
            // $status = 0 ("learned"), 1 ("learning"), 2 ("new"), 3 ("forgotten")
            $status = array('fa-hourglass-end status_learned', 'fa-hourglass-half status_learning', 
                            'fa-hourglass-start status_new', 'fa-hourglass-start status_forgotten');
            $status_text = array('Learned', 'Learning', 'New', 'Forgotten');

            if ($this->has_chkbox) {
                $html .= "<tr><td class='col-checkbox'><div><input id='row-$word_id' class='form-check-input chkbox-selrow' type='checkbox' aria-label='Select row' data-idWord='$word_id'><label class='form-check-label' for='row-$word_id'></label></div></td>";
            } 
            
            $html .= '<td class="col-title"><a class="word word-list">' . $word . '</a></td><td class="col-status text-center">' .
                '<i title="' . $status_text[$word_status] . '" class="fas ' . $status[$word_status] . '"></i></td></tr>';
        }
        return $html;
    }
}

?>