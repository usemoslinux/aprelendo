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

// Youtube JS code to load iframe player (w/access to YT API)
let video_paused;

// 2. This code loads the IFrame Player API code asynchronously.
let tag = document.createElement("script");
const yt_id = document.getElementById("player").dataset.ytid;

tag.src = "https://www.youtube.com/iframe_api";
let firstScriptTag = document.getElementsByTagName("script")[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

// 3. This function creates an <iframe> (and YouTube player)
//    after the API code downloads.
let player;

function onYouTubeIframeAPIReady() {
    player = new YT.Player("player", {
        height: "390",
        width: "640",
        playerVars: {
            // autoplay: 1,
            loop: 0,
            controls: 1,
            fs: 0,
            showinfo: 0,
            autohide: 1,
            modestbranding: 1},
        videoId: yt_id,
        events: {
            onReady: onPlayerReady,
            onStateChange: onPlayerStateChange
        }
    });
} // end onYouTubeIframeAPIReady

// 4. The API will call this function when the video player is ready.
function onPlayerReady(event) {
    event.target.playVideo();
} // end onPlayerReady

// 5. The API calls this function when the player's state changes.
//    The function indicates that when playing a video (state=1),
//    the player should play for six seconds and then stop.
function onPlayerStateChange(event) {
    if (event.data === YT.PlayerState.PLAYING) {
        let $obj = $("div", "#text-container");
        let video_time = 0;
        let timer;
        video_paused = false;

        function updateTime(time_interval) {
            timer = setInterval(function() {
                if (!video_paused) {
                    video_time = player.getCurrentTime();
                    let $next_obj = $obj
                        .filter(function() {
                            return $(this).attr("data-start") < video_time;
                        })
                        .last();
                    if (
                        $next_obj.length > 0 &&
                        !$next_obj.hasClass("video-reading-line")
                    ) {
                        $obj.removeClass("video-reading-line");
                        $next_obj.addClass("video-reading-line");
                        
                        $next_obj[0].scrollIntoView({
                            behavior: 'auto',
                            block: 'center',
                            inline: 'center'
                        });
                    }
                } else {
                    clearInterval(timer);
                }
            }, time_interval);
        }

        updateTime(500);
    } else {
        video_paused = true;
    }
} // end onPlayerStateChange
