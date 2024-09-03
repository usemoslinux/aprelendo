/**
 * Shows custom message in the top section of the screen
 * @param {string} html
 * @param {string} type
 */
function showMessage(html, type) {
    let alert = {
        'alert-success': { 'title': 'Success', 'image': 'bi-check-circle-fill' },
        'alert-info': { 'title': 'Information', 'image': 'bi-info-circle-fill' },
        'alert-warning': { 'title': 'Careful', 'image': 'bi-exclamation-triangle-fill' },
        'alert-danger': { 'title': 'Oops!', 'image': 'bi-exclamation-circle-fill' }
    }

    let title = '';
    let image = '';

    for (const key in alert) {
        if (key == type) {
            title = alert[key].title;
            image = alert[key].image;
            break;
        }
    }

    let div_flag_html = '<i class="bi ' + image + '"></i>' + title;
    let $div_flag = $("<div>").addClass("alert-flag fs-5").html(div_flag_html);
    let $div_msg = $("<div>").addClass("alert-msg").html(html);

    $("#alert-box")
        .empty()
        .removeAttr('style')
        .removeClass()
        .addClass("alert " + type)
        .append($div_flag, $div_msg);
    $(window).scrollTop(0);
} // end showMessage

function hideMessage(timeout) {
    $("#alert-box").hide(timeout);
}

/**
 * Smoothly scrolls the webpage to the top.
 * @returns {void}
 */
function scrollToPageTop() {
    $("html, body").animate({
        scrollTop: 0
    }, "fast");
} // end scrollToPageTop()

/**
 * Retrieves the current URI parameters from the URL.
 * Parses the query string of the current page's URL and returns an object
 * containing key-value pairs of the parameters.
 * @returns {Object} An object with the current URI parameters as key-value pairs.
 */
function getCurrentURIParameters() {
    const params = new URLSearchParams(window.location.search);
    const result = {};

    for (const [key, value] of params.entries()) {
        result[key] = value;
    }

    return result;
} // end getCurrentURIParameters

/**
 * Converts an object of key-value pairs into a URI query string.
 * Takes an object and converts it into a URL-encoded query string format,
 * ensuring only parameters with values are included.
 * If the object is empty, it returns an empty string.
 * @param {Object} paramsObject - An object containing key-value pairs to be parameterized.
 * @returns {string} A URI query string with parameters.
 */
function buildQueryString(paramsObject) {
    if (Object.keys(paramsObject).length === 0) {
        return '';
    }
    
    const params = new URLSearchParams();

    for (const [key, value] of Object.entries(paramsObject)) {
        if (value) {
            params.append(key, value);
        }
    }

    return params.toString() ? '?' + params.toString() : '';
} // end buildQueryString()

/**
 * Retrieves the file name of the current page.
 * Extracts the file name from the current page's URL, ignoring the path.
 * Returns only the file name, without any directories or parameters.
 * @returns {string} The file name of the current page.
 */
function getCurrentFileName() {
    const pathname = new URL(window.location.href).pathname;
    return pathname.substring(pathname.lastIndexOf('/') + 1);
} // end getCurrentFileName

/**
 * Renables scrolling without making text jump around
 */
$.fn.enableScroll = function() {
    // Remove the scroll event handler for the specific element
    this.off('scroll.scrolldisabler');
    return this; // Enable chainability
};

/**
 * Disables scrolling without making text jump around
 */
$.fn.disableScroll = function() {
    // Store the current scroll position of the element
    this.data('oldScrollPos', this.scrollTop());

    // Attach a scroll event handler to the element
    this.on('scroll.scrolldisabler', (event) => {
        // Reset the scroll position to the stored value
        this.scrollTop(this.data('oldScrollPos'));
        event.preventDefault();
    });

    return this; // Enable chainability
};

/**
 * Determines if an element is after another one
 * @param {Jquery object} sel
 */
$.fn.isAfter = function(sel) {
    return this.prevUntil(sel).length !== this.prevAll().length;
}; // end $.fn.isAfter