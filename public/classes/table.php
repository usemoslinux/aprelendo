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
            <div class="col-xs-12">
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
        $html = '</tbody></table><div class="row"><div class="col-xs-12">';

        if (!empty($this->action_menu)) {
            $html .= '<div class="dropdown">
            <button class="btn btn-default dropdown-toggle disabled" type="button" 
                id="actions-menu" data-toggle="dropdown">Actions <span class="caret"></span></button><ul class="dropdown-menu 
                dropdown-menu-left" aria-labelledby="actions-menu" role="menu">';

            foreach ($this->action_menu as $menu_id => $menu_text) { 
                $html .= "<li id='$menu_id'><a role='menuitem'>$menu_text</a></li>";
            }

            $html .= '</ul></div>';
        }
        
        $html .= '<div class="dropdown"><button class="btn btn-default dropdown-toggle 
            pull-right" type="button" id="sort-menu" data-toggle="dropdown">Sort by <span class="caret"></span></button>
            <ul id="dropdown-menu-sort" class="dropdown-menu dropdown-menu-right" aria-labelledby="sort-menu" role="menu">';

        $sort_index = 0;
        foreach ($this->sort_menu as $menu_id => $menu_text) {
            $is_active = $sort_by == $sort_index ? ' class="active" ' : ''; 
            $html .= "<li id='$menu_id' onclick=\"$('#o').val($sort_index);\" $is_active><a role='menuitem'>$menu_text</a></li>";
            $sort_index++;
        }

        $html .= '</ul></div></div></div></div></div>';

        return $html;
    }

    abstract protected function print_content();
}

class TextTable extends Table {
    protected $is_shared;
    protected $show_archived;

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
    public function __construct($headings, $col_widths, $rows, $show_archived, $action_menu, $sort_menu) {
        parent::__construct($headings, $col_widths, $rows, $action_menu, $sort_menu);
        $this->has_chkbox = true;
        $this->is_shared = false;
        $this->show_archived = $show_archived;
    }

    /**
     * Prints table content (only for Texts table)
     *
     * @return string HTML for table content
     */
    protected function print_content() {
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
            $text_id = $this->rows[$i][0];
            $text_title = $this->rows[$i][1];
            // $text_type = isset($this->rows[$i][4]) && !empty($this->rows[$i][4]) ? "{$type_array[$this->rows[$i][4]-1][0]}" : '';
            $text_author = isset($this->rows[$i][2]) && !empty($this->rows[$i][2]) ? "by {$this->rows[$i][2]}" : 'by Unkown';
            $nr_of_words = isset($this->rows[$i][5]) && !empty($this->rows[$i][5]) ? ' - ' . number_format($this->rows[$i][5]) . ' words' : '';
            $text_level = isset($this->rows[$i][6]) && !empty($this->rows[$i][6]) ? " - {$level_array[$this->rows[$i][6]-1]}" : '';
            $has_audio = isset($this->rows[$i][3]) && !empty($this->rows[$i][3]) ? ' <i title="Has audio" class="fas fa-headphones"></i>' : '';
            $link = $this->show_archived ? '' : 'showtext.php';
            
            // if it's a video, then change link accordinly
            if ($this->rows[$i][4] && !empty($this->rows[$i][4])) {
                if (isset($link) && !empty($link)) {
                    switch ($this->rows[$i][4]) {
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
            
            if ($this->has_chkbox) {
                $html .= "<tr><td class='col-checkbox'><input class='chkbox-selrow' type='checkbox' data-idText='$text_id'></td>";
            } else {
                $total_likes = $this->total_user_likes[$i]; // get total user likes for this post
                $user_liked = $this->active_user_liked[$i] ? 'fas' : 'far'; // check if user liked this post
                $html .= "<tr><td class='text-center'><span title='Like'><i class='$user_liked fa-heart' data-idText='$text_id'></i><br><small>$total_likes</small></span></td>";
            }
            
            $html .= '<td class="col-title">' . $type_array[$this->rows[$i][4]-1][1] . ' ' . $link .
            $text_title . '</a><br/><small>' . $text_author . $text_level . $nr_of_words . $has_audio . '</small></td></tr>';
        }
        return $html;
    }
}

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
    public function __construct($headings, $col_widths, $rows, $action_menu, $sort_menu) {
        parent::__construct($headings, $col_widths, $rows, $action_menu, $sort_menu);
        $this->has_chkbox = true;
    }

    /**
     * Prints table content (only for Words table)
     *
     * @return string HTML for table content
     */
    protected function print_content() {
        $html = '';
            
        for ($i=0; $i < sizeof($this->rows); $i++) { 
            $word_id = $this->rows[$i][0];
            $word = $this->rows[$i][1];
            $word_status = $this->rows[$i][2];
            $status = array('fa-hourglass-end status_learned', 'fa-hourglass-half status_learning', 'fa-hourglass-start status_new');
            $status_text = array('Learned', 'Learning', 'New');

            if ($this->has_chkbox) {
                $html .= "<tr><td class='col-checkbox'><label><input class='chkbox-selrow' type='checkbox' data-idWord='$word_id'></label></td>";
            } 
            
            $html .= '<td class="col-title">' . $word . '</td><td class="col-status text-center">' .
                '<i title="' . $status_text[$word_status] . '" class="fas ' . $status[$word_status] . '"></i></td></tr>';
        }
        return $html;
    }
}

?>