<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class AudioFile extends File
{
    /**
     * Constructor
     * @param string $file_name
     */
    public function __construct(string $file_name)
    {
        parent::__construct($file_name);
        $this->allowed_extensions = ['mp3', 'ogg'];
        $this->max_size = 67108864; // 64 MB
    } // end __construct()
}
