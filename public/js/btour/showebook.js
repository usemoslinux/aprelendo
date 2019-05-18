// instance tour steps
var tour_steps = [
    {
        element: ".lds-ripple",
        title: "Processing words",
        placement: 'bottom',
        content: "This loader indicates that Aprelendo is still processing the current chapter to underline words accordingly.<br><br>Please, be patient. It can take a while depending on the number of words of the current chapter and those you've already saved in your library.",
    },
    {
        element: "#hamburger",
        title: "Table of Contents",
        placement: 'bottom',
        content: "Click here to access your ebook's table of contents (TOC).",
    },
    {
        element: "#btn-save",
        title: "Save & Close",
        placement: 'bottom',
        content: "Always remember to click this button once you finish your reading session.<br><br>This ensures that you reading position is saved and that the status of the words you learned is updated correctly.",
    }
];

// Instance the tour
var tour = new Tour({
    name: "showebook",
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