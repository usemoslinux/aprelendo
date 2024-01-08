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

use Aprelendo\UserException;

class AudioPlayer
{
    protected string $audio_uri;

    /**
     * Constructor
     * Calls createFullReader or createMiniReader depending on the nr. of args
     */
    public function __construct(string $audio_url)
    {
        $this->audio_uri = $audio_url;
    } // end __construct()

    /**
     * Return audio MIME type based on URI file extension
     *
     * @return string
     */
    protected function getAudioMimeType(): string {
        // Get file extension
        $file_extension = pathinfo($this->audio_uri, PATHINFO_EXTENSION);

        // Map file extensions to audio types
        $audio_types = array(
            'mp3'  => 'audio/mpeg',
            'ogg'  => 'audio/ogg',
            'wav'  => 'audio/wav',
            'aac'  => 'audio/aac',
            'm4a'  => 'audio/x-m4a',
            'webm' => 'audio/webm',
            'flac' => 'audio/flac',
            'opus' => 'audio/opus',
        );

        // Set the appropriate MIME type based on the file extension
        return isset($audio_types[$file_extension]) ? $audio_types[$file_extension] : 'audio/mpeg';
    }
}
