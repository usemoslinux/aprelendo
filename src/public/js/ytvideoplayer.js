// Load the IFrame Player API code asynchronously
const tag = document.createElement("script");
const video_element = document.getElementById("videoplayer");
const ytId = video_element.dataset.ytid;

tag.src = "https://www.youtube.com/iframe_api";
const first_script_tag = document.getElementsByTagName("script")[0];
first_script_tag.parentNode.insertBefore(tag, first_script_tag);

let VideoController = {}; // Define the custom player object

const youtubeApiReady = new Promise(resolve => {
    window.onYouTubeIframeAPIReady = function () {
        resolve();
    };
});

function initializeVideoPlayer(audio_pos) {
    Promise.all([youtubeApiReady])
        .then(([]) => {
            VideoController.instance = new YT.Player("videoplayer", {
                playerVars: {
                    autoplay: 0, // don't autoplay
                    start: Math.round(audio_pos), // Start time
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
                    onStateChange: onPlayerStateChange
                }
            });

            VideoController.resume_video = false;
            VideoController.timer_id = null;
            VideoController.start_time = null;

            // Add custom methods to the VideoController object
            VideoController.play = function () {
                VideoController.instance.playVideo();
                VideoController.resume_video = false;
            };

            VideoController.pause = function (resume) {
                if (!VideoController.isPaused()) {
                    VideoController.instance.pauseVideo();
                    VideoController.resume_video = resume;
                }
                console.log('pause');
            };

            VideoController.isPaused = function () {
                return VideoController.instance.getPlayerState() !== YT.PlayerState.PLAYING;
            };

            VideoController.resume = function () {
                if (VideoController.resume_video) {
                    VideoController.play();
                    VideoController.resume_video = false;
                }
            };

            VideoController.getCurrentTime = function () {
                return VideoController.instance.getCurrentTime();
            };
        })
        .catch(error => {
            // Handle any errors from the AJAX call or Promise chain
            console.error("Initialization failed:", error);
        });
}

// helper functions for scrolling and finding spans
function centerInContainer(el, container) {
    if (!el || !container) return;

    const elRect = el.getBoundingClientRect();
    const contRect = container.getBoundingClientRect();
    const current = container.scrollTop;
    const delta = (elRect.top + elRect.height / 2) - (contRect.top + contRect.height / 2);
    container.scrollTop = current + delta;
}

function getOffsetTopWithin(el, ancestor) {
    let top = 0, node = el;
    while (node && node !== ancestor) {
        top += node.offsetTop;
        node = node.offsetParent;
    }
    return top;
}

function getSpans() {
    return Array.from(document.querySelectorAll("#text-container span"));
}

function findCurrentIndex(spans, t) {
    let lo = 0, hi = spans.length - 1, ans = -1;
    while (lo <= hi) {
        const mid = (lo + hi) >> 1;
        const s = parseFloat(spans[mid].dataset.start || "0");
        if (s <= t) { ans = mid; lo = mid + 1; } else { hi = mid - 1; }
    }
    return ans;
}

// --- ticker driven by rAF while playing ---
let rafId = null;
let lastIdx = -1;

function startTicker(VideoController) {
    if (rafId) cancelAnimationFrame(rafId);
    const spans = getSpans();
    if (!spans.length) return;
    const container = document.getElementById("text-container");

    function tick() {
        if (!VideoController || VideoController.isPaused()) {
            rafId = null;
            return;
        }
        const t = VideoController.getCurrentTime();
        const idx = findCurrentIndex(spans, t);
        if (idx !== -1 && idx !== lastIdx) {
            if (lastIdx >= 0 && spans[lastIdx]) spans[lastIdx].classList.remove("video-reading-line");
            spans[idx].classList.add("video-reading-line");
            centerInContainer(spans[idx], container);
            lastIdx = idx;
        }
        rafId = requestAnimationFrame(tick);
    }

    rafId = requestAnimationFrame(tick);
}

function stopTicker() {
    if (rafId) cancelAnimationFrame(rafId);
    rafId = null;
}

function onPlayerStateChange(event) {
    const state = event.data;
    if (state === YT.PlayerState.PLAYING) {
        startTicker(VideoController);
    } else if (state === YT.PlayerState.ENDED) {
        stopTicker();
        const spans = getSpans();
        spans.forEach(s => s.classList.remove("video-reading-line"));
        lastIdx = -1;
    } else {
        stopTicker();
    }
}