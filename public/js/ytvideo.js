    // Youtube JS code to load iframe player (w/access to YT API)
    
    // 2. This code loads the IFrame Player API code asynchronously.
    var tag = document.createElement('script');
    var yt_id = document.getElementById("player").dataset.ytid;
    
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    
    // 3. This function creates an <iframe> (and YouTube player)
    //    after the API code downloads.
    var player;
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
            height: '390',
            width: '640',
            autoplay: 0,
            videoId: yt_id,
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
    }
    
    // 4. The API will call this function when the video player is ready.
    function onPlayerReady(event) {
        event.target.playVideo();


        


    }
    
    // 5. The API calls this function when the player's state changes.
    //    The function indicates that when playing a video (state=1),
    //    the player should play for six seconds and then stop.
    function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.PLAYING) {
            console.log(player.getCurrentTime());
        }
    }
    
    function stopVideo() {
        player.stopVideo();
    }
