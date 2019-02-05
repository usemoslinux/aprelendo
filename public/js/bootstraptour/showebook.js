// Instance the tour
var tour = new Tour({
    name: "showebook",
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
            element: ".loader",
            title: "Processing words",
            placement: 'auto',
            content: "This loader indicates that Aprelendo is still processing the current chapter to underline words accordingly.<br/><br/>Please, be patient. This process can take a while depending on the number of words of the current chapter and those you've already saved in your library.",
            onNext: function (tour) {
                $('#item-1').collapse('toggle');
                $('#item-1-1').collapse('toggle');
            }
        },
        {
            element: "#hamburger",
            title: "Table of Contents",
            placement: 'auto',
            content: "Click here to access your ebook's table of contents (TOC).",
        },
        {
            element: "#btn-save",
            title: "Save",
            placement: 'auto',
            content: "Always remember to click this button once you finish reading. This ensures that you reading position is saved and that the status of the words you learned is updated correctly.",
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