// Instance the tour
var tour = new Tour({
    name: "texts",
    container: "body",
    smartPlacement: false,
    orphan: true,
    // storage: false,
    template: "<div class='popover tour'> \
                    <div class='arrow'></div> \
                    <h3 class='popover-title'></h3> \
                    <div class='popover-content'></div> \
                    <div class='popover-navigation'> \
                        <div class='btn-group'> \
                            <button class='btn btn-sm btn-default disabled' data-role='prev' disabled='' tabindex='-1'>« Prev</button> \
                            <button class='btn btn-sm btn-default' data-role='next'>Next »</button>  \
                        </div> \
                        <button class='btn btn-sm btn-default' data-role='end'>Close</button> </div> \
                </div>",
    steps: [
        {
            element: "#body",
            title: "Welcome!",
            placement: 'auto',
            content: "It seems this is your first time using Aprelendo. Let's go over the basics."
        },
        {
            element: "#user-dropdown",
            title: "User menu",
            placement: 'left',
            content: "There are four different 'sections' in Aprelendo: <ol><li>your private texts,</li><li>texts shared by the community,</li><li>the list of words you are learning,</li><li>your learning stats.</li></ol>Here you can access all of them. <br/><br/>Also, you will find the settings page and your user profile. In case of doubt, remember to look for this <i class='far fa-question-circle'></i> symbol."
        },
        {
            element: "#language-dropdown",
            title: "Language menu",
            placement: 'left',
            content: "Here you can change your language preferences and select what language you are currently learning. Want to use another dictionary? Just go here."
        },
        {
            element: "#add-wrapper-div",
            title: "Adding content",
            placement: 'left',
            content: "This button allows you to add 'simple' texts (articles, conversations, letters, songs and others) to your library. <br/><br/>By clicking in the dropdown arrow, you can add other types of content, such as youtube videos, ebooks and RSS texts."
        },
        {
            element: "#extensions-link",
            title: "Extensions & Bookmarklets",
            placement: 'top',
            content: "Another, more practical, way to add texts and Youtube videos to Aprelendo is by using our tailor made extensions (Firefox & Chrome) or bookmarklet (all browsers). <br/><br/>We strongly suggest to take a look at this page after the tour ends."
        },
        {
            element: "#search-wrapper-div",
            title: "Search",
            placement: 'bottom',
            content: "Use this box to search for texts in your private library. <br/><br/>Remember: if you want to search texts shared by the community you need to go to the appropriate section, via the <i class='fas fa-user-circle'></i> user menu."
        },
        {
            element: "#filter-wrapper-div",
            title: "Filter",
            placement: 'right',
            content: "You can also filter search results by selecting one of six different types of texts (articles, conversations, letters, songs, ebooks and others). <br/><br/>Here you will also find an option to search for archived texts, which are those you have already finished reading."
        },
        {
            element: "#actions-menu",
            title: "Actions menu",
            placement: 'right',
            content: "The 'actions' menu has two entries: delete and archive. <br/><br/>The first will delete the selected texts from your library. <br/><br/>The second will tag them as 'archived', meaning they will stay hidden unles you specifically set your search results to show 'archived' texts."
        },
        {
            element: "#sort-menu",
            placement: 'left',
            title: "Sort menu",
            content: "The 'sort' menu allows you to sort your search results by date of upload."
        },
        {
            element: "#body",
            title: "End",
            placement: 'auto',
            content: "Good. You are ready to start using Aprelendo. Don't forget to follow us on our social networks and give us a like ;)"
        }
    ]});
    
    // Initialize the tour
    tour.init();
    
    // Start the tour
    tour.start();