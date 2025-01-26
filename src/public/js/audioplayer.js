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
    const total_time_stamp = document.getElementById('ap-totaltime-stamp');
    const speed_menu_items = document.querySelectorAll('#ap-speed-menu .dropdown-item');
    const ab_loop_btn = document.getElementById("ap-abloop-btn");

    let resume_audio = false;
    let ab_loop_start = -1;
    let ab_loop_end = -1;

    // Default functions (do nothing if audio is not defined)
    let play = () => {};
    let stop = () => {};
    let pause = () => {};
    let resume = () => {};
    let togglePlayPause = () => {};
    let playFromBeginning = () => {};
    
    // If the audio element exists, redefine the functions
    if (audio) {
        play = () => audio.play();

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

        const playbackProgressUpdate = () => {
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
        const changeSpeed = (speed) => {
            audio.playbackRate = speed;

            // Update active class
            document.querySelectorAll('#ap-speed-menu .dropdown-item').forEach(option => {
                if (parseFloat(option.getAttribute('data-speed')) === speed) {
                    option.classList.add('active');
                } else {
                    option.classList.remove('active');
                }
            });
        }

        // event listeners
        
        play_pause_btn.addEventListener('click', togglePlayPause);
        audio.addEventListener('loadedmetadata', playbackProgressUpdate);

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
            const clickPosition = e.clientX - rect.left;
            const clickPercentage = clickPosition / rect.width;
            audio.currentTime = clickPercentage * audio.duration;
        });

        progress_bar_container.addEventListener('mousemove', (e) => {
            const rect = progress_bar_container.getBoundingClientRect();
            const hover_position = e.clientX - rect.left;
            const hover_percentage = hover_position / rect.width;
            const hover_time = Math.floor(hover_percentage * audio.duration);
        
            const hours = Math.floor(hover_time / 3600);
            const minutes = Math.floor((hover_time % 3600) / 60);
            const seconds = hover_time % 60;
        
            progress_bar_container.title = 
                hours > 0 
                    ? `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}` 
                    : `${minutes}:${seconds.toString().padStart(2, '0')}`;
        });        

        speed_menu_items.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();

                const target = e.target;
        
                if (target.hasAttribute('data-speed')) {
                    const speed = parseFloat(target.getAttribute('data-speed'));
                    changeSpeed(speed);
                }
            });
        });

        audio.addEventListener('play', () => {
            play_pause_btn_icon.className = 'bi bi-pause-fill';
        });

        audio.addEventListener('pause', () => {
            play_pause_btn_icon.className = 'bi bi-play-fill';
        });

        audio.addEventListener('ended', () => {
            audio.currentTime = 0;
            play_pause_btn_icon.className = 'bi bi-play-fill';
        });

        audio_source.addEventListener('error', () => {
            if (audio_source.src !== '' && audio_source.src !== window.location.href) {
                play_pause_btn.removeEventListener('click', togglePlayPause);
                play_pause_btn.classList = 'btn btn-danger';
            }
        });

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
    }

    return {
        play,
        stop,
        pause,
        resume,
        togglePlayPause,
        playFromBeginning
    };
})();