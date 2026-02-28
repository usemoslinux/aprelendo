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

$(document).ready(function() {
    let current_params = {
        s: new URLSearchParams(window.location.search).get('s') || '',
        o: new URLSearchParams(window.location.search).get('o') || 0,
        ft: new URLSearchParams(window.location.search).get('ft') || 0,
        fl: new URLSearchParams(window.location.search).get('fl') || 0,
        p: new URLSearchParams(window.location.search).get('p') || 1
    };

    /**
     * Loads shared texts list via AJAX
     */
    async function loadSharedTexts() {
        $("#shared-texts-loader").removeClass("d-none");
        $("#shared-texts-content").addClass("opacity-50");

        try {
            const query_str = new URLSearchParams(current_params).toString();
            const response = await fetch(`/ajax/getsharedtexts.php?${query_str}`);
            
            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to fetch shared texts');
            }
            
            $("#shared-texts-content").html(data.payload.html);
            
            // Update URL without reloading
            const new_url = window.location.pathname + '?' + query_str;
            window.history.pushState(current_params, '', new_url);

            // Re-initialize tooltips if available
            if (typeof Tooltips !== 'undefined') Tooltips.init();
        } catch (error) {
            console.error(error);
            $("#shared-texts-content").html(`<div class="alert alert-danger">Error: ${error.message}</div>`);
        } finally {
            $("#shared-texts-loader").addClass("d-none");
            $("#shared-texts-content").removeClass("opacity-50");
        }
    }

    // Initial load
    loadSharedTexts();

    $("#shared-texts-filter-form").on("submit", function(e) {
        e.preventDefault();
        current_params.s = $("#s").val().trim();
        current_params.p = 1;
        loadSharedTexts();
    });

    /**
     * Handle Filter menu clicks
     */
    $(document).on("click", "#filter-dropdown .dropdown-item", function(e) {
        e.preventDefault();
        const $item = $(this);

        const is_type = $item.is('.ft');
        const selector = is_type ? '.ft' : '.fl';
        
        $item.parent().find(selector + '.active').removeClass('active');
        $item.addClass('active');

        if (is_type) {
            current_params.ft = $item.data('value') || 0;
        } else {
            current_params.fl = $item.data('value') || 0;
        }

        current_params.p = 1;
        loadSharedTexts();
    });

    /**
     * Handle Sorting
     */
    $(document).on("click", "#dropdown-menu-sort .o", function(e) {
        e.preventDefault();
        current_params.o = $(this).data('value') || 0;
        current_params.p = 1;
        loadSharedTexts();
    });

    /**
     * Handle Pagination clicks
     */
    $(document).on("click", ".pagination a", function(e) {
        e.preventDefault();
        const url = new URL($(this).attr('href'), window.location.origin);
        current_params.p = url.searchParams.get('p') || 1;
        loadSharedTexts();
    });

    // Handle browser back/forward
    window.onpopstate = function(event) {
        if (event.state) {
            current_params = event.state;
            $("#s").val(current_params.s);
            loadSharedTexts();
        }
    };
});
