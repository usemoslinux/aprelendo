<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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
    } 

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
        return $audio_types[$file_extension] ?? 'audio/mpeg';
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
        $query = parse_url($uri, PHP_URL_QUERY) ?? '';
        if (!empty($query)) {
            parse_str($query, $query_params);
            $rss_query_keys = ['format', 'type', 'feed'];
            foreach ($rss_query_keys as $query_key) {
                if (strtolower((string)($query_params[$query_key] ?? '')) === 'rss') {
                    return true;
                }
            }
        }

        $path = parse_url($uri, PHP_URL_PATH) ?? '';
        return str_contains($path, '/rss/')
            || str_contains($path, '/feed/')
            || str_ends_with($path, '/rss')
            || str_ends_with($path, '/feed');
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
