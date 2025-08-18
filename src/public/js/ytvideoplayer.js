// Load the IFrame Player API code asynchronously
const tag = document.createElement("script");
const video_element = document.getElementById("videoplayer");
const ytId = video_element.dataset.ytid;

tag.src = "https://www.youtube.com/iframe_api";
const first_script_tag = document.getElementsByTagName("script")[0];
first_script_tag.parentNode.insertBefore(tag, first_script_tag);

let VideoController = {}; // Define the custom player object

// Function called when the YouTube API is ready
window.onYouTubeIframeAPIReady = function () {
    VideoController.instance = new YT.Player("videoplayer", {
        playerVars: {
            autoplay: 0, // don't autoplay
            loop: 0, // don't play video in a loop
            controls: 2, // minimum controls
            fs: 0, // disable fullscreen
            showinfo: 0, // don't show uploader info (deprecated?)
            autohide: 1, // controls will auto-hide after user inactivity
            modestbranding: 1, // restricts the YouTube logo display
            playsinline: 1, // play inline, not fullscreen on mobile
        },
        videoId: ytId,
        events: {
            onStateChange: onPlayerStateChange,
            onReady: onPlayerReady
        }
    });

    VideoController.resume_video = false;
    VideoController.timer_id = null;
    VideoController.start_time = null;

    // Add custom methods to the VideoController object
    VideoController.play = function() {
        VideoController.instance.playVideo();
        VideoController.resume_video = false;
    };
    
    VideoController.pause = function(resume) {
        if (!VideoController.isPaused()) {
            VideoController.instance.pauseVideo();
            VideoController.resume_video = resume;
        }
        console.log('pause');
    };

    VideoController.isPaused = function() {
        return VideoController.instance.getPlayerState() !== YT.PlayerState.PLAYING;
    };

    VideoController.resume = function() {
        if (VideoController.resume_video) {
            VideoController.play();
            VideoController.resume_video = false;
        }
    };
    
    VideoController.getCurrentTime = function() {
        return VideoController.instance.getCurrentTime();
    };

    VideoController.seekTo = function(seconds) {
        VideoController.start_time = seconds;
    };
};

function onPlayerStateChange(event) {
    const objs = Array.from(document.querySelectorAll("#text span"));
    let current_video_time = 0;

    const updateTime = (interval) => {
        return setInterval(() => {
            current_video_time = VideoController.getCurrentTime();
            const next_obj = objs
                .filter(div => parseFloat(div.dataset.start) < current_video_time)
                .slice(-1)[0];

            if (next_obj && !next_obj.classList.contains("video-reading-line")) {
                objs.forEach(div => div.classList.remove("video-reading-line"));
                next_obj.classList.add("video-reading-line");
                next_obj.scrollIntoView({
                    behavior: 'auto',
                    block: 'center',
                    inline: 'center'
                });
            }
        }, interval);
    };

    if (event.data === YT.PlayerState.PLAYING) {
        VideoController.timer_id = updateTime(500);
    } else if (VideoController.timer_id !== null) {
        clearInterval(VideoController.timer_id);
        VideoController.timer_id = null;
    }
}

function onPlayerReady(event) {
    VideoController.instance.seekTo(VideoController.start_time, true);
}