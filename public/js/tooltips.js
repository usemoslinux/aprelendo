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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

function isMobileDevice() {
    let userAgent = navigator.userAgent || navigator.vendor || window.opera;

    // Check for iOS and Android platforms
    if (/android/i.test(userAgent)) {
        return true; // Android
    }

    return !!(/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream); // Not a mobile device
}

$(document).ready(function () {
    // Use Bootstrap tooltips only on desktop
    if (!isMobileDevice()) {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => 
            new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover'
            })
        );
    } 
});