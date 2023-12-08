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

namespace Aprelendo;

use Aprelendo\Table;

class WordTable extends Table
{
    private const STATUS_ICONS = [
        'bi-hourglass-top status_learned',
        'bi-hourglass-split status_learning',
        'bi-hourglass-bottom status_new',
        'bi-hourglass-bottom status_forgotten',
    ];

    private const STATUS_TEXT = [
        'Learned',
        'Learning',
        'New',
        'Forgotten',
    ];

    /**
     * Constructor
     *
     * @param array $rows
     */
    public function __construct(array $rows)
    {
        $this->headings = ['Word', 'Status'];
        $this->col_widths = ['33px', '', '60px'];
        $this->action_menu = ['mDelete' => 'Delete'];
        $this->sort_menu = [
            'mSortNewFirst'       => 'New first',
            'mSortOldFirst'       => 'Old first',
            'mSortLearnedFirst'   => 'Learned first',
            'mSortForgottenFirst' => 'Forgotten first'
        ];
        $this->rows = $rows;
        $this->has_chkbox = true;
    }

    /**
     * Prints table content (only for Words table)
     *
     * @return string HTML for table content
     */
    protected function printContent(): string
    {
        $html = '';

        foreach ($this->rows as $row) {
            $html .= $this->generateTableRow($row);
        }

        return $html;
    }

    /**
     * Generates the HTML for a single table row
     *
     * @param array $row
     * @return string
     */
    private function generateTableRow(array $row): string
    {
        $html  = "<tr>";
        $html .= $this->generateCheckboxCell($row);
        $html .= $this->generateLink($row);
        $html .= $this->generateStatusIcon($row);
        $html .= "</tr>";

        return $html;
    } // end generateTableRow()

    /**
     * Generates the HTML for the checkbox cell
     *
     * @param array $row
     * @return string
     */
    private function generateCheckboxCell(array $row): string
    {
        $word_id = $row['id'];

        return $this->has_chkbox
            ? '<td class="col-checkbox"><div><input id="row-' . $word_id .'" class="form-check-input '
                . 'chkbox-selrow" type="checkbox" aria-label="Select row" data-idWord="' . $word_id
                . '"><label class="form-check-label" for="row-' . $word_id . '"></label></div></td>'
            : '';
    } // end generateCheckboxCell()

    /**
     * Generates the HTML link for a row
     *
     * @param array $row
     * @return string
     */
    private function generateLink(array $row): string
    {
        $word = $row['word'];
        return "<td class='col-title'><a class='word word-list'>$word</a></td>";
    } // end generateLink()

    /**
     * Generates the status icon HTML for a row
     *
     * @param array $row
     * @return string
     */
    private function generateStatusIcon(array $row): string
    {
        $word_status = $row['status'];
        $diff_today_modif = $row['diff_today_modif'];
        $days_modif_str = $diff_today_modif !== null
            ? ' - modified ' . $diff_today_modif . ' days ago'
            : ' - never modified';

        $statusIconClass = self::STATUS_ICONS[$word_status];
        $statusText = self::STATUS_TEXT[$word_status];

        return '<td class="col-status text-center"><span title="' . $statusText . $days_modif_str
            . '" class="bi ' . $statusIconClass . '"></span></td>';
    } // end generateStatusIcon()
}
