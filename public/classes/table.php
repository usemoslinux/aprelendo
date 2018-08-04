<?php 

abstract class Table 
{
    protected $show_archived;
    protected $headings;
    protected $col_count;
    protected $col_widths;
    protected $rows;
    protected $action_menu;
    protected $sort_menu;
    protected $url;
    protected $has_chkbox;

    /**
     * Constructor
     *
     * @param string $headings
     * @param string $col_widths
     * @param array $rows
     * @param string $url
     * @param string $action_menu HTML to create action menu
     * @param string $sort_menu HTML to create sort menu
     */
    public function __construct($headings, $col_widths, $rows, $url, $action_menu, $sort_menu) {
        $this->headings = $headings;
        $this->col_count = sizeof($headings);
        $this->col_widths = $col_widths;
        $this->rows = $rows;
        $this->url = $url;
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
            <table id="textstable" class="table table-bordered">
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

    /**
     * Constructor
     *
     * @param string $headings
     * @param string $col_widths
     * @param array $rows
     * @param string $url
     * @param string $action_menu HTML to create action menu
     * @param string $sort_menu HTML to create sort menu
     */
    public function __construct($headings, $col_widths, $rows, $url, $action_menu, $sort_menu) {
        parent::__construct($headings, $col_widths, $rows, $url, $action_menu, $sort_menu);
        $this->has_chkbox = true;
        $this->is_shared = false;
    }

    /**
     * Prints table content (only for Texts table)
     *
     * @return string HTML for table content
     */
    protected function print_content() {
        $html = '';
        $type_array = array('<i title="Article" class="far fa-newspaper"></i>', 
            '<i title="Conversation" class="far fa-comments"></i>', 
            '<i title="Letter" class="far fa-envelope-open"></i>', 
            '<i title="Song" class="fas fa-music"></i>', 
            '<i title="Video" class="fas fa-video"></i>', 
            '<i title="Other" class="far fa-file-alt"></i>');
        $level_array = array('Beginner', 'Intermediate', 'Advanced');
        for ($i=0; $i < sizeof($this->rows); $i++) { 
            $text_id = $this->rows[$i][0];
            $text_title = $this->rows[$i][1];
            $text_type = isset($this->rows[$i][4]) && !empty($this->rows[$i][4]) ? "Type: {$type_array[$this->rows[$i][4]-1]}" : '';
            $text_author = isset($this->rows[$i][2]) && !empty($this->rows[$i][2]) ? " - Author: {$this->rows[$i][2]}" : ' - Author: unkown';
            $nr_of_words = isset($this->rows[$i][5]) && !empty($this->rows[$i][5]) ? " - {$this->rows[$i][5]} words" : '';
            $text_level = isset($this->rows[$i][6]) && !empty($this->rows[$i][6]) ? " - Level: {$level_array[$this->rows[$i][6]-1]}" : '';
            $has_audio = isset($this->rows[$i][3]) && !empty($this->rows[$i][3]) ? ' - <i title="Has audio" class="fas fa-headphones"></i>' : '';
            $link = '';
            
            // if it's a video, then change link accordinly
            if ($this->rows[$i][4] && !empty($this->rows[$i][4])) {
                if (isset($this->url) && !empty($this->url)) {
                    switch (true) {
                        case ($this->rows[$i][4] == 5):
                            $replace = str_replace('showtext.php', 'showvideo.php', $this->url);
                            $link = empty($replace) ? '' : "<a href ='{$replace}?id=$text_id";
                            break;
                        default:
                            $link = empty($this->url) ? '' : "<a href ='{$this->url}?id=$text_id";
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
                $html .= "<tr><td class='text-center'><a href='#' title='Like' class='btn-like-text'><i class='$user_liked fa-heart' data-idText='$text_id'></i><br><small>$total_likes</small></a></td>";
            }
            
            $html .= '<td class="col-title">' . $link .
            $text_title . '</a><br/><small>' . $text_type . $text_author . $nr_of_words . $text_level . $has_audio . '</small></td></tr>';
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
     * @param string $url
     * @param string $action_menu HTML to create action menu
     * @param string $sort_menu HTML to create sort menu
     */
    public function __construct($con, $headings, $col_widths, $rows, $url, $action_menu, $sort_menu) {
        parent::__construct($headings, $col_widths, $rows, $url, $action_menu, $sort_menu);
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
     * @param string $url
     * @param string $action_menu HTML to create action menu
     * @param string $sort_menu HTML to create sort menu
     */
    public function __construct($headings, $col_widths, $rows, $url, $action_menu, $sort_menu) {
        parent::__construct($headings, $col_widths, $rows, $url, $action_menu, $sort_menu);
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