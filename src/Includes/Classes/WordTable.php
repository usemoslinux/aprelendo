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
        $this->headings = ['Word'];
        $this->col_widths = ['33px', ''];
        $this->action_menu = ['mDelete' => 'Delete'];
        $this->individual_action_menu = [
            'imOpenDictionary' => 'Open dictionary',
            'imOpenImageDictionary' => 'Open image dictionary',
            'imOpenTranslator' => 'Open translator',
            'imOpenAIBot' => 'Open AI bot',
            'imForgot' => 'Mark as forgotten',
            'imDelete' => 'Delete'
        ];
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
        $word = htmlspecialchars($row['word'], ENT_QUOTES, 'UTF-8');

        $html  = '<tr data-word="' . $word . '">';
        $html .= $this->generateCheckboxCell($row);
        $html .= $this->generateWordCell($row);
        $html .= "</tr>";

        return $html;
    } 

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
    } 

    /**
     * Generates the HTML link for a row
     *
     * @param array $row
     * @return string
     */
    private function generateWordCell(array $row): string
    {
        $word = htmlspecialchars($row['word'], ENT_QUOTES, 'UTF-8');
        $word_status_class = $this->generateWordStatusClass($row);
        $freq_badge = $this->generateFrequencyBadge($row['freq_level']);
        $individual_action_menu = $this->generateIndividualActionMenu($row);

        return <<<HTML_WORD_CELL
            <td>
                <div class="text-row d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <a class="word word-list reviewing {$word_status_class}">{$word}</a>
                        {$freq_badge}
                    </div>
                    {$individual_action_menu}
                </div>
            </td>
        HTML_WORD_CELL;
    } 

    /**
     * Generates the frequency badge HTML for a row.
     *
     * @param string $frequency
     * @return string
     */
    private function generateFrequencyBadge(string $frequency): string
    {
        return match ($frequency) {
            'very high' => '<span class="badge rounded-pill bg-danger" data-bs-toggle="tooltip" '
                . 'data-bs-placement="top" data-bs-title="Very high frequency"><i class="bi bi-star-fill"></i> '
                . '<i class="bi bi-star-fill"></i></span>',
            'high' => '<span class="badge rounded-pill bg-warning text-dark" data-bs-toggle="tooltip" '
                . 'data-bs-placement="top" data-bs-title="High frequency"><i class="bi bi-star-fill"></i></span>',
            default => '',
        };
    } 

    /**
     * Returns the word status class for a row.
     *
     * @param array $row
     * @return string
     */
    private function generateWordStatusClass(array $row): string
    {
        $word_status = WordStatus::tryFrom((int)$row['status']) ?? WordStatus::new_word;

        return match ($word_status) {
            WordStatus::learned => 'learned',
            WordStatus::learning => 'learning',
            WordStatus::new_word => 'new',
            WordStatus::forgotten => 'forgotten',
        };
    } 

    /**
     * Prints the individual action menu for a word row.
     *
     * @param array $row
     * @return string
     */
    private function generateIndividualActionMenu(array $row): string
    {
        if (empty($this->individual_action_menu)) {
            return '';
        }

        $html = <<<HTML_ACTION_MENU
            <div class="dropdown">
                <button class="btn btn-link btn-sm text-muted" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actions-menu" role="menu">
            HTML_ACTION_MENU;

        foreach ($this->individual_action_menu as $menu_id => $menu_text) {
            if ($menu_id === 'imForgot') {
                $html .= '<div class="dropdown-divider"></div>';
            }

            $id = htmlspecialchars($menu_id, ENT_QUOTES, 'UTF-8');
            $text = htmlspecialchars($menu_text, ENT_QUOTES, 'UTF-8');
            $icon_html = $this->generateActionMenuIcon($menu_text);
            $item_html = $icon_html . ' ' . $text;

            if ($menu_id === 'imDelete') {
                $html .= "<a class='{$id} dropdown-item text-danger'>{$item_html}</a>";
            } else {
                $html .= "<a class='{$id} dropdown-item'>{$item_html}</a>";
            }
        }

        $html .= '</div></div>';

        return $html;
    } 

    /**
     * Generates action menu icons HTML for word-specific actions.
     *
     * @param string $menu_text
     * @return string
     */
    protected function generateActionMenuIcon(string $menu_text): string
    {
        $icons = [
            'Open dictionary' => '<span title="Open dictionary" class="bi bi-book me-2"></span>',
            'Open image dictionary' => '<span title="Open image dictionary" class="bi bi-card-image me-2"></span>',
            'Open translator' => '<span title="Open translator" class="bi bi-translate me-2"></span>',
            'Open AI bot' => '<span title="Open AI bot" class="bi bi-robot me-2"></span>',
            'Mark as forgotten' => '<span title="Mark as forgotten" class="bi bi-bookmark-dash me-2"></span>',
        ];

        return $icons[$menu_text] ?? parent::generateActionMenuIcon($menu_text);
    } 
}
