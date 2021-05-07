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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

// Youtube JS code to load iframe player (w/access to YT API)

// 2. This code loads the IFrame Player API code asynchronously.
var tag = document.createElement("script");
var yt_id = document.getElementById("player").dataset.ytid;

tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName("script")[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

// 3. This function creates an <iframe> (and YouTube player)
//    after the API code downloads.
var player;
function onYouTubeIframeAPIReady() {
    player = new YT.Player("player", {
        height: "390",
        width: "640",
        autoplay: 0,
        videoId: yt_id,
        events: {
            onReady: onPlayerReady,
            onStateChange: onPlayerStateChange
        }
    });
} // end onYouTubeIframeAPIReady

// 4. The API will call this function when the video player is ready.
function onPlayerReady(event) {
    // event.target.playVideo();
} // end onPlayerReady

// 5. The API calls this function when the player's state changes.
//    The function indicates that when playing a video (state=1),
//    the player should play for six seconds and then stop.
function onPlayerStateChange(event) {
    if (event.data === YT.PlayerState.PLAYING) {
        var $obj = $("div.text-center", "#text-container");
        var video_time = 0;
        var timer;
        video_paused = false;

        function updateTime(time_interval) {
            timer = setInterval(function() {
                if (!video_paused) {
                    video_time = player.getCurrentTime();
                    var $next_obj = $obj
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
