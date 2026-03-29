<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class SharedTextTable extends TextTable
{
    /**
     * Constructor
     *
     * @param array $rows
     */
    public function __construct(array $rows)
    {
        parent::__construct($rows, false);
        $this->headings = ['Title'];
        $this->col_widths = ['69px', ''];
        $this->action_menu = [];
        $this->sort_menu = [
            'mSortByNew' => 'New first',
            'mSortByOld' => 'Old first',
            'mSortByMoreLikes' => 'More likes first',
            'mSortByLessLikes' => 'Less likes first'
        ];
        $this->is_shared = true;
        $this->has_chkbox = false;
    } // end __construct()
}
