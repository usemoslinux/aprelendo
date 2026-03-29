// SPDX-License-Identifier: GPL-3.0-or-later

const VideoController = (() => {
    const video = document.getElementById('videoplayer');
    let resume_video = false;

    // Default functions (do nothing if audio is not defined)
    let play = () => { };
    let stop = () => { };
    let pause = () => { };
    let resume = () => { };
    let playFromBeginning = () => { };

    // If the audio element exists, redefine the functions
    if (video) {
        play = () => video.play();

        stop = () => {
            video.pause();
            video.currentTime = 0;
        };

        pause = (resume) => {
            resume_video = video.paused  && !resume_video ? false : resume;
            video.pause();
        };

        resume = () => {
            if (resume_video) {
                play();
                resume_video = false;
            }
        };

        playFromBeginning = () => {
            video.pause();
            video.currentTime = 0;
            play();
        };

        const playbackProgressUpdate = () => {
            const video_time = document.getElementById('videoplayer').currentTime * 1000;
            const objs = document.querySelectorAll("#text span");
            let next_obj = null;

            // Loop through elements to find the last one that meets the condition
            objs.forEach((element) => {
                if (parseFloat(element.getAttribute("data-start")) < video_time) {
                    next_obj = element;
                }
            });

            if (next_obj && !next_obj.classList.contains("video-reading-line")) {
                // Remove the class from all elements
                objs.forEach(element => element.classList.remove("video-reading-line"));

                // Add the class to the found element
                next_obj.classList.add("video-reading-line");

                // Scroll to the element
                next_obj.scrollIntoView({
                    behavior: 'auto',
                    block: 'center',
                    inline: 'center'
                });
            }
        };

        // event listeners
        video.addEventListener('loadedmetadata', playbackProgressUpdate);
        video.addEventListener('timeupdate', () => {
            playbackProgressUpdate();
        });
    }

    return {
        play,
        stop,
        pause,
        resume,
        playFromBeginning
    };
})();