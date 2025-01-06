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
            const objs = document.querySelectorAll("#text-container .text-center");
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