// Load the IFrame Player API code asynchronously
const tag = document.createElement("script");
const video_element = document.getElementById("player");
const ytId = video_element.dataset.ytid;

tag.src = "https://www.youtube.com/iframe_api";
const first_script_tag = document.getElementsByTagName("script")[0];
first_script_tag.parentNode.insertBefore(tag, first_script_tag);

let video_controller = {}; // Define the custom player object

// Function called when the YouTube API is ready
window.onYouTubeIframeAPIReady = function () {
    video_controller.instance = new YT.Player("player", {
        height: "390",
        width: "640",
        playerVars: {
            loop: 0,
            controls: 1,
            fs: 0,
            showinfo: 0,
            autohide: 1,
            modestbranding: 1
        },
        videoId: ytId,
        events: {
            onStateChange: onPlayerStateChange
        }
    });

    video_controller.resume_video = false;
    video_controller.timer_id = null;

    // Add custom methods to the video_controller object
    video_controller.play = function() {
        video_controller.instance.playVideo();
        video_controller.resume_video = false;
    };
    
    video_controller.pause = function(resume) {
        video_controller.resume_video = video_controller.isPaused() ? false : resume;
        video_controller.instance.pauseVideo();
    };

    video_controller.isPaused = function() {
        return video_controller.instance.getPlayerState() !== YT.PlayerState.PLAYING;
    };

    video_controller.resume = function() {
        if (video_controller.resume_video) {
            video_controller.play();
        }
    };
};

function onPlayerStateChange(event) {
    const text_container = document.getElementById("text-container");
    const text_divs = Array.from(text_container.getElementsByTagName("div"));
    let current_video_time = 0;

    const updateTime = (interval) => {
        return setInterval(() => {
            current_video_time = video_controller.instance.getCurrentTime();
            const next_obj = text_divs
                .filter(div => parseFloat(div.dataset.start) < current_video_time)
                .slice(-1)[0];

            if (next_obj && !next_obj.classList.contains("video-reading-line")) {
                text_divs.forEach(div => div.classList.remove("video-reading-line"));
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
        video_controller.timer_id = updateTime(500);
    } else if (video_controller.timer_id !== null) {
        clearInterval(video_controller.timer_id);
        video_controller.timer_id = null;
    }
}