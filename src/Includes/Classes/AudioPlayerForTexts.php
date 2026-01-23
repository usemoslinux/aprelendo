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

class AudioPlayerForTexts extends AudioPlayer
{
    /**
     * Prints audio player html for Texts
     *
     * @param string $display_mode
     * @param bool $show_loading
     * @return string
     */
    public function show(string $display_mode, bool $show_loading): string
    {
        $audio_url = $this->audio_uri;
        $playlist_attr = '';
        $playlist_type = $this->getPlaylistType();
        if ($playlist_type !== '') {
            $audio_url = '';
            $playlist_src = htmlspecialchars($this->audio_uri, ENT_QUOTES);
            $playlist_attr = ' data-playlist-src="' . $playlist_src . '" data-playlist-type="' . $playlist_type . '"';
        }
        $audio_url = htmlspecialchars($audio_url, ENT_QUOTES);
        $audio_mime_type = $this->getAudioMimeType();

        // $html = '<hr>';

        $html = '<div id="alert-box-audio" class="alert alert-danger d-none"></div>';

        if ($show_loading) {
            $html .= '<div id="audioplayer-loader" class="audio-loading mx-auto" title="Loading audio...">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>';
        }
        
        $display_mode_css = $display_mode . 'mode';
        $audio_controls_class = $show_loading ? 'd-none' : '';

        $html .= <<<AUDIOPLAYER_CONTAINER
            <div id="audioplayer-container" class="$display_mode_css $audio_controls_class">
                <audio id="audioplayer" preload="auto"$playlist_attr>
                    <source id="audio-source" src="$audio_url" type="$audio_mime_type">
                    Your browser does not support the audio element.
                </audio>

                <div class="d-flex align-items-center my-3">
                    <button id="ap-play-btn" class="btn btn-primary">
                        <i id="ap-play-btn-icon" class="bi bi-play-fill"></i>
                    </button>

                    <div class="flex-grow-1 px-1">
                        <div id="ap-progress-bar-container" class="progress flex-grow-1 border border-secondary">
                            <div id="ap-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <div id="ap-elapsedtime-stamp" class="small">00:00</div>
                            <div id="ap-hovertime-stamp" class="small"></div>
                            <div id="ap-totaltime-stamp" class="small">00:00</div>
                        </div>
                    </div>

                    <div class="dropdown me-1">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="ap-speed-menu"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-speedometer"></i>
                    </button>
                    <ul class="dropdown-menu" id="ap-speed-menu" aria-labelledby="ap-speed-menu">
                        <li><a class="dropdown-item" href="#" data-speed="0.5">0.5x</a></li>
                        <li><a class="dropdown-item" href="#" data-speed="0.75">0.75x</a></li>
                        <li><a class="dropdown-item active" href="#" data-speed="1">1x</a></li>
                        <li><a class="dropdown-item" href="#" data-speed="1.25">1.25x</a></li>
                        <li><a class="dropdown-item" href="#" data-speed="1.5">1.5x</a></li>
                        <li><a class="dropdown-item" href="#" data-speed="1.75">1.75x</a></li>
                        <li><a class="dropdown-item" href="#" data-speed="2">2x</a></li>
                    </ul>
                    </div>
                <button data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-title="Loop audio from point A to point B, click to set point A"
                    id="ap-abloop-btn" class="btn btn-outline-warning">
                    A
                </button>
                <button id="ap-autopause-btn" class="btn btn-outline-secondary ms-1" type="button"
                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                    data-bs-title="Auto-pause while typing: Off" aria-pressed="false">
                    <i class="bi bi-keyboard"></i>
                </button>
                </div>
                <div id="ap-chapter-controls" class="d-none d-flex align-items-center gap-2 mt-2">
                    <button id="ap-prev-chapter" class="btn btn-outline-secondary btn-sm" type="button">
                        <i class="bi bi-skip-backward-fill"></i>
                    </button>
                    <select id="ap-chapter-select" class="form-select form-select-sm flex-grow-1"></select>
                    <button id="ap-next-chapter" class="btn btn-outline-secondary btn-sm" type="button">
                        <i class="bi bi-skip-forward-fill"></i>
                    </button>
                </div>
            </div>
            AUDIOPLAYER_CONTAINER;
        
        // $html .= '<hr>';

        return $html;
    }
}
