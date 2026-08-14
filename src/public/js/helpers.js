// SPDX-License-Identifier: GPL-3.0-or-later

/**
 * Shows a plain-text message in an alert box.
 * @param {string} message
 * @param {string} type
 * @param {?string} custom_title Optional custom title to override the default
 * @param {string} box_id Optional alert box ID
 */
function showMessage(message, type, custom_title = null, box_id = 'alert-box') {
    return renderMessage(message, type, custom_title, box_id, false);
}

/**
 * Shows developer-controlled HTML in an alert box.
 * Callers must sanitize content before passing it here.
 * @param {string} html
 * @param {string} type
 * @param {?string} custom_title Optional custom title to override the default
 * @param {string} box_id Optional alert box ID
 */
function showHtmlMessage(html, type, custom_title = null, box_id = 'alert-box') {
    return renderMessage(html, type, custom_title, box_id, true);
}

function renderMessage(message, type, custom_title, box_id, allow_html) {
    const alerts = {
        'alert-success': { title: 'Success', image: 'bi-check-circle-fill' },
        'alert-info': { title: 'Information', image: 'bi-info-circle-fill' },
        'alert-warning': { title: 'Careful', image: 'bi-exclamation-triangle-fill' },
        'alert-danger': { title: 'Oops!', image: 'bi-exclamation-circle-fill' }
    };
    const alert_config = alerts[type] || alerts['alert-info'];
    const $box = $(document.getElementById(box_id));

    if ($box.length === 0) {
        return;
    }

    const $div_flag = $("<div>").addClass("alert-flag fs-5");
    $("<i>").addClass("bi " + alert_config.image).appendTo($div_flag);
    $div_flag.append(document.createTextNode(custom_title || alert_config.title));

    const $div_msg = $("<div>").addClass("alert-msg");
    if (allow_html) {
        $div_msg.html(message);
    } else {
        $div_msg.text(message);
    }

    $box
        .empty()
        .removeAttr('style')
        .removeClass()
        .addClass("alert " + type)
        .append($div_flag, $div_msg);

    $(window).scrollTop(0);
} 

/**
 * Scrolls the current reader container or page to the top.
 * @returns {void}
 */
function scrollToPageTop() {
    const $container = $("#text-container");

    if ($container.length) {
        // Scroll the container itself
        $container.scrollTop(0);
    } else {
        // Fallback: scroll the page
        $("html, body").scrollTop(0);
    }
} 


/**
 * Locks or unlocks scrolling on an element without extending jQuery globally.
 * @param {jQuery} $element
 * @param {boolean} is_locked
 */
function setScrollLocked($element, is_locked) {
    $element.each(function () {
        this.style.overflow = is_locked ? 'hidden' : '';
        this.classList.toggle('overflow-hidden', is_locked);
        this.classList.toggle('overflow-auto', !is_locked);
    });
}

/**
 * Opens an HTTP(S) URL in a new tab.
 * @param {string} url
 * @returns {Window|null}
 */
function openInNewTab(url) {
    try {
        const target_url = new URL(url, window.location.href);
        if (target_url.protocol !== 'http:' && target_url.protocol !== 'https:') {
            return null;
        }

        return window.open(target_url.href, '_blank', 'noopener,noreferrer');
    } catch (error) {
        console.warn('Refused to open invalid URL.', error);
        return null;
    }
}

/**
 * Determines if the user is on a touch-only device.
 * Prefers input capability checks (hover/pointer) so touchscreen laptops
 * with a mouse/trackpad are treated as non-mobile.
 *
 * @returns {boolean} - True if the user is on a mobile device, false otherwise.
 */
function isMobileDevice() {
    // 1. Check for primary input type (Most reliable for modern browsers)
    if (window.matchMedia) {
        const is_mobile_query = window.matchMedia('(pointer: coarse)').matches;
        const is_desktop_query = window.matchMedia('(pointer: fine)').matches;

        // If the primary pointer is coarse (finger), it's likely a mobile context
        if (is_mobile_query && !is_desktop_query) return true;
        // If the primary pointer is fine (mouse), it's likely desktop
        if (is_desktop_query) return false;
    }

    // 2. Fallback to User Agent for specific "Tablet/Mobile" identification
    const ua = navigator.userAgent || navigator.vendor || window.opera;
    const is_mobile_ua = /Mobi|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua);

    // 3. Check for touch support as a tie-breaker
    const hasTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);

    return is_mobile_ua || hasTouch;
} 
