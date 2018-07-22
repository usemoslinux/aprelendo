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

    public function __construct($headings, $col_widths, $rows, $url, $action_menu, $sort_menu) {
        $this->headings = $headings;
        $this->col_count = sizeof($headings);
        $this->col_widths = $col_widths;
        $this->rows = $rows;
        $this->url = $url;
        $this->action_menu = $action_menu;
        $this->sort_menu = $sort_menu;
    }

    public function print($sort_by) {
        $html = $this->print_header();
        $html .= $this->print_content();
        $html .= $this->print_footer($sort_by);
        return $html;
    }

    // functions to print table header, contents & footer
    protected function print_header() {
        $html = '<div class="row">
            <div class="col-xs-12">
            <table id="textstable" class="table table-bordered">
            <colgroup>';
        
        foreach ($this->col_widths as $col_width) { 
            $html .= "<col width='$col_width'>";
        }
        
        $html .= '</colgroup><thead><tr>
            <th class="col-checkbox"><input id="chkbox-selall" type="checkbox"></th>';

        foreach ($this->headings as $heading) { 
            $html .= "<th class='col-title'>$heading</th>";
        }

        $html .= '</tr></thead><tbody>';

        return $html;
    }

    protected function print_footer($sort_by) {
        $html = '</tbody></table><div class="row"><div class="col-xs-12"><div class="dropdown">
        <button class="btn btn-default dropdown-toggle disabled" type="button" id="actions-menu" data-toggle="dropdown">Actions <span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-left" aria-labelledby="actions-menu" role="menu">';
        
        foreach ($this->action_menu as $menu_id => $menu_text) { 
            $html .= "<li id='$menu_id'><a role='menuitem'>$menu_text</a></li>";
        }
        
        $html .= '</ul></div><div class="dropdown">
        <button class="btn btn-default dropdown-toggle pull-right" type="button" id="sort-menu" data-toggle="dropdown">Sort by <span class="caret"></span></button>
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
    protected function print_content() {
        $html = '';
        $type_array = array('Articles', 'Conversations', 'Letters', 'Songs', 'Videos', 'Others');
        for ($i=0; $i < sizeof($this->rows); $i++) { 
            $text_id = $this->rows[$i][0];
            $text_title = $this->rows[$i][1];
            $text_author = isset($this->rows[$i][2]) && !empty($this->rows[$i][2]) ? " - Author: {$this->rows[$i][2]}" : '';
            $text_type = isset($this->rows[$i][3]) && !empty($this->rows[$i][3]) ? "Type: {$type_array[$this->rows[$i][3]-1]}" : '';
            $link = '';
            
            if ($this->rows[$i][3] && !empty($this->rows[$i][3])) {
                $text_type = "Type: {$type_array[$this->rows[$i][3]-1]}";
                if (isset($this->url) && !empty($this->url)) {
                    switch (true) {
                        case ($this->rows[$i][3] == 5):
                            $replace = str_replace('showtext.php', 'showvideo.php', $this->url);
                            $link = empty($replace) ? '' : "<a href ='{$replace}?id=$text_id'>";
                            break;
                        default:
                            $link = empty($this->url) ? '' : "<a href ='{$this->url}?id=$text_id'>";
                            break;
                    }
                }
            } else {
                $text_type = '';

            }
            
            $html .= '<tr><td class="col-checkbox"><label><input class="chkbox-selrow" type="checkbox" data-idText="' .
            $text_id . '"></label></td><td class="col-title">' . $link .
            $text_title . '</a><br/><small>' . $text_type . $text_author . '</small></td></tr>';
        }
        return $html;
    }
}

class WordTable extends Table {
    protected function print_content() {
        $html = '';
            
        for ($i=0; $i < sizeof($this->rows); $i++) { 
            $word_id = $this->rows[$i][0];
            $word = $this->rows[$i][1];
            $word_status = $this->rows[$i][2];
            $status = array('fa-hourglass-end status_learned', 'fa-hourglass-half status_learning', 'fa-hourglass-start status_new');
            $status_text = array('Learned', 'Learning', 'New');

            $html .= '<tr><td class="col-checkbox"><label><input class="chkbox-selrow" type="checkbox" data-idWord="' .
                $word_id . '"></label></td><td class="col-title">' . 
                $word . '</td><td class="col-status text-center">' .
                '<i title="' . $status_text[$word_status] . '" class="fas ' . $status[$word_status] . '"></i></td></tr>';
        }
        return $html;
    }
}

?>