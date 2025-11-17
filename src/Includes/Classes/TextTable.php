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

class TextTable extends Table
{
    protected $is_shared      = false;
    protected $show_archived  = false;

    /**
     * Constructor
     *
     * @param array $rows
     * @param boolean $show_archived
     */
    public function __construct(array $rows, bool $show_archived)
    {
        $this->headings = ['Title'];
        $this->col_widths = ['33px', ''];
        $this->action_menu = $show_archived
            ? ['mArchive' => 'Unarchive', 'mDelete' => 'Delete']
            : ['mArchive' => 'Archive', 'mDelete' => 'Delete'];
        $this->individual_action_menu = $show_archived
            ? ['imEdit' => 'Edit', 'imArchive' => 'Unarchive', 'imShare' => 'Share', 'imDelete' => 'Delete']
            : ['imEdit' => 'Edit', 'imArchive' => 'Archive', 'imShare' => 'Share', 'imDelete' => 'Delete'];
        $this->sort_menu = ['mSortByNew' => 'New first', 'mSortByOld' => 'Old first'];
        $this->rows = $rows;
        $this->has_chkbox = true;
        $this->is_shared = false;
        $this->show_archived = $show_archived;
    } // end __construct()

    /**
     * Generates the HTML content for the table rows
     *
     * @return string
     */
    protected function printContent(): string
    {
        $html = '';
    
        foreach ($this->rows as $row) {
            $html .= $this->generateTableRow($row);
        }
    
        return $html;
    } // end printContent()
    
    /**
     * Generates the HTML for a single table row
     *
     * @param array $row
     * @return string
     */
    private function generateTableRow(array $row): string
    {
        $type_icon = $this->generateTypeIcon($row);
        $audio_icon = $this->generateAudioIcon($row);
        $link = $this->generateLink($row);
        
        $text_author = $this->formatTextAuthor($row);
        $shared_by = $this->formatSharedBy($row);
        $text_level = $this->formatTextLevel($row);
        $nr_of_words = $this->formatWordCount($row);
        $individual_action_menu = $this->generateIndividualActionMenu($row);
    
        $html  = '<tr>';
        $html .= $this->generateCheckboxCell($row);

        $html .= '<td><div class="text-row d-flex justify-content-between align-items-center">'
            . '<div>' . $type_icon . ' ' . $audio_icon . ' ' . $link . '<br>'
            . '<small>' . $text_author . $shared_by . $text_level . $nr_of_words . '</small></div>'
            . $individual_action_menu
            . '</div></td>';
        $html .= '</tr>';
    
        return $html;
    } // end generateTableRow()

    /**
     * Generates the audio icon HTML for a row
     *
     * @param array $row
     * @return string
     */
    private function generateAudioIcon(array $row): string
    {
        $result = '';

        $adequate_text_length = $row['char_length'] < Reader::MAX_TEXT_LENGTH + 1;
        $not_video_or_ebook = $row['type'] < 5 || $row['type'] > 6;

        if (!empty($row['audio_uri'])) {
            $result = '<span title="Has audio (provided by user)" class="bi bi-headphones"></span>';
        } elseif ($adequate_text_length && $not_video_or_ebook) {
            $result = '<span title="Has audio (created by TTS engine - only works in assisted learning mode)" '
                . 'class="bi bi-volume-up-fill"></span>';
        }
        
        return $result;
    } // end generateAudioIcon()

    /**
     * Generates the HTML link for a row
     *
     * @param array $row
     * @return string
     */
    private function generateLink(array $row): string
    {
        $title = $row['title'];

        if ($this->show_archived) {
            return $title;
        }

        $link_html = '';
        if (!empty($row['type'])) {
            switch ($row['type']) {
                case 5:
                    $link_url = 'showvideo?id=' . $row['id'];
                    $link_html = !empty($link_url) ? '<a href="' . $link_url : '';
                    break;
                case 6:
                    $link_url = 'showebook?id=' . $row['id'];
                    $link_html = !empty($link_url) ? '<a href="' . $link_url : '';
                    break;
                default:
                    $link_html = '<a href="showtext?id=' . $row['id'];
                    break;
            }
    
            $link_html .= $this->is_shared ? '&sh=1">' : '&sh=0">';
        }
    
        $link_html .= $title . '</a>';

        return $link_html ;
    } // end generateLink()

    /**
     * Prints action menu
     *
     * @return string
     */
    private function generateIndividualActionMenu(array $row): string
    {
        if (empty($this->action_menu)) {
            return '';
        }

        $is_ebook = $row['type'] === 6;
        $individual_action_menu = $this->individual_action_menu;

        if ($is_ebook) {
            unset($individual_action_menu['imEdit'], $individual_action_menu['imShare']);
        }

        $html = <<<HTML_ACTION_MENU
            <div class="dropdown">
                <button class="btn btn-link btn-sm text-muted" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actions-menu" role="menu">
            HTML_ACTION_MENU;

        foreach ($individual_action_menu as $menu_id => $menu_text) {
            $id = htmlspecialchars($menu_id, ENT_QUOTES, 'UTF-8');
            $text = htmlspecialchars($menu_text, ENT_QUOTES, 'UTF-8');

            $text = $this->generateActionMenuIcon($menu_text) . ' ' . $text;

            if ($menu_text === 'Delete') {
                $html .= "<a class='{$id} dropdown-item text-danger'>{$text}</a>";
            } else {
                $html .= "<a class='{$id} dropdown-item'>{$text}</a>";
            }
        }

        $html .= '</div></div>';

        return $html;
    }
    
    /**
     * Formats and returns the word count HTML for a row
     *
     * @param array $row
     * @return string
     */
    private function formatWordCount(array $row): string
    {
        return !empty($row['word_count'])
            ? ' - ' . number_format($row['word_count']) . ' words'
            : '';
    } // end formatWordCount()
    
    /**
     * Formats and returns the text level HTML for a row
     *
     * @param array $row
     * @return string
     */
    private function formatTextLevel(array $row): string
    {
        $levels = ['Beginner', 'Intermediate', 'Advanced'];

        return !empty($row['level'])
            ? ' - ' . $levels[$row['level'] - 1]
            : '';
    } // end formatTextLevel()
    
    /**
     * Generates the HTML for the checkbox cell
     *
     * @param array $row
     * @param integer $text_id
     * @return string
     */
    private function generateCheckboxCell(array $row): string
    {
        $text_id = $row['id'];

        if ($this->has_chkbox) {
            return '<td class="col-checkbox"><div><input id="row-' . $text_id . '" class="form-check-input '
                . 'chkbox-selrow" type="checkbox" aria-label="Select row" data-idText="' . $text_id . '">'
                . '<label class="form-check-label" for="row-' . $text_id . '"></label></div></td>';
        } else {
            $total_likes = $row['total_likes'] ?? 0;
            $user_liked = $row['user_liked'] ? 'bi-heart-fill ' : 'bi-heart';
    
            return '<td class="text-center"><span title="Like"><span class="' . $user_liked . '" '
                . 'data-idText="' . $text_id . '"></span><br><small>' . $total_likes . '</small></span></td>';
        }
    } // end generateCheckboxCell()

    /**
     * Generates the audio icon HTML for a row
     *
     * @param array $row
     * @return string
     */
    private function generateTypeIcon(array $row): string
    {
        $type_icons = [
            'Article' => '<span title="Article" class="bi bi-newspaper"></span>',
            'Conversation' => '<span title="Conversation" class="bi bi-chat-left-dots"></span>',
            'Letter' => '<span title="Letter" class="bi bi-envelope-open-fill"></span>',
            'Lyrics' => '<span title="Lyrics" class="bi bi-file-earmark-music-fill"></span>',
            'Video' => '<span title="Video" class="bi bi-file-earmark-play-fill"></span>',
            'Ebook' => '<span title="Ebook" class="bi bi-journal-bookmark-fill"></span>',
            'Other' => '<span title="Other" class="bi bi-file-earmark-text-fill"></span>'
        ];

        $keys  = array_values($type_icons);

        return $keys[$row['type']-1];
    } // end generateTypeIcon()
    
    /**
     * Formats and returns the text author information
     *
     * @param array $row
     * @return string
     */
    private function formatTextAuthor(array $row): string
    {
        $shared_by = '';
        if (!empty($row[1])) {
            $shared_by = " via {$row[1]}";
        }
    
        if (!empty($row['author'])) {
            $text_author = "by {$row['author']}";
        } else {
            $source_uri = !empty($row['source_uri'])
                ? ' (' . Url::getDomainName($row['source_uri']) . ')'
                : '';
            $text_author = "by Unknown{$source_uri}";
        }
    
        return $text_author . $shared_by;
    } // end formatTextAuthor()
    
    /**
     * Formats and returns the shared by information
     *
     * @param array $row
     * @return string
     */
    private function formatSharedBy(array $row): string
    {
        return !empty($row[1]) ? " via {$row[1]}" : '';
    } // end formatSharedBy()
}
