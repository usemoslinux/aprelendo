/**
 * Copyright (C) 2019 Pablo Castagnino
 * 
 * This file is part of aprelendo.
 * 
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

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
        old_tooltip.hide();
        old_tooltip.dispose();
    }

    tooltip_elem.setAttribute('data-bs-title', tooltip_title);
    return new bootstrap.Tooltip(tooltip_elem, {
        trigger: 'hover'
    });
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
