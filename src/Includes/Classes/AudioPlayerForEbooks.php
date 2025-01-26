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

class AudioPlayerForEbooks extends AudioPlayer
{
    /**
     * Prints audio player html for Ebooks
     *
     * @return string
     */
    public function show(): string
    {
        $audio_url = $this->audio_uri;
        $audio_mime_type = $this->getAudioMimeType();

        return <<<AUDIOPLAYER_CONTAINER
            <hr>
            <div id="audioplayer-container" class="container mb-3">
                <audio id="audioplayer" preload="auto">
                    <source id="audio-source" src="$audio_url" type="$audio_mime_type">
                    Your browser does not support the audio element.
                </audio>

                <div class="d-flex align-items-center">
                    <button id="ap-play-btn" class="btn btn-primary">
                        <i id="ap-play-btn-icon" class="bi bi-play-fill"></i>
                    </button>

                    <div class="flex-grow-1 px-1">
                        <div id="ap-progress-bar-container" class="progress flex-grow-1 border border-secondary">
                            <div id="ap-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 0%"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div id="ap-elapsedtime-stamp" class="small">
                            00:00
                            </div>
                            <div id="ap-totaltime-stamp" class="small">
                            00:00
                            </div>
                        </div>
                    </div>

                    <div class="dropdown">
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
                </div>
            </div>
            AUDIOPLAYER_CONTAINER;
    }
}
