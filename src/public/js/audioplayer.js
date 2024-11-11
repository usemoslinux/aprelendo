const audioController = (() => {
    const audio = document.getElementById('audioplayer');
    const audio_source = document.getElementById('audio-source');
    const play_pause_btn = document.getElementById('ap-play-btn');
    const play_pause_btn_icon = document.getElementById('ap-play-btn-icon');
    const progress_bar = document.getElementById('ap-progress-bar');
    const time_stamp = document.getElementById('ap-time-stamp');
    const speed_menu_items = document.querySelectorAll('#ap-speed-menu .dropdown-item');
    const ab_loop_btn = document.getElementById("ap-abloop-btn");

    let playing_audio = false;
    let ab_loop_start = 0;
    let ab_loop_end = 0;

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
            playing_audio = false;
        };

        pause = (resume) => {
            if (!audio.paused) {
                audio.pause();
                if (resume) {
                    setTimeout(() => {
                        playing_audio = true;
                    }, 50);
                }
            }
        };

        togglePlayPause = () => {
            if (audio.paused || audio.ended) {
                audio.play();
                playing_audio = true;
            } else {
                audio.pause();
                playing_audio = false;
            }
        };

        resume = () => {
            if (playing_audio) {
                play();
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

            time_stamp.textContent = `${currentTimeDisplay} / ${durationTimeDisplay}`;
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
            if (ab_loop_end > 0) {
                if(audio.currentTime >= ab_loop_end) {
                    audio.currentTime = ab_loop_start;
                }
            }
            playbackProgressUpdate();
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
            playing_audio = true;
        });

        audio.addEventListener('pause', () => {
            play_pause_btn_icon.className = 'bi bi-play-fill';
            playing_audio = false;
        });

        audio.addEventListener('ended', () => {
            audio.currentTime = 0;
            play_pause_btn_icon.className = 'bi bi-play-fill';
            playing_audio = false;
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
                if (ab_loop_start === 0 && ab_loop_end === 0) {
                    ab_loop_start = audio.currentTime;
                    ab_loop_btn.textContent = "B";
                } else if (ab_loop_start > 0 && ab_loop_end === 0) {
                    ab_loop_end = audio.currentTime;
                    ab_loop_btn.textContent = "C";
                } else {
                    ab_loop_start = ab_loop_end = 0;
                    ab_loop_btn.textContent = "A";
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