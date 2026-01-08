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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Aprelendo;

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
        $file_extension = $this->getFileExtension();

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

    /**
     * Check if the current URI points to an M3U playlist
     *
     * @return bool
     */
    protected function isM3uPlaylist(): bool
    {
        return in_array($this->getFileExtension(), ['m3u', 'm3u8'], true);
    }

    /**
     * Check if the current URI points to an RSS feed
     *
     * @return bool
     */
    protected function isRssFeed(): bool
    {
        $extension = $this->getFileExtension();
        if (in_array($extension, ['rss', 'xml'], true)) {
            return true;
        }

        $uri = strtolower($this->audio_uri);
        if (preg_match('/[?&](format|type|feed)=rss\b/', $uri)) {
            return true;
        }

        return (bool)preg_match('#/(rss|feed)(/|$)#', $uri);
    }

    /**
     * Return playlist type if supported
     *
     * @return string
     */
    protected function getPlaylistType(): string
    {
        if ($this->isM3uPlaylist()) {
            return 'm3u';
        }
        if ($this->isRssFeed()) {
            return 'rss';
        }

        return '';
    }

    /**
     * Extract file extension from URI path
     *
     * @return string
     */
    protected function getFileExtension(): string
    {
        $path = parse_url($this->audio_uri, PHP_URL_PATH) ?? '';
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return strtolower($extension);
    }
}
