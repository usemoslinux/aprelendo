const audio = document.getElementById('audioplayer');
const audio_source = document.getElementById('audio-source');
const playPauseButton = document.getElementById('ap-play-btn');
const icon = document.getElementById('ap-play-btn-icon');
const progressBar = document.getElementById('ap-progress-bar');
const timeStamp = document.getElementById('ap-time-stamp');
const btnAbloop = document.getElementById("ap-abloop-btn");

let abloop_start = 0;
let abloop_end = 0;

if (audio) {
    function playAudio() {
        icon.className = 'bi bi-pause-fill';
        audio.play();
    }

    function stopAudio() {
        icon.className = 'bi bi-play-fill';
        audio.pause();
        audio.currentTime = 0;
    }
    
    function pauseAudio() {
        icon.className = 'bi bi-play-fill';
        audio.pause();
    }

    function togglePlayPause() {
        if (audio.paused || audio.ended) {
            playAudio();
        } else {
            pauseAudio();
        }
    }

    function playAudioFromBeginning() {
        audio.pause();
        audio.currentTime = 0;
        togglePlayPause();
    }

    // Set progress bar and time labels to reflect playback progress
    function playbackProgressUpdate() {
        // set progress bar and time labels
        let progress = (audio.currentTime / audio.duration) * 100;
        progressBar.style.width = `${progress}%`;

        // Calculate hours, minutes, and seconds
        let currentHours = Math.floor(audio.currentTime / 3600);
        let currentMinutes = Math.floor((audio.currentTime % 3600) / 60);
        let currentSeconds = Math.floor(audio.currentTime % 60);

        let durationHours = Math.floor(audio.duration / 3600);
        let durationMinutes = Math.floor((audio.duration % 3600) / 60);
        let durationSeconds = Math.floor(audio.duration % 60);

        // Format time for display
        let currentTimeDisplay = currentHours > 0 ? `${currentHours}:` : '';
        currentTimeDisplay += `${currentMinutes < 10 ? '0' : ''}${currentMinutes}:${currentSeconds < 10 ? '0' : ''}${currentSeconds}`;

        let durationTimeDisplay = durationHours > 0 ? `${durationHours}:` : '';
        durationTimeDisplay += `${durationMinutes < 10 ? '0' : ''}${durationMinutes}:${durationSeconds < 10 ? '0' : ''}${durationSeconds}`;

        timeStamp.textContent = `${currentTimeDisplay} / ${durationTimeDisplay}`;
    }

    // Change Playback Speed
    function changeSpeed(event, speed) {
        event.preventDefault(); // Prevent the default anchor action

        audio.playbackRate = speed;

        // Get all speed options
        let speedOptions = document.querySelectorAll('#ap-speed-menu + .dropdown-menu .dropdown-item');

        // Remove 'active' class from all options and add to the selected one
        speedOptions.forEach(option => {
            if (parseFloat(option.textContent) === speed) {
                option.classList.add('active');
            } else {
                option.classList.remove('active');
            }
        });
    }

    playPauseButton.addEventListener('click', togglePlayPause);

    audio.addEventListener('loadedmetadata', playbackProgressUpdate);

    // Update Progress Bar and Time Stamp
    audio.addEventListener('timeupdate', function() {
        // set abloop
        if (abloop_end > 0) {
            if(audio.currentTime >= abloop_end) {
                audio.currentTime = abloop_start;
            }    
        }

        playbackProgressUpdate();
    });

    audio.addEventListener('ended', function() {
        audio.currentTime = 0;
        icon.className = 'bi bi-play-fill';
        playing_audio = false;
    });

    // on audio error
    audio_source.addEventListener('error', function(e) {
        if (audio_source.src !== '' && audio_source.src !== window.location.href) {
            playPauseButton.removeEventListener('click', togglePlayPause);
            playPauseButton.classList = 'btn btn-danger';   
        }
    });
}

if (btnAbloop) {
    // Add click event listener to the AB loop button
    btnAbloop.addEventListener("click", function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (abloop_start === 0 && abloop_end === 0) {
            abloop_start = audio.currentTime;
            this.textContent = "B";
        } else if (abloop_start > 0 && abloop_end === 0) {
            abloop_end = audio.currentTime;
            this.textContent = "C";
        } else {
            abloop_start = abloop_end = 0;
            this.textContent = "A";
        }
    });
}