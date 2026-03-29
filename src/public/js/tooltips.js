// SPDX-License-Identifier: GPL-3.0-or-later

/**
 * Replaces the current tooltip instance with a new title.
 *
 * @param {?HTMLElement} tooltip_elem Element that owns the tooltip.
 * @param {string} tooltip_title Tooltip text to render.
 * @returns {?bootstrap.Tooltip} New tooltip instance or null when unavailable.
 */
function setNewTooltip(tooltip_elem, tooltip_title) {
    if (isMobileDevice() || !tooltip_elem) {
        return null;
    }

    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) {
        return null;
    }

    const old_tooltip = bootstrap.Tooltip.getInstance(tooltip_elem);
    if (old_tooltip) {
        old_tooltip.dispose();
    }

    tooltip_elem.setAttribute('data-bs-title', tooltip_title);
    return new bootstrap.Tooltip(tooltip_elem, {
        trigger: 'hover'
    });
}

/**
 * Disposes the current tooltip instance for an element when one exists.
 *
 * @param {?HTMLElement} tooltip_elem Element that owns the tooltip.
 * @returns {void}
 */
function disposeTooltip(tooltip_elem) {
    if (!tooltip_elem) {
        return;
    }

    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) {
        return;
    }

    const existing_tooltip = bootstrap.Tooltip.getInstance(tooltip_elem);
    if (existing_tooltip) {
        existing_tooltip.dispose();
    }
}

/**
 * Initializes all desktop tooltip triggers.
 *
 * @returns {void}
 */
function initTooltips() {
    if (isMobileDevice() || typeof bootstrap === 'undefined' || !bootstrap.Tooltip) {
        return;
    }

    const tooltip_trigger_list = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltip_trigger_list].forEach((tooltip_trigger_elem) => {
        new bootstrap.Tooltip(tooltip_trigger_elem, {
            trigger: 'hover'
        });
    });
}

$(document).ready(initTooltips);
