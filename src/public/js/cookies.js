// SPDX-License-Identifier: GPL-3.0-or-later

/**
 * Creates a cookie
 * @param {string} name 
 * @param {string} value 
 * @param {integer} days 
 */
function setCookie(name, value, days) {
    let expires;

    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

/**
 * Retrieves a cookie value
 * @param {string} name 
 */
function getCookie(name) {
    const nameEQ = encodeURIComponent(name) + "=";
    const ca = document.cookie.split(';');
    for (const element of ca) {
        let c = element;
        while (c.startsWith(' '))
            c = c.substring(1, c.length);
        if (c.startsWith(nameEQ))
            return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}

/**
 * Deletes a cookie
 * @param {string} name 
 */
function deleteCookie(name) {
    setCookie(name, "", -1);
}