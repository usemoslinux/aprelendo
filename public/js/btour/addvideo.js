// instance tour steps
var tour_steps = [
    {
        element: "#url",
        placement: 'top',
        title: "Adding videos - Intro",
        content: "Adding videos to Aprelendo is easy: just copy the video's link, paste it here and hit the Fetch button. <br><br>Only YouTube videos are supported for the moment."
    }, 
    {
        title: "Adding videos - Requisites",
        content: "Before adding new videos, remember: <ol><li>only videos with subtitles are supported.</li><li>they must be available in the language you are currently learning.</li></ol>The reason for this is that auto-generated subtitles are still not good enough.<br><br>If you don't know how to search for videos with subtitles in YouTube, there is a neat explanation in <a href='https://support.google.com/youtube/answer/3029103?hl=en'>Google's support center</a>. "
    }, 
    {
        title: "Adding videos - Shared by default",
        content: "Finally, bear in mind that all uploaded videos will be tagged as 'shared' by default, ergo they will be available in the '<u>shared texts</u>' section, not in your private library."
    }
];  
    
// Instance the tour
var tour = new Tour({
    name: "addvideo",
    container: "body",
    smartPlacement: true,
    orphan: true,
    // storage: window.localStorage,
    template:  "<div class='popover tour'> \
                <div class='arrow'></div> \
                <h3 class='popover-title'></h3> \
                <div class='popover-content'></div> \
                <div class='popover-navigation'> \
                <button class='btn btn-sm btn-default' data-role='prev'>« Prev</button> \
                <span data-role='separator'>|</span> \
                <button class='btn btn-sm btn-default' data-role='next'>Next »</button> \
                <button class='btn btn-sm btn-primary' data-role='end'>End tour</button> \
                </div> \
                </div>",
    steps: tour_steps
});

$.each(tour_steps, function(i, step){
    step['title'] += '<span class="float-right">'+(i+1)+'/'+tour_steps.length+'</span>';
});

// Initialize the tour
tour.init();

// Start the tour
tour.start();