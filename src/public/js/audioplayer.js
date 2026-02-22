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

const AudioController = (() => {
    const audio = document.getElementById('audioplayer');
    const audio_source = document.getElementById('audio-source');
    const play_pause_btn = document.getElementById('ap-play-btn');
    const play_pause_btn_icon = document.getElementById('ap-play-btn-icon');
    const progress_bar = document.getElementById('ap-progress-bar');
    const progress_bar_container = document.getElementById('ap-progress-bar-container');
    const elapsed_time_stamp = document.getElementById('ap-elapsedtime-stamp');
    const hover_time_stamp = document.getElementById('ap-hovertime-stamp');
    const total_time_stamp = document.getElementById('ap-totaltime-stamp');
    const speed_menu_items = document.querySelectorAll('#ap-speed-menu .dropdown-item');
    const ab_loop_btn = document.getElementById("ap-abloop-btn");
    const chapter_controls = document.getElementById('ap-chapter-controls');
    const chapter_select = document.getElementById('ap-chapter-select');
    const prev_chapter_btn = document.getElementById('ap-prev-chapter');
    const next_chapter_btn = document.getElementById('ap-next-chapter');

    const playlist_src = audio ? (audio.dataset.playlistSrc || '') : '';
    const playlist_type = audio ? (audio.dataset.playlistType || '') : '';
    const normalized_playlist_src = playlist_src.trim();
    const normalized_playlist_type = playlist_type.trim().toLowerCase();
    const playlist_enabled = normalized_playlist_src !== '';
    const playlist_kind = normalized_playlist_type !== '' ? normalized_playlist_type : 'm3u';

    let resume_audio = false;
    let ab_loop_start = -1;
    let ab_loop_end = -1;
    let playlist_tracks = [];
    let playlist_index = 0;
    let playlist_ready = false;
    let pending_seek_time = null;
    let pending_playlist_position = null;
    let pending_autoplay = false;
    let pending_play_after_load = false;

    // Default functions (do nothing if audio is not defined)
    let play = () => {};
    let stop = () => {};
    let pause = () => {};
    let resume = () => {};
    let togglePlayPause = () => {};
    let playFromBeginning = () => {};
    let isPlaylist = () => false;
    let setPlaylistPositionFromString = () => {};
    let getPlaylistPositionString = () => '';
    let setSpeed = () => {};
    let getSpeed = () => 1;
    let seekRelative = () => {};
    
    // If the audio element exists, redefine the functions
    if (audio) {
        if (play_pause_btn_icon) {
            const has_audio_src = audio_source && audio_source.getAttribute('src');
            if (playlist_enabled || has_audio_src) {
                play_pause_btn_icon.className = 'spinner-border spinner-border-sm';
            }
        }

        play = () => {
            if (playlist_enabled && !playlist_ready) {
                pending_autoplay = true;
                return;
            }
            audio.play();
        };

        stop = () => {
            audio.pause();
            audio.currentTime = 0;
        };

        pause = (resume) => {
            resume_audio = audio.paused && !resume_audio ? false : resume;
            audio.pause();
        };

        togglePlayPause = () => {
            if (audio.paused || audio.ended) {
                play();
            } else {
                audio.pause();
            }
        };

        resume = () => {
            if (resume_audio) {
                play();
                resume_audio = false;
            }
        };

        playFromBeginning = () => {
            audio.pause();
            audio.currentTime = 0;
            play();
        };

        isPlaylist = () => playlist_enabled;

        const resolvePlaylistUrl = (url, base_url) => {
            try {
                return new URL(url, base_url).href;
            } catch (e) {
                return url;
            }
        };

        const parseM3u = (m3u_text, base_url) => {
            const lines = m3u_text.split(/\r?\n/);
            const tracks = [];
            let current_title = '';

            lines.forEach((line) => {
                const trimmed = line.trim();
                if (!trimmed) {
                    return;
                }
                if (trimmed.startsWith('#EXTINF')) {
                    const parts = trimmed.split(',');
                    current_title = parts.length > 1 ? parts.slice(1).join(',').trim() : '';
                    return;
                }
                if (trimmed.startsWith('#')) {
                    return;
                }

                const track_url = resolvePlaylistUrl(trimmed, base_url);
                const title = current_title || `Chapter ${tracks.length + 1}`;
                tracks.push({ title, url: track_url });
                current_title = '';
            });

            return tracks;
        };

        const parseRss = (rss_text, base_url) => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(rss_text, 'application/xml');
            if (doc.getElementsByTagName('parsererror').length > 0) {
                return [];
            }

            const items = Array.from(doc.querySelectorAll('item, entry'));
            const tracks = [];

            items.forEach((item) => {
                const title_node = item.querySelector('title');
                let title = title_node ? title_node.textContent.trim() : '';
                let url = '';

                const enclosure = item.querySelector('enclosure');
                if (enclosure && enclosure.getAttribute('url')) {
                    url = enclosure.getAttribute('url');
                }

                if (!url) {
                    const media = item.querySelector('media\\:content');
                    if (media && media.getAttribute('url')) {
                        url = media.getAttribute('url');
                    }
                }

                if (!url) {
                    const link_nodes = Array.from(item.querySelectorAll('link'));
                    link_nodes.some((link) => {
                        const rel = (link.getAttribute('rel') || '').toLowerCase();
                        if (rel === 'enclosure' && link.getAttribute('href')) {
                            url = link.getAttribute('href');
                            return true;
                        }
                        return false;
                    });
                }

                if (!url) {
                    return;
                }

                if (!title) {
                    title = `Chapter ${tracks.length + 1}`;
                }

                tracks.push({ title, url: resolvePlaylistUrl(url, base_url) });
            });

            return tracks;
        };

        const updateChapterControls = () => {
            if (!chapter_controls || !chapter_select) {
                return;
            }

            if (!playlist_enabled || playlist_tracks.length === 0) {
                chapter_controls.classList.add('d-none');
                return;
            }

            chapter_select.innerHTML = '';
            playlist_tracks.forEach((track, index) => {
                const option = document.createElement('option');
                option.value = String(index);
                option.textContent = track.title;
                chapter_select.appendChild(option);
            });

            chapter_select.value = String(playlist_index);
            chapter_controls.classList.remove('d-none');
            if (prev_chapter_btn) {
                prev_chapter_btn.disabled = playlist_index === 0;
            }
            if (next_chapter_btn) {
                next_chapter_btn.disabled = playlist_index >= playlist_tracks.length - 1;
            }
        };

        const applyPendingSeek = () => {
            if (pending_seek_time !== null) {
                audio.currentTime = pending_seek_time;
                pending_seek_time = null;
            }
        };

        const loadTrack = (index, start_time = 0, autoplay = false) => {
            if (!playlist_enabled || !playlist_tracks.length) {
                return;
            }

            const clamped_index = Math.min(Math.max(index, 0), playlist_tracks.length - 1);
            playlist_index = clamped_index;
            const track = playlist_tracks[clamped_index];

            if (!track || !track.url) {
                return;
            }

            pending_play_after_load = autoplay;
            if (play_pause_btn) {
                play_pause_btn.classList = 'btn btn-primary';
            }
            if (play_pause_btn_icon) {
                play_pause_btn_icon.className = 'spinner-border spinner-border-sm';
            }
            if (progress_bar) {
                progress_bar.style.width = '0%';
            }
            if (elapsed_time_stamp) {
                elapsed_time_stamp.textContent = '00:00';
            }
            if (total_time_stamp) {
                total_time_stamp.textContent = '';
            }

            audio_source.src = track.url;
            audio.load();
            pending_seek_time = start_time;
            updateChapterControls();

            if (autoplay) {
                audio.play();
            }
        };

        const setPlaylistPosition = (index, seconds) => {
            if (!playlist_enabled) {
                return;
            }
            if (!playlist_ready) {
                pending_playlist_position = { index, seconds };
                return;
            }
            loadTrack(index, seconds, false);
        };

        setPlaylistPositionFromString = (position) => {
            if (!playlist_enabled || !position) {
                return;
            }
            const parts = String(position).split('|');
            if (parts.length !== 2) {
                return;
            }
            const index = parseInt(parts[0], 10);
            const seconds = parseFloat(parts[1]);
            if (Number.isNaN(index) || Number.isNaN(seconds)) {
                return;
            }
            setPlaylistPosition(index, seconds);
        };

        getPlaylistPositionString = () => {
            if (!playlist_enabled) {
                return '';
            }
            const safe_time = Number.isFinite(audio.currentTime) ? Math.floor(audio.currentTime) : 0;
            return `${playlist_index}|${safe_time}`;
        };

        const showPlaylistError = (message) => {
            play_pause_btn.classList = 'btn btn-danger disabled';
            play_pause_btn_icon.className = 'bi bi-play-fill';
            elapsed_time_stamp.textContent = message;
            total_time_stamp.textContent = '';
        };

        /**
         * Fetches a playlist directly from its URL.
         *
         * @param {string} playlist_url
         * @returns {Promise<{text: string, url: string}>}
         */
        const fetchPlaylist = (playlist_url) => {
            return fetch(playlist_url, { cache: 'no-store' })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch playlist.');
                    }
                    return response.text().then((text) => ({
                        text,
                        url: response.url || playlist_url
                    }));
                });
        };

        /**
         * Fetches a playlist through server-side proxy endpoints.
         *
         * @param {string} playlist_url
         * @returns {Promise<{text: string, url: string}>}
         */
        const fetchPlaylistViaProxy = (playlist_url) => {
            const proxy_endpoint = playlist_kind === 'rss' ? '/ajax/fetchrss.php' : '/ajax/fetchm3u.php';
            const payload_key = playlist_kind === 'rss' ? 'rss' : 'm3u';
            const request_url = `${proxy_endpoint}?url=${encodeURIComponent(playlist_url)}`;
            return fetch(request_url)
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        throw new Error(data.error_msg);
                    }
                    return {
                        text: data.payload && data.payload[payload_key] ? data.payload[payload_key] : '',
                        url: data.payload && data.payload.url ? data.payload.url : playlist_url
                    };
                });
        };

        /**
         * Determines if a URL points to a different origin than the current page.
         *
         * @param {string} url
         * @returns {boolean}
         */
        const isCrossOriginUrl = (url) => {
            try {
                const parsed_url = new URL(url, window.location.href);
                return parsed_url.origin !== window.location.origin;
            } catch (error) {
                return false;
            }
        };

        /**
         * Initializes playlist loading and sets the first track in the player.
         *
         * @returns {void}
         */
        const initPlaylist = () => {
            if (!playlist_enabled) {
                return;
            }

            const must_use_proxy_first = playlist_kind === 'rss' || isCrossOriginUrl(normalized_playlist_src);
            const fetch_playlist_promise = must_use_proxy_first
                ? fetchPlaylistViaProxy(normalized_playlist_src).catch(() => fetchPlaylist(normalized_playlist_src))
                : fetchPlaylist(normalized_playlist_src).catch(() => fetchPlaylistViaProxy(normalized_playlist_src));

            fetch_playlist_promise
                .then(({ text, url }) => {
                    playlist_tracks = playlist_kind === 'rss'
                        ? parseRss(text, url)
                        : parseM3u(text, url);
                    playlist_ready = playlist_tracks.length > 0;
                    if (!playlist_ready) {
                        showPlaylistError('No playable chapters found.');
                        return;
                    }

                    if (pending_playlist_position) {
                        loadTrack(pending_playlist_position.index, pending_playlist_position.seconds, false);
                        pending_playlist_position = null;
                    } else {
                        loadTrack(0, 0, false);
                    }

                    if (pending_autoplay) {
                        pending_autoplay = false;
                        audio.play();
                    }
                })
                .catch(() => {
                    showPlaylistError('Error loading playlist!');
                });
        };

        const playbackProgressUpdate = () => {
            if (!Number.isFinite(audio.duration) || audio.duration <= 0) {
                progress_bar.style.width = '0%';
                elapsed_time_stamp.textContent = '00:00';
                total_time_stamp.textContent = '';
                return;
            }

            let progress = (audio.currentTime / audio.duration) * 100;
            progress_bar.style.width = `${progress}%`;

            let currentHours = Math.floor(audio.currentTime / 3600);
            let currentMinutes = Math.floor((audio.currentTime % 3600) / 60);
            let currentSeconds = Math.floor(audio.currentTime % 60);

            let durationHours = Math.floor(audio.duration / 3600);
            let durationMinutes = Math.floor((audio.duration % 3600) / 60);
            let durationSeconds = Math.floor(audio.duration % 60);

            let currentTimeDisplay = currentHours > 0 ? `${currentHours}:` : '';
            currentTimeDisplay += `${currentMinutes < 10 ? '0' : ''}${currentMinutes}:${currentSeconds < 10 ? '0' : ''}${currentSeconds}`;

            let durationTimeDisplay = durationHours > 0 ? `${durationHours}:` : '';
            durationTimeDisplay += `${durationMinutes < 10 ? '0' : ''}${durationMinutes}:${durationSeconds < 10 ? '0' : ''}${durationSeconds}`;

            elapsed_time_stamp.textContent = currentTimeDisplay;
            total_time_stamp.textContent = durationTimeDisplay;
        };

        // Change Playback Speed
        setSpeed = (speed) => {
            if (!Number.isFinite(speed)) {
                return;
            }
            audio.playbackRate = speed;

            // Update active class
            document.querySelectorAll('#ap-speed-menu .dropdown-item').forEach(option => {
                if (parseFloat(option.getAttribute('data-speed')) === speed) {
                    option.classList.add('active');
                } else {
                    option.classList.remove('active');
                }
            });
        };

        getSpeed = () => audio.playbackRate;

        seekRelative = (seconds) => {
            if (!Number.isFinite(seconds)) {
                return;
            }
            const duration = audio.duration;
            const target_time = audio.currentTime + seconds;
            if (Number.isFinite(duration)) {
                audio.currentTime = Math.min(Math.max(target_time, 0), duration);
            } else {
                audio.currentTime = Math.max(target_time, 0);
            }
        };

        // event listeners
        
        play_pause_btn.addEventListener('click', togglePlayPause);
        audio.addEventListener('loadedmetadata', playbackProgressUpdate);
        audio.addEventListener('loadedmetadata', applyPendingSeek);

        audio.addEventListener('timeupdate', () => {
            if (ab_loop_end > -1) {
                if(audio.currentTime >= ab_loop_end) {
                    audio.currentTime = ab_loop_start;
                }
            }
            playbackProgressUpdate();
        });

        progress_bar_container.addEventListener('click', (e) => {
            const rect = progress_bar_container.getBoundingClientRect();
            const click_position = e.clientX - rect.left;
            const click_percentage = click_position / rect.width;
            audio.currentTime = click_percentage * audio.duration;
        });

        if (window.matchMedia('(hover: hover)').matches) {
            progress_bar_container.addEventListener('mousemove', (e) => {
                const rect = progress_bar_container.getBoundingClientRect();
                const hover_position = e.clientX - rect.left;
                const hover_percentage = hover_position / rect.width;
                const hover_time = Math.max(0, Math.floor(hover_percentage * audio.duration));
            
                const hours = Math.floor(hover_time / 3600);
                const minutes = Math.floor((hover_time % 3600) / 60);
                const seconds = hover_time % 60;
            
                hover_time_stamp.textContent = 
                    hours > 0 
                        ? `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}` 
                        : `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                hover_time_stamp.style.left = `${hover_position}px`;
                hover_time_stamp.style.display = 'block';
            });

            progress_bar_container.addEventListener('mouseleave', (e) => {
                hover_time_stamp.style.display = 'none';
            });
        }

        speed_menu_items.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();

                const target = e.target;
        
                if (target.hasAttribute('data-speed')) {
                    const speed = parseFloat(target.getAttribute('data-speed'));
                    setSpeed(speed);
                }
            });
        });

        audio.addEventListener('play', () => {
            pending_play_after_load = false;
            play_pause_btn_icon.className = 'bi bi-pause-fill';
        });

        audio.addEventListener('pause', () => {
            pending_play_after_load = false;
            play_pause_btn_icon.className = 'bi bi-play-fill';
        });

        audio.addEventListener('ended', () => {
            if (playlist_enabled && playlist_tracks.length > 0 && playlist_index < playlist_tracks.length - 1) {
                loadTrack(playlist_index + 1, 0, true);
                return;
            }
            audio.currentTime = 0;
            play_pause_btn_icon.className = 'bi bi-play-fill';
        });

        audio_source.addEventListener('error', () => {
            if (audio_source.src !== '' && audio_source.src !== window.location.href) {
                pending_play_after_load = false;
                play_pause_btn.classList = 'btn btn-danger disabled';
                play_pause_btn_icon.className = 'bi bi-play-fill';
                elapsed_time_stamp.textContent = 'Error loading audio!';
                total_time_stamp.textContent = '';
            }
        });

        // When metadata is loaded, replace spinner with play icon
        audio.addEventListener('loadedmetadata', () => {
            if (pending_play_after_load) {
                return;
            }
            if (audio.paused || audio.ended) {
                play_pause_btn_icon.className = 'bi bi-play-fill';
            } else {
                play_pause_btn_icon.className = 'bi bi-pause-fill';
            }
        });

        if (chapter_select) {
            chapter_select.addEventListener('change', () => {
                const selected_index = parseInt(chapter_select.value, 10);
                if (Number.isNaN(selected_index)) {
                    return;
                }
                const should_autoplay = !audio.paused && !audio.ended;
                loadTrack(selected_index, 0, should_autoplay);
            });
        }

        if (prev_chapter_btn) {
            prev_chapter_btn.addEventListener('click', () => {
                const should_autoplay = !audio.paused && !audio.ended;
                loadTrack(playlist_index - 1, 0, should_autoplay);
            });
        }

        if (next_chapter_btn) {
            next_chapter_btn.addEventListener('click', () => {
                const should_autoplay = !audio.paused && !audio.ended;
                loadTrack(playlist_index + 1, 0, should_autoplay);
            });
        }

        // Add AB loop button functionality if the button exists
        if (ab_loop_btn) {
            ab_loop_btn.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (ab_loop_start === -1) {
                    ab_loop_start = audio.currentTime;
                    ab_loop_btn.textContent = "B";
                    setNewTooltip(ab_loop_btn, 'Loop audio from point A to point B, click to set point B');
                } else if (ab_loop_start > -1 && ab_loop_end === -1) {
                    ab_loop_end = audio.currentTime;
                    ab_loop_btn.style.backgroundColor = 'var(--bs-btn-color)';
                    ab_loop_btn.style.color = 'black';
                    ab_loop_btn.textContent = "A-B";
                    setNewTooltip(ab_loop_btn, 'Stop the A-B loop');
                    ab_loop_btn.blur();
                } else {
                    ab_loop_start = ab_loop_end = -1;
                    ab_loop_btn.textContent = "A";
                    ab_loop_btn.style.backgroundColor = 'var(--bs-btn-bg)';
                    ab_loop_btn.style.color = 'var(--bs-btn-color)';
                    setNewTooltip(ab_loop_btn, 'Loop audio from point A to point B, click to set point A');
                    ab_loop_btn.blur();
                }
            });
        }

        initPlaylist();
    }

    return {
        play,
        stop,
        pause,
        resume,
        togglePlayPause,
        playFromBeginning,
        isPlaylist,
        setPlaylistPositionFromString,
        getPlaylistPositionString,
        setSpeed,
        getSpeed,
        seekRelative
    };
})();
