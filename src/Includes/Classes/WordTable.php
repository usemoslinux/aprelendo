<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class WordTable extends Table
{
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
        $word = htmlspecialchars($row['word'], ENT_QUOTES, 'UTF-8');
        $freq_badge = $this->generateFrequencyBadge($row['freq_level']);
        return "<td class='col-title'><a class='word word-list'>$word</a> $freq_badge</td>";
    } // end generateLink()

    /**
     * Generates the frequency badge HTML for a row.
     *
     * @param string $frequency
     * @return string
     */
    private function generateFrequencyBadge(string $frequency): string
    {
        return match ($frequency) {
            'very high' => '<span class="badge rounded-pill bg-danger" title="Very high frequency">Very high</span>',
            'high' => '<span class="badge rounded-pill bg-warning text-dark" title="High frequency">High</span>',
            default => '',
        };
    } // end generateFrequencyBadge()

    /**
     * Generates the status icon HTML for a row
     *
     * @param array $row
     * @return string
     */
    private function generateStatusIcon(array $row): string
    {
        $word_status = WordStatus::tryFrom((int)$row['status']) ?? WordStatus::new_word;
        $diff_today_modif = $row['diff_today_modif'];
        
        $days_modif_str = match (true) {
            $diff_today_modif == null => ' - never modified',
            $diff_today_modif == 0 => ' - modified today',
            $diff_today_modif == 1 => ' - modified yesterday',
            default => ' - modified ' . $diff_today_modif . ' days ago',
        };

        $status_icon_class = $word_status->getIconClass();
        $status_text = $word_status->getLabel();

        return '<td class="col-status text-center"><span title="' . $status_text . $days_modif_str
            . '" class="bi ' . $status_icon_class . '"></span></td>';
    } // end generateStatusIcon()
}
