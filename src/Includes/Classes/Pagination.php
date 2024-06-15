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

namespace Aprelendo;

use Aprelendo\Url;

class Pagination
{
    private $adjacents    = 0; // adjacent page numbers
    private $total_pages  = 0; // total number of pages
    private $page         = 0; // current page
    private $start        = 0; // beginning of range
    private $end          = 0; // end of range
    public $offset        = 0; // offset used to retrieve rows

    /**
     * Constructor
     *
     * @param int $total_rows Total amount of rows
     * @param int $page Current page number
     * @param int $limit How many rows to show
     * @param int $adjacents How many adjacent pages to show in pagination
     */
    public function __construct(int $total_rows, int $page = 1, int $limit = 10, int $adjacents = 2)
    {
        $this->adjacents = $adjacents;
        $this->total_pages = ceil($total_rows / $limit);

        if (!empty($page)) {
            $this->page = $page;
            $this->offset = ($page - 1) * $limit;
        }

        $this->setStartEndPageRange();
    } // end __construct()


    /**
     * Calculates and sets the start and end page numbers to create a range of page numbers for display
     *
     * @return void
     */
    private function setStartEndPageRange(): void {
        if ($this->total_pages <= (1 + ($this->adjacents * 2))) {
            $this->start = 1;
            $this->end   = $this->total_pages;
        } else {
            if (($this->page - $this->adjacents) > 1) {
                if (($this->page + $this->adjacents) < $this->total_pages) {
                    $this->start = ($this->page - $this->adjacents);
                    $this->end   = ($this->page + $this->adjacents);
                } else {
                    $this->start = ($this->total_pages - (1 + ($this->adjacents * 2)));
                    $this->end   = $this->total_pages;
                }
            } else {
                $this->start = 1;
                $this->end   = (1 + ($this->adjacents * 2));
            }
        }
    }

    /**
     * Prints pagination
     *
     * @param Url $url
     * @return string HTML of pagination
     */
    public function print(Url $url): string {
        $result = '';
        $base_url = $url->base_url;
        $query_str = $url->query_str;

        if ($this->total_pages > 1) {
            $result  = $this->createPaginationHeader();
            $result .= $this->createFirstPageLink($base_url, $query_str);
            $result .= $this->createPrevPageLink($base_url, $query_str);
            $result .= $this->createInBetweenPagesLinks($base_url, $query_str);
            $result .= $this->createNextPageLink($base_url, $query_str);
            $result .= $this->createLastPageLink($base_url, $query_str);
            $result .= $this->createPaginationFooter();
        }

        return $result;
    } // end print()

    /**
     * Create pagination header HTML code
     *
     * @return string
     */
    private function createPaginationHeader(): string {
        return "<nav aria-label='Page navigation'>
                <div class='text-center'>
                <ul class='pagination pagination-sm justify-content-center'>";
    }

    /**
     * Create first page link HTML code for pagination
     *
     * @param string $url
     * @param string $query
     * @return string
     */
    private function createFirstPageLink(string $url, string $query): string {
        return  "<!-- Link of the first page -->
                <li class='page-item " . ($this->page <= 1 ? ' disabled ' : ' ') . "'>
                <a class='page-link' href='$url" . $query . "p=1'>&lt;&lt;</a>
                </li>";
    }

    /**
     * Create previous page link HTML code for pagination
     *
     * @param string $url
     * @param string $query
     * @return string
     */
    private function createPrevPageLink(string $url, string $query): string {
        return  "<!-- Link of the previous page -->
                <li class='page-item " . ($this->page <= 1 ? ' disabled ' : ' ') . "'>
                <a class='page-link' href='$url" . $query . "p=" . ($this->page > 1 ? $this->page - 1 : 1) . "'>
                &lt;</a>
                </li>";
    }

    /**
     * Create in between pages links HTML code for pagination
     *
     * @param string $url
     * @param string $query
     * @return string
     */
    private function createInBetweenPagesLinks(string $url, string $query): string {
        $html = '';

        for ($i = $this->start; $i <= $this->end; $i++) {
            $html .=
                "<li class='page-item " . ($i == $this->page ? ' active ' : ' ') . "'>
                <a class='page-link' href='$url" . $query . "p=$i'>$i</a>
                </li>";
        }
        
        return $html;
    }

    /**
     * Create next page link HTML code for pagination
     *
     * @param string $url
     * @param string $query
     * @return string
     */
    private function createNextPageLink(string $url, string $query): string {
        return  "<!-- Link of the next page -->
                <li class='page-item " . ($this->page >= $this->total_pages ? ' disabled ' : ' ') . "'>
                <a class='page-link' href='$url"
                . $query
                . "p="
                . ($this->page < $this->total_pages ? $this->page + 1 : $this->total_pages) . "'>&gt;</a>
                </li>";
    }

    /**
     * Create last page link HTML code for pagination
     *
     * @param string $url
     * @param string $query
     * @return string
     */
    private function createLastPageLink(string $url, string $query): string {
        return  "<!-- Link of the last page -->
                <li class='page-item " . ($this->page >= $this->total_pages ? ' disabled ' : ' ') . "'>
                <a class='page-link' href='$url" . $query . "p=$this->total_pages'>&gt;&gt;</a>
                </li>";
    }

    /**
     * Create pagination footer HTML code
     *
     * @return string
     */
    private function createPaginationFooter(): string {
        return "</ul></div></nav>";
    }
}
