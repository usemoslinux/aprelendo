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

$(document).ready(function () {
    let dictionary_URI = "";
    let current_params = {
        s: new URLSearchParams(window.location.search).get('s') || '',
        o: new URLSearchParams(window.location.search).get('o') || 0,
        p: new URLSearchParams(window.location.search).get('p') || 1
    };

    /**
     * Loads words list via AJAX
     */
    async function loadWords() {
        $("#words-loader").removeClass("d-none");
        $("#words-content").addClass("opacity-50");

        try {
            const query_str = new URLSearchParams(current_params).toString();
            const response = await fetch(`/ajax/getwords.php?${query_str}`);
            
            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to fetch words');
            }
            
            $("#words-content").html(data.payload.html);
            
            // Update URL without reloading
            const new_url = window.location.pathname + '?' + query_str;
            window.history.pushState(current_params, '', new_url);

            // Re-initialize tooltips and event listeners for new content
            if (typeof Tooltips !== 'undefined') Tooltips.init();
            toggleActionMenu();
        } catch (error) {
            console.error(error);
            $("#words-content").html(`<div class="alert alert-danger">Error: ${error.message}</div>`);
        } finally {
            $("#words-loader").addClass("d-none");
            $("#words-content").removeClass("opacity-50");
        }
    }

    // Initial load
    loadWords();

    // Fetch dictionary URI
    (async () => {
        try {
            const response = await fetch("/ajax/getdicuris.php");
            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
            const data = await response.json();
            if (data.success) dictionary_URI = data.payload.dictionary_uri;
        } catch (error) {
            console.error(error);
        }
    })();

    // Search form submission
    $("#words-filter-form").on("submit", function (e) {
        e.preventDefault();
        current_params.s = $("#s").val().trim();
        current_params.p = 1; // reset to first page on search
        loadWords();
    });

    // Handle sorting
    $(document).on("click", "#dropdown-menu-sort .o", function (e) {
        e.preventDefault();
        current_params.o = $(this).data('value') || 0;
        current_params.p = 1;
        loadWords();
    });

    // Handle pagination clicks
    $(document).on("click", ".pagination a", function (e) {
        e.preventDefault();
        const url = new URL($(this).attr('href'), window.location.origin);
        current_params.p = url.searchParams.get('p') || 1;
        loadWords();
    });

    // Deletes selected words
    $(document).on("click", "#mDelete", async function () {
        if (confirm("Really delete?")) {
            let ids = [];
            $("input.chkbox-selrow:checked").each(function () {
                ids.push($(this).attr("data-idWord"));
            });

            if (ids.length === 0) return;

            try {
                const form_data = new URLSearchParams();
                form_data.append('wordIDs', JSON.stringify(ids));

                const response = await fetch("/ajax/removeword.php", {
                    method: "POST",
                    body: form_data
                });

                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                const data = await response.json();
                
                if (!data.success) throw new Error(data.error_msg || 'Failed to delete words');

                loadWords(); // Reload list after deletion
            } catch (error) {
                console.error(error);
                alert(`Oops! ${error.message}`);
            }
        }
    });

    // Toggle action menu
    function toggleActionMenu() {
        if ($("input.chkbox-selrow:checked").length === 0) {
            $("#actions-menu").addClass("disabled");
        } else {
            $("#actions-menu").removeClass("disabled");
        }
    }

    $(document).on("change", ".chkbox-selrow", toggleActionMenu);

    // Select/Unselect all
    $(document).on("click", "#chkbox-selall", function (e) {
        const is_checked = $(this).prop("checked");
        $(".chkbox-selrow").prop("checked", is_checked);
        toggleActionMenu();
    });

    // Open dictionary modal
    $(document).on("click", ".word", function (e) {
        if (!dictionary_URI) return;
        const dic_link = LinkBuilder.forWordInDictionary(dictionary_URI, $(this).text());
        openInNewTab(dic_link);
    });

    // Handle browser back/forward
    window.onpopstate = function(event) {
        if (event.state) {
            current_params = event.state;
            $("#s").val(current_params.s);
            loadWords();
        }
    };
});
