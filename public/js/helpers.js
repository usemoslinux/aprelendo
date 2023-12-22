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
        .removeClass()
        .addClass("alert " + type)
        .append($div_flag, $div_msg);
    $(window).scrollTop(0);
} // end showMessage

/**
 * Returns current URI parameters
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
 * Converts array to URI string with parameters
 * @param {array} arr 
 * @returns string
 */
function parameterizeArray(arr) {
    const params = new URLSearchParams();

    for (const [key, value] of Object.entries(arr)) {
        if (value) {
            params.append(key, value);
        }
    }

    return '?' + params.toString();
} // end parameterizeArray


/**
 * Gets current page file name
 */
function getCurrentFileName() {
    const pathname = new URL(window.location.href).pathname;
    return pathname.substring(pathname.lastIndexOf('/') + 1);
} // end getCurrentFileName