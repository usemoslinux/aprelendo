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

namespace Aprelendo\Includes\Classes;

class Pagination 
{
  private $limit = 10;    // number of rows per page
  private $adjacents = 2; // adjacent page numbers
  private $total_rows;    // total number of rows
  private $total_pages;   // total number of pages
  private $page = 1;      // current page
  public  $offset = 0;    // offset used to retrieve rows
  private $start;         // beginning of range
  private $end;           // end of range

  /**
   * Constructor
   *
   * @param integer $page Current page number
   * @param integer $limit How many rows to show
   * @param integer $total_rows Total amount of rows
   * @param integer $adjacents How many adjacent pages to show in pagination
   */
  public function __construct($page = 1, $limit = 10, $total_rows, $adjacents = 2) {
    $this->limit = $limit;
    $this->adjacents = $adjacents;
    $this->total_rows = $total_rows;
    $this->total_pages = ceil($total_rows / $limit);

    if (isset($page) && $page != '') {
      $this->page = $page;
      $this->offset = ($page - 1) * $limit;
    }
    
    // Generate the range of the page numbers which will be displayed
    if($this->total_pages <= (1+($this->adjacents * 2))) {
      $this->start = 1;
      $this->end   = $this->total_pages;
    } else {
      if(($this->page - $this->adjacents) > 1) { 
        if(($this->page + $this->adjacents) < $this->total_pages) { 
          $this->start = ($this->page - $this->adjacents);            
          $this->end   = ($this->page + $this->adjacents);         
        } else {             
          $this->start = ($this->total_pages - (1+($this->adjacents*2)));  
          $this->end   = $this->total_pages;               
        }
      } else {               
        $this->start = 1;                                
        $this->end   = (1+($this->adjacents * 2));             
      }
    }
  } // end __construct()

  /**
   * Prints pagination selector
   *
   * @param string $url
   * @param string $search_text
   * @param integer $sort_by
   * @param integer $filter
   * @param integer $show_archived
   * @return string HTML of pagination
   */
  public function print($url, $search_text, $sort_by, $filter = NULL, $show_archived = NULL) {
    $search_text = urlencode($search_text);
    if (!is_null($show_archived)) {
        $show_archived = $show_archived ? 1 : 0;
    }
    
    // build query string
    $s = !empty($search_text) ? "s=$search_text&" : '';
    $o = !empty($sort_by) ? "o=$sort_by&" : '';
    $f = !is_null($filter) ? "f=$filter&" : '';
    $sa = !is_null($show_archived) ? "sa=$show_archived&" : '';
    $query = "?$s$o$f$sa";

    if($this->total_pages > 1) { 
      $result = 
      "<nav aria-label='Page navigation'>
        <div class='text-center'>
          <ul class='pagination pagination-sm justify-content-center'>
            <!-- Link of the first page -->
            <li class='page-item " . ($this->page <= 1 ? ' disabled ' : ' ') . "'>
              <a class='page-link' href='$url" . $query . "p=1'>
              &lt;&lt;</a>
            </li>
            <!-- Link of the previous page -->
            <li class='page-item " . ($this->page <= 1 ? ' disabled ' : ' ') . "'>
              <a class='page-link' href='$url" . $query . "p=" . ($this->page>1 ? $this->page-1 : 1) . "'>
              &lt;</a>
            </li>
            <!-- Links of the pages with page number -->";

        for($i=$this->start; $i<=$this->end; $i++) {
          $result .= 
          "<li class='page-item " . ($i == $this->page ? ' active ' : ' ') . "'>
            <a class='page-link' href='$url" . $query . "p=$i'>$i</a>
          </li>";
        }
        
        $result .= 
          "<!-- Link of the next page -->
            <li class='page-item " . ($this->page >= $this->total_pages ? ' disabled ' : ' ') . "'>
              <a class='page-link' href='$url" . $query . "p=" . ($this->page < $this->total_pages ? $this->page+1 : $this->total_pages) . "'>&gt;</a>
            </li>
          <!-- Link of the last page -->
            <li class='page-item " . ($this->page >= $this->total_pages ? ' disabled ' : ' ') . "'>
              <a class='page-link' href='$url" . $query . "p=$this->total_pages'>&gt;&gt;</a>
            </li>
          </ul>
        </div>
      </nav>";

      return $result;
     }
  } // end print()
}

?>