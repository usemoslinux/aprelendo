// Instance the tour
var tour = new Tour({
    name: "addrss",
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
                            <button class='btn btn-sm btn-default disabled' data-role='prev' disabled='' tabindex='-1'>« Prev</button> \
                            <button class='btn btn-sm btn-default' data-role='next'>Next »</button>  \
                        </div> \
                        <button class='btn btn-sm btn-default' data-role='end'>Close</button> </div> \
                </div>",
    steps: [
        {
            element: "",
            title: "Adding RSS/Atom feeds",
            placement: 'auto',
            content: "RSS and Atom are web feeds that allow you to access your favorite content in a standardized, computer-readable format.<br/><br/>Aprelendo supports them both, but only up to 3 feeds by language are allowed.<br/><br/>You can add new feeds in your Languages section. Once that's done, here you will be able to access all your feed entries and add them to your library.",
            onNext: function (tour) {
                $('#item-1').collapse('toggle');
                $('#item-1-1').collapse('toggle');
            }
        },
        {
            element: "#item-1-1 .btn-addsound",
            title: "Edit",
            placement: 'auto',
            content: "Adds this RSS entry to your library and redirects you to the 'edit' page so that you can make further changes to the text.<br/><br/>This option can be useful if the RSS shows a shortened version of the post, which is fairly common these days. Once you are in the 'edit' page, click the 'Fetch' button and Aprelendo will try to extract the complete post text, not just the summary shown here.",
        },
        {
            element: "#item-1-1 .btn-readnow",
            title: "Add & Read Now",
            placement: 'auto',
            content: "Adds this RSS entry to your library and opens it so that you can start reading it.",
        },
        {
            element: "#item-1-1 .btn-readlater",
            title: "Add & Read Later",
            placement: 'auto',
            content: "Adds this RSS entry to your library and lets you continue to explore your feeds so that you can add further entries to your library",
        }
    ]
    });
    
    // Initialize the tour
    tour.init();
    
    // Start the tour
    tour.start();