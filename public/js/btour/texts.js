// instance tour steps
var tour_steps = [
    {
        title: "Welcome!",
        content: "It seems this is the first time you use Aprelendo so let's go over the basics."
    },
    {
        element: "#user-dropdown",
        title: "User menu",
        content: "There are four different 'sections' in Aprelendo: <ol><li>your private texts,</li><li>texts shared by the community,</li><li>the list of words you are learning,</li><li>your learning stats.</li></ol>Here you can access all of them. <br/><br/>Also, you will find the settings page and your user profile. In case of doubt, remember to look for this <i class='far fa-question-circle'></i> symbol.",
        placement: 'bottom'
    },
    {
        element: "#language-dropdown",
        title: "Language menu",
        content: "Here you can change your language preferences and select the current language.",
        placement: 'bottom'
    },
    {
        element: "#add-wrapper-div",
        title: "Adding content",
        content: "This button allows you to add 'simple' texts (articles, conversations, letters, song lyrics and others) to your library. <br/><br/>By clicking in the dropdown arrow you can add other types of content, such as YouTube videos, ebooks and RSS texts. The last two are available for premium users only.",
        placement: 'bottom'
    },
    {
        element: "#extensions-link",
        title: "Extensions & Bookmarklets",
        content: "Another, more practical, way to add texts and YouTube videos to Aprelendo is by using our tailor made extensions (Firefox & Chrome). <br/><br/>For more information on this, we strongly suggest to take a look at this page after the tour ends.",
        placement: 'top'
    },
    {
        element: "#search-wrapper-div",
        title: "Search",
        content: "Use this box to search for texts in your <u>private</u> library. <br/><br/>To search texts shared by the community you need to go to the appropriate section, via the <i class='fas fa-user-circle'></i> user menu, in the upper right.",
        placement: 'bottom'
    },
    {
        element: "#filter-wrapper-div",
        title: "Filter",
        content: "Filter search results by selecting one of six different types of texts (articles, conversations, letters, song lyrics, ebooks and others). <br/><br/>Here you will also find an option to search for archived texts, which are those you have already finished reading.",
        placement: 'bottom'
    },
    {
        element: "#actions-menu",
        title: "Actions menu",
        content: "The 'actions' menu has two entries: delete and archive. <br/><br/>The first will delete the selected texts from your library. <br/><br/>The second will tag them as 'archived', meaning they will stay hidden unless you specifically set your search results to show 'archived' texts.",
        placement: 'top'
    },
    {
        element: "#sort-menu",
        title: "Sort menu",
        content: "The 'sort' menu allows you to sort your search results by date of upload.",
        placement: 'top'

    },
    {
        title: "End",
        content: "Good. You are ready to start using Aprelendo. Don't forget to follow us on our social networks and give us a like ;)"
    }
];

// Instance the tour
var tour = new Tour({
    name: "texts",
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