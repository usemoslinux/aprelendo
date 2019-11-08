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

class Pagination 
{
  private $limit        = 0; // number of rows per page
  private $adjacents    = 0; // adjacent page numbers
  private $total_rows   = 0; // total number of rows
  private $total_pages  = 0; // total number of pages
  private $page         = 0; // current page
  private $offset       = 0; // offset used to retrieve rows
  private $start        = 0; // beginning of range
  private $end          = 0; // end of range

  /**
   * Constructor
   *
   * @param int $page Current page number
   * @param int $limit How many rows to show
   * @param int $total_rows Total amount of rows
   * @param int $adjacents How many adjacent pages to show in pagination
   */
  public function __construct(int $page = 1, int $limit = 10, int $total_rows, int $adjacents = 2) {
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
   * @param int $sort_by
   * @param int $filter
   * @param int $show_archived
   * @return string HTML of pagination
   */
  public function print(string $url, string $search_text, int $sort_by, 
                        int $filter = -1, int $show_archived = -1): string {
    
    $result = '';
    $search_text = urlencode($search_text);
    if (-1 !== $show_archived) {
        $show_archived = $show_archived ? 1 : 0;
    }
    
    // build query string
    $s = !empty($search_text) ? "s=$search_text&" : '';
    $o = !empty($sort_by) ? "o=$sort_by&" : '';
    $f = (-1 !== $filter) ? "f=$filter&" : '';
    $sa = (-1 !== $show_archived) ? "sa=$show_archived&" : '';
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
     }
     return $result;
  } // end print()

  /**
   * Get the value of offset
   * @return int
   */ 
  public function getOffset(): int
  {
    return $this->offset;
  }
}

?>