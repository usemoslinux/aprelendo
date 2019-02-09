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

abstract class Table 
{
    protected $headings;
    protected $col_count;
    protected $col_widths;
    protected $rows;
    protected $action_menu;
    protected $sort_menu;
    protected $has_chkbox;

    /**
     * Constructor
     *
     * @param string $headings
     * @param string $col_widths
     * @param array $rows
     * @param string $action_menu HTML to create action menu
     * @param string $sort_menu HTML to create sort menu
     */
    public function __construct($headings, $col_widths, $rows, $action_menu, $sort_menu) {
        $this->headings = $headings;
        $this->col_count = sizeof($headings);
        $this->col_widths = $col_widths;
        $this->rows = $rows;
        $this->action_menu = $action_menu;
        $this->sort_menu = $sort_menu;
    }

    /**
     * Prints table
     *
     * @param integer $sort_by
     * @return void
     */
    public function print($sort_by) {
        $html = $this->print_header();
        $html .= $this->print_content();
        $html .= $this->print_footer($sort_by);
        return $html;
    }

    /**
     * Prints table header
     *
     * @return string HTML for table header
     */
    protected function print_header() {
        $html = '<div class="row">
            <div class="col-sm-12">
            <table id="textstable" class="table table-hover">
            <colgroup>';
        
        foreach ($this->col_widths as $col_width) { 
            $html .= "<col width='$col_width'>";
        }
        
        $html .= '</colgroup><thead><tr>';

        if ($this->has_chkbox) {
            $html .= '<th class="col-checkbox"><input id="chkbox-selall" type="checkbox"></th>';
        } else {
            $html .= '<th></th>';
        }

        foreach ($this->headings as $heading) { 
            $html .= "<th class='col-title'>$heading</th>";
        }

        $html .= '</tr></thead><tbody>';

        return $html;
    }

    /**
     * Prints table footer
     *
     * @param integer $sort_by
     * @return string HTML for table footer
     */
    protected function print_footer($sort_by) {
        $html = '</tbody></table><div class="row"><div class="col-sm-12">';

        if (!empty($this->action_menu)) {
            $html .= '<div class="dropdown d-inline-block">
            <button class="btn btn-secondary dropdown-toggle disabled" type="button" 
                id="actions-menu" data-toggle="dropdown">Actions <span class="caret"></span></button><div class="dropdown-menu 
                dropdown-menu-left" aria-labelledby="actions-menu" role="menu">';

            foreach ($this->action_menu as $menu_id => $menu_text) { 
                $html .= "<a id='$menu_id' class='dropdown-item'>$menu_text</a>";
            }

            $html .= '</div></div>';
        }
        
        $html .= '<div class="dropdown d-inline-block float-right"><button class="btn btn-secondary dropdown-toggle 
            float-right" type="button" id="sort-menu" data-toggle="dropdown">Sort by <span class="caret"></span></button>
            <div id="dropdown-menu-sort" class="dropdown-menu dropdown-menu-right" aria-labelledby="sort-menu" role="menu">';

        $sort_index = 0;
        foreach ($this->sort_menu as $menu_id => $menu_text) {
            $is_active = $sort_by == $sort_index ? ' class="dropdown-item active" ' : 'class="dropdown-item"'; 
            $html .= "<a id='$menu_id' onclick=\"$('#o').val($sort_index);\" $is_active>$menu_text</a>";
            $sort_index++;
        }

        $html .= '</div></div></div></div></div></div>';

        return $html;
    }

    abstract protected function print_content();
}

?>