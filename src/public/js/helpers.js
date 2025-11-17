/**
 * Shows custom message in the top section of the screen
 * @param {string} html
 * @param {string} type
 * @param {string} custom_title Optional custom title to override the default
 * @param {string} box Optional custom html element ID to show message
 */
function showMessage(html, type, custom_title = null, box = 'alert-box') {
    const alert = {
        'alert-success': { title: 'Success', image: 'bi-check-circle-fill' },
        'alert-info': { title: 'Information', image: 'bi-info-circle-fill' },
        'alert-warning': { title: 'Careful', image: 'bi-exclamation-triangle-fill' },
        'alert-danger': { title: 'Oops!', image: 'bi-exclamation-circle-fill' }
    };

    let title = '';
    let image = '';

    for (const key in alert) {
        if (key === type) {
            title = custom_title ? custom_title : alert[key].title;
            image = alert[key].image;
            break;
        }
    }

    const div_flag_html = '<i class="bi ' + image + '"></i>' + title;
    const $div_flag = $("<div>").addClass("alert-flag fs-5").html(div_flag_html);
    const $div_msg = $("<div>").addClass("alert-msg").html(html);

    $(`#${box}`)
        .empty()
        .removeAttr('style')
        .removeClass()
        .addClass("alert " + type)
        .append($div_flag, $div_msg);

    $(window).scrollTop(0);
} // end showMessage

/**
 * Smoothly scrolls the webpage to the top.
 * @returns {void}
 */
function scrollToPageTop() {
    const $container = $("#text-container");

    if ($container.length) {
        // Scroll the container itself
        $container.animate({ scrollTop: 0 }, "fast");
    } else {
        // Fallback: scroll the page
        $("html, body").animate({ scrollTop: 0 }, "fast");
    }
} // end scrollToPageTop()


/**
 * Calculates the number of unique elements of a specific class in the document, providing a count of unique occurrences.
 *
 * @param {string} class_name - The class name to target within the document.
 * @returns {number} The count of unique textual elements of the specified class.
 */
function getUniqueElements(class_name) {
    let unique_elements = new Set();

    $(class_name).each(function () {
        let text = $(this).text().toLowerCase().trim();
        unique_elements.add(text);
    });

    return unique_elements.size;
} // end getUniqueElements()

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
$.fn.enableScroll = function () {
    return this.each(function () {
        this.style.overflow = '';
        this.classList.remove('overflow-hidden');
        this.classList.add('overflow-auto');
    });
}; // end $.fn.enableScroll

/**
 * Disables scrolling without making text jump around
 */
$.fn.disableScroll = function () {
    return this.each(function () {
        this.style.overflow = 'hidden';
        this.classList.remove('overflow-auto');
        this.classList.add('overflow-hidden');
    });
}; // end $.fn.disableScroll

/**
 * Gets the width of the scrollbar for either the document body or a specific element
 * Uses a cached value for document body scrollbar to avoid repeated DOM operations
 * @param {HTMLElement} [element=document.body] - The element to measure scrollbar width for
 * @returns {number} The width of the scrollbar in pixels
 */
function getScrollbarWidth(element) {
    if (element === document.body) {
        return window.innerWidth - document.documentElement.clientWidth;
    } else {
        // Create a temporary element to measure the scrollbar
        const temp = document.createElement('div');
        temp.style.visibility = 'hidden';
        temp.style.overflow = 'scroll'; // Force a scrollbar
        temp.style.width = '100px'; // Arbitrary width
        temp.style.height = '100px'; // Arbitrary height
        document.body.appendChild(temp);

        // Create a child inside the temp element to measure
        const inner = document.createElement('div');
        inner.style.width = '100%';
        temp.appendChild(inner);

        // Clean up the temporary elements
        temp.parentNode.removeChild(temp);

        return temp.offsetWidth - inner.offsetWidth;
    }
} // end getScrollbarWidth

/**
 * Determines if an element is after another one
 * @param {Jquery object} sel
 */
$.fn.isAfter = function (sel) {
    return this.prevUntil(sel).length !== this.prevAll().length;
}; // end $.fn.isAfter

/**
 * Opens link in new tab
 * @param {string} $url 
 */
function openInNewTab($url) {
    window.open($url, '_blank', 'noopener,noreferrer');
} // end openInNewTab

/**
 * Determines if the user is on a mobile device.
 * This function combines user agent string detection, screen width, and touch capability checks
 * for a robust and accurate mobile device detection.
 *
 * @returns {boolean} - True if the user is on a mobile device, false otherwise.
 */
function isMobileDevice() {
    const userAgentCheck = /Mobi|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i.test(navigator.userAgent);
    const screenWidthCheck = window.innerWidth <= 768;
    const touchDeviceCheck = 'ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0;

    return userAgentCheck || screenWidthCheck || touchDeviceCheck;
} // end isMobileDevice