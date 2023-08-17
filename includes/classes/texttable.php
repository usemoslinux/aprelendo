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
    public function __construct(array $rows, bool $show_archived) {
        $this->headings = array('Title');
        $this->col_widths = array('33px', '');
        $this->action_menu = $show_archived
            ? array('mArchive' => 'Unarchive', 'mDelete' => 'Delete')
            : array('mArchive' => 'Archive', 'mDelete' => 'Delete');
        $this->sort_menu = array('mSortByNew' => 'New first', 'mSortByOld' => 'Old first');
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
    protected function printContent(): string {
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
    private function generateTableRow(array $row): string {
        $type_icon = $this->generateTypeIcon($row);
        $audio_icon = $this->generateAudioIcon($row);
        $link = $this->generateLink($row);
        
        $text_author = $this->formatTextAuthor($row);
        $shared_by = $this->formatSharedBy($row);
        $text_level = $this->formatTextLevel($row);
        $nr_of_words = $this->formatWordCount($row);
    
        $html  = '<tr>';
        $html .= $this->generateCheckboxCell($row);
        $html .= '<td class="col-title">' . $type_icon . ' ' . $audio_icon . ' ' . $link . '<br>'
            . '<small>' . $text_author . $shared_by . $text_level . $nr_of_words . '</small></td>';
        $html .= '</tr>';
    
        return $html;
    } // end generateTableRow()

    /**
     * Generates the audio icon HTML for a row
     *
     * @param array $row
     * @return string
     */
    private function generateAudioIcon(array $row): string {
        $has_audio = $this->hasAudio($row);
        return $has_audio ? '<span title="Has audio" class="fa-solid fa-headphones"></span>' : '';
    } // end generateAudioIcon()

    /**
     * Checks if a row has audio
     *
     * @param array $row
     * @return boolean
     */
    private function hasAudio(array $row): bool {
        return
            !empty($row['audio_uri']) ||
            (
                $row['char_length'] < Reader::MAX_TEXT_LENGTH + 1 &&
                ($row['type'] < 5 || $row['type'] > 6)
            );
    } // end hasAudio()
    
    /**
     * Generates the HTML link for a row
     *
     * @param array $row
     * @return string
     */
    private function generateLink(array $row): string
    {
        $title = $row['title'];
        $link_html = '';
        if ($row['type'] && !empty($row['type'])) {
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
     * Formats and returns the word count HTML for a row
     *
     * @param array $row
     * @return string
     */
    private function formatWordCount(array $row): string
    {
        return isset($row['word_count']) && !empty($row['word_count'])
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

        return isset($row['level']) && !empty($row['level'])
            ? ' - ' . $levels[$row['level'] - 1] . '"'
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
            return '<tr><td class="col-checkbox"><div><input id="row-' . $text_id . '" class="form-check-input '
                . 'chkbox-selrow" type="checkbox" aria-label="Select row" data-idText="' . $text_id . '">'
                . '<label class="form-check-label" for="row-' . $text_id . '"></label></div></td>';
        } else {
            $total_likes = isset($this->rows['total_likes']) ? $this->rows['total_likes'] : 0;
            $user_liked = $row['user_liked'] ? 'fas' : 'far';
    
            return '<tr><td class="text-center"><span title="Like"><span class="' . $user_liked . ' fa-heart" '
                . 'data-idText="' . $text_id . '"></span><br><small>' . $total_likes . '</small></span></td>';
        }
    } // end generateCheckboxCell()

    /**
     * Generates the audio icon HTML for a row
     *
     * @param array $row
     * @return string
     */
    private function generateTypeIcon(array $row): string {
        $type_icons = [
            'Article' => '<span title="Article" class="far fa-newspaper"></span>',
            'Conversation' => '<span title="Conversation" class="far fa-comments"></span>',
            'Letter' => '<span title="Letter" class="far fa-envelope-open"></span>',
            'Lyrics' => '<span title="Lyrics" class="fas fa-music"></span>',
            'Video' => '<span title="Video" class="fas fa-video"></span>',
            'Ebook' => '<span title="Ebook" class="fas fa-book"></span>',
            'Other' => '<span title="Other" class="far fa-file-alt"></span>'
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
        if (isset($row[1]) && !empty($row[1])) {
            $shared_by = " via {$row[1]}";
        }
    
        if (isset($row['author']) && !empty($row['author'])) {
            $text_author = "by {$row['author']}";
        } else {
            $source_uri = isset($row['source_uri']) && !empty($row['source_uri'])
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
        return isset($row[1]) && !empty($row[1]) ? " via {$row[1]}" : '';
    } // end formatSharedBy()
}
