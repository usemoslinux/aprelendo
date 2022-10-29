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

use Aprelendo\Includes\Classes\File;

class EbookFile extends File
{
    /**
     * Constructor
     * @param string $file_name
     * @param bool $owned_by_premium_user
     */
    public function __construct(string $file_name, bool $owned_by_premium_user)
    {
        parent::__construct($file_name);
        $this->allowed_extensions = array('epub');
        if ($owned_by_premium_user) {
            $this->max_size = 2097152; // 2 MB
        } else {
            $this->max_size = 0; // ebook uploading is not allowed for non-premium users
        }
    } // end __construct()
}
