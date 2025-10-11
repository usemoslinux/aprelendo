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

abstract class Table
{
    protected array $headings                = [];
    protected array $col_widths              = [];
    protected array $rows                    = [];
    protected array $action_menu             = [];
    protected array $individual_action_menu  = [];
    protected array $sort_menu               = [];
    protected bool  $has_chkbox              = false;

    /**
     * Prints table
     *
     * @param int $sort_by
     * @return string
     */
    public function print(int $sort_by): string
    {
        $html = $this->printHeader();
        $html .= $this->printContent();
        $html .= $this->printFooter($sort_by);
        return $html;
    } // end print()

    /**
     * Prints table header
     *
     * @return string HTML for table header
     */
    protected function printHeader(): string
    {
        $html = '<div class="row">
            <div class="col-sm-12">
            <table class="table table-bordered table-hover">
            <colgroup>';

        foreach ($this->col_widths as $col_width) {
            if (empty($col_width)) {
                $html .= "<col>";
            } else {
                $html .= "<col style='width: $col_width;'>";
            }
        }

        $html .= '</colgroup><thead class="table-light"><tr>';

        if ($this->has_chkbox) {
            $html .= '<th class="col-checkbox"><div><input id="chkbox-selall" class="form-check-input"
            aria-label="Select all" type="checkbox"><label class="form-check-label" for="chkbox-selall"></label>
            </div></th>';
        } else {
            $html .= '<th></th>';
        }

        foreach ($this->headings as $heading) {
            $html .= "<th class='col-title'>$heading</th>";
        }

        $html .= '</tr></thead><tbody>';

        return $html;
    } // end printHeader()

    /**
     * Prints table footer
     *
     * @param int $sort_by
     * @return string HTML for table footer
     */
    protected function printFooter(int $sort_by): string
    {
        $html = '</tbody></table>'; // close table
        $html .= '<div class="row"><div class="col-sm-12">'; // add footer row
        $html .= $this->printActionMenu();
        $html .= $this->printSortMenu($sort_by);
        $html .= '</div></div>'; // close footer row

        return $html;
    } // end printFooter()

    /**
     * Prints action menu
     *
     * @return string
     */
    private function printActionMenu(): string
    {
        if (empty($this->action_menu)) {
            return '';
        }

        $html = <<<HTML_ACTION_MENU
            <div class="dropdown d-inline-block">
                <button class="btn btn-secondary dropdown-toggle disabled" type="button"
                    id="actions-menu" data-bs-toggle="dropdown">
                    Actions <span class="caret"></span>
                </button>
            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="actions-menu" role="menu">
            HTML_ACTION_MENU;

        foreach ($this->action_menu as $menu_id => $menu_text) {
            $id = htmlspecialchars($menu_id, ENT_QUOTES, 'UTF-8');
            $text = htmlspecialchars($menu_text, ENT_QUOTES, 'UTF-8');

            $text = $this->generateActionMenuIcon($menu_text) . ' ' . $text;

            if ($menu_text === 'Delete') {
                $html .= "<a id='{$id}' class='dropdown-item text-danger'>{$text}</a>";
            } else {
                $html .= "<a id='{$id}' class='dropdown-item'>{$text}</a>";
            }
        }

        $html .= '</div></div>';

        return $html;
    }

    /**
     * Generates action menu icons HTML
     *
     * @param array $row
     * @return string
     */
    protected function generateActionMenuIcon(string $menu_text): string
    {
        $icons = [
            'Edit' => '<span title="Edit" class="bi bi-pencil me-2"></span>',
            'Share' => '<span title="Share" class="bi bi-share me-2"></span>',
            'Archive' => '<span title="Archive" class="bi bi-archive me-2"></span>',
            'Unarchive' => '<span title="Unarchive" class="bi bi-archive-fill me-2"></span>',
            'Delete' => '<span title="Delete" class="bi bi-trash me-2"></span>'
        ];

        return $icons[$menu_text];
    } // end generateActionMenuIcon()

    /**
     * Prints sort menu
     *
     * @param string $sort_by
     * @return string
     */
    private function printSortMenu(string $sort_by): string
    {
        if (empty($this->sort_menu)) {
            return '';
        }

        $html = <<<HTML_SORT_MENU
            <div class="dropdown d-inline-block float-end">
            <button class="btn btn-secondary dropdown-toggle float-end" type="button"
                id="sort-menu" data-bs-toggle="dropdown">
                Sort by <span class="caret"></span>
            </button>
            <div id="dropdown-menu-sort" class="dropdown-menu dropdown-menu-right" aria-labelledby="sort-menu" role="menu">
            HTML_SORT_MENU;

        $sort_index = 0;
        foreach ($this->sort_menu as $menu_id => $menu_text) {
            $id = htmlspecialchars($menu_id, ENT_QUOTES, 'UTF-8');
            $text = htmlspecialchars($menu_text, ENT_QUOTES, 'UTF-8');
            $is_active = ($sort_by == $sort_index) ? 'active' : '';

            $html .= "<a id='{$id}' data-value='{$sort_index}' class='dropdown-item o {$is_active}'>{$text}</a>";
            $sort_index++;
        }

        $html .= '</div></div>';

        return $html;
    }


    abstract protected function printContent();
}
