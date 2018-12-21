// Instance the tour
var tour = new Tour({
    name: "addtext",
    container: "body",
    smartPlacement: false,
    orphan: true,
    // storage: false,
    steps: [
        {
            element: "#body",
            title: "Adding texts",
            placement: 'auto',
            content: "This is where you can add 'simple' texts to your library, such as articles, conversations, letters, songs and others."
        },
        {
            element: "#body",
            title: "Adding texts",
            placement: 'auto',
            content: "There are three ways to add texts to Aprelendo.<br/><br/>The first -and most obvious- is to manually complete the fields in this form and hit the Save button.<br/><br/>Please note that even though some fields are optional, it is highly recommended that you fill them all, as this will increase the quality of the texts you upload and make it easy for you and other people to search for them."
        },
        {
            element: "#body",
            title: "Adding texts",
            placement: 'auto',
            content: "The second one is to copy the text's source URL and click the Fetch button. This will extract the text in that page -as well as its title and author information- without having to copy and paste it manually.<br/><br/>Please bear in mind that, even though this method is quite effective, it won't always work. Also, the extracted text might need some refinement before you upload it."
        },
        {
            element: "#body",
            title: "Adding texts",
            placement: 'auto',
            content: "The third method consists of using our extensions (Chrome/Firefox) or bookmarklet (any browser). Once you install any of them, all you have to do is navigate to the page that contains the text you are interested in and click on the Aprelendo button. The magic behind this and the previous method is the same, so you should keep in mind the same recommendations."
        },
        {
            element: "#shared-text-wrapper-div",
            title: "Sharing texts",
            placement: 'top',
            content: "Note that if you select this option you will 'share' this text with our community, i.e. it will published in the 'shared texts' section and everyone will be able to access it."
        }
    ]});
    
    // Initialize the tour
    tour.init();
    
    // Start the tour
    tour.start();