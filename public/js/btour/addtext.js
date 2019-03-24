// instance tour steps
var tour_steps = [
    {
        title: "Adding texts",
        content: "This is where you can add 'simple' texts, such as articles, conversations, letters, lyrics and others.<br/><br/>As you will be doing this often, it is important that you follow this brief introduction."
    },
    {
        title: "Adding texts - 1st method",
        content: "There are three ways to add texts to Aprelendo.<br/><br/>The first -and most obvious- is to manually complete the fields in this form and hit the Save button.<br/><br/>Please note that even though some fields are optional it is highly recommended that you fill all of them, as this will increase the text's quality and facilitate searches."
    },
    {
        title: "Adding texts - 2nd method",
        content: "The second one consists of copying the text's source URL and hitting the Fetch button. This will extract the text in that page -as well as its title and author information- without having to copy and paste it manually.<br/><br/>Please bear in mind that, even though this method is quite effective, it won't always work. Also, the extracted text might need some refinement before you upload it."
    },
    {
        title: "Adding texts - 3rd method",
        content: "The third method consists of using our extensions (Chrome/Firefox) or bookmarklet (any browser).<br/><br/>Once you install any of them, all you have to do is navigate to the page that contains the text you are interested in and click on the Aprelendo button.<br/><br/>The magic behind this and the previous method is the same, so you should keep in mind the same recommendations."
    },
    {
        element: "#shared-text-label",
        title: "Sharing texts",
        placement: 'top',
        content: "Note that if you select this option you will 'share' this text with our community, i.e. it will published in the 'shared texts' section and everyone will be able to access it."
    }
];

// Instance the tour
var tour = new Tour({
    name: "addtext",
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