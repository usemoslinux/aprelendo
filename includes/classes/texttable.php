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
use Aprelendo\Includes\Classes\Url;

class TextTable extends Table {
    protected $is_shared      = false;
    protected $show_archived  = false;
    
    /**
    * Constructor
    *
    * @param string $headings
    * @param string $col_widths
    * @param array $rows
    * @param boolean $show_archived
    * @param string $action_menu HTML to create action menu
    * @param string $sort_menu HTML to create sort menu
    */
    public function __construct(array $headings, array $col_widths, array $rows, bool $show_archived, 
                                array $action_menu, array $sort_menu) {
        parent::__construct($headings, $col_widths, $rows, $action_menu, $sort_menu);
        $this->has_chkbox = true;
        $this->is_shared = false;
        $this->show_archived = $show_archived;
    } // end __construct()
    
    /**
    * Prints table content (only for Texts table)
    *
    * @return string HTML for table content
    */
    protected function print_content(): string {
        $html = '';
        $type_array = array(
            array('Article',        '<i title="Article" class="far fa-newspaper"></i>'),
            array('Conversation',   '<i title="Conversation" class="far fa-comments"></i>'),
            array('Letter',         '<i title="Letter" class="far fa-envelope-open"></i>'),
            array('Song',           '<i title="Song" class="fas fa-music"></i>'),
            array('Video',          '<i title="Video" class="fas fa-video"></i>'),
            array('Ebook',          '<i title="Ebook" class="fas fa-book"></i>'),
            array('Other',          '<i title="Other" class="far fa-file-alt"></i>')
        );
        $level_array = array('Beginner', 'Intermediate', 'Advanced');
        
        for ($i=0; $i < sizeof($this->rows); $i++) { 
            $text_id = $this->rows[$i]['id'];
            $shared_by = isset($this->rows[$i][1]) && !empty($this->rows[$i][1]) ? " via {$this->rows[$i][1]}" : '';
            $text_title = $this->rows[$i]['title'];
            $source_uri = isset($this->rows[$i]['source_uri']) && !empty($this->rows[$i]['source_uri']) ? ' (' . Url::getDomainName($this->rows[$i]['source_uri']) . ')': '';
            $text_author = isset($this->rows[$i]['author']) && !empty($this->rows[$i]['author']) ? "by {$this->rows[$i]['author']}" : 'by Unkown' . $source_uri;
            
            $link = $this->show_archived ? '' : 'showtext.php';
            
            // if it's a video, then change link accordinly
            if ($this->rows[$i]['type'] && !empty($this->rows[$i]['type'])) {
                if (isset($link) && !empty($link)) {
                    switch ($this->rows[$i]['type']) {
                        case 5: // videos
                        $replace = str_replace('showtext.php', 'showvideo.php', $link);
                        $link = empty($replace) ? '' : "<a href ='$replace?id=$text_id";
                        break;
                        case 6: // ebooks
                        $replace = str_replace('showtext.php', 'showebook.php', $link);
                        $link = empty($replace) ? '' : "<a href ='$replace?id=$text_id";
                        break;    
                        default:
                        $link = empty($link) ? '' : "<a href ='{$link}?id=$text_id";
                        break;
                    }
                    // determine if text is shared or private
                    $link .= $this->is_shared ? "&sh=1'>" : "&sh=0'>";
                }
            } else {
                $text_type = '';
            }
            
            $nr_of_words = isset($this->rows[$i]['word_count']) && !empty($this->rows[$i]['word_count']) ? ' - ' . number_format($this->rows[$i]['word_count']) . ' words' : '';
            
            $text_level = isset($this->rows[$i]['level']) && !empty($this->rows[$i]['level']) ? " - {$level_array[$this->rows[$i]['level']-1]}" : '';
            
            if ($this->has_chkbox) {
                $html .= "<tr><td class='col-checkbox'><div class='custom-control custom-checkbox'><input id='row-$text_id' class='custom-control-input chkbox-selrow' type='checkbox' data-idText='$text_id'><label class='custom-control-label' for='row-$text_id'></label></div></td>";
            } else {
                $total_likes = isset($this->rows[$i]['total_likes']) ? $this->rows[$i]['total_likes'] : 0; // get total user likes for this post
                $user_liked = $this->rows[$i]['user_liked'] ? 'fas' : 'far'; // check if user liked this post

                $html .= "<tr><td class='text-center'><span title='Like'><i class='$user_liked fa-heart' data-idText='$text_id'></i><br><small>$total_likes</small></span></td>";
            }
            
            $html .= '<td class="col-title">' . $type_array[$this->rows[$i]['type']-1][1] . ' ' . $link .
            $text_title . '</a><br><small>' . $text_author . $shared_by . $text_level . $nr_of_words . '</small></td></tr>';
        }
        return $html;
    } // end print_content()
}

?>