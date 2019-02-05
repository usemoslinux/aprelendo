// Instance the tour
var tour = new Tour({
    name: "addvideo",
    container: "body",
    smartPlacement: false,
    orphan: true,
    storage: false,
    template: "<div class='popover tour'> \
                    <div class='arrow'></div> \
                    <h3 class='popover-title'></h3> \
                    <div class='popover-content'></div> \
                    <div class='popover-navigation'> \
                        <div class='btn-group'> \
                            <button class='btn btn-sm btn-secondary disabled' data-role='prev' disabled='' tabindex='-1'>« Prev</button> \
                            <button class='btn btn-sm btn-secondary' data-role='next'>Next »</button>  \
                        </div> \
                        <button class='btn btn-sm btn-secondary' data-role='end'>Close</button> </div> \
                </div>",
    steps: [
        {
            element: "#url",
            title: "Adding videos",
            placement: 'auto',
            content: "Adding videos to Aprelendo is easy: just copy the video's link, paste it here and finally click the Fetch button. <br/><br/>Only Youtube videos are supported for the moment.<br/><br/>All uploaded videos will be tagged as 'shared' by default, ergo they will be available in the 'shared texts' section."
        }, 
        {
            element: "#body",
            title: "Adding videos",
            placement: 'auto',
            content: "Before adding new videos, remember two things: <ol><li>only videos with subtitles are supported.</li><li>these subtitles must be in the language you are currently trying to learn.</li></ol>There is a good reason for this: we need subtitles to ensure a complete learning experience and auto-generated subtitles by Google are still not good enough.<br/><br/>If you don't know how to search for videos with subtitles in Youtube, there is a neat explanation in <a href='https://support.google.com/youtube/answer/3029103?hl=en'>Google's support center</a>. "
        }
    ]});
    
    // Initialize the tour
    tour.init();
    
    // Start the tour
    tour.start();