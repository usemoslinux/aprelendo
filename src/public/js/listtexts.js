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
        sa: new URLSearchParams(window.location.search).get('sa') || 0,
        p: new URLSearchParams(window.location.search).get('p') || 1
    };

    /**
     * Loads texts list via AJAX
     */
    async function loadTexts() {
        $("#texts-loader").removeClass("d-none");
        $("#texts-content").addClass("opacity-50");

        try {
            const query_str = new URLSearchParams(current_params).toString();
            const response = await fetch(`/ajax/gettexts.php?${query_str}`);
            
            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || 'Failed to fetch texts');
            }
            
            $("#texts-content").html(data.payload.html);
            
            // Update URL without reloading
            const new_url = window.location.pathname + '?' + query_str;
            window.history.pushState(current_params, '', new_url);

            // Re-initialize tooltips if available
            if (typeof Tooltips !== 'undefined') Tooltips.init();
            toggleActionMenu();
        } catch (error) {
            console.error(error);
            $("#texts-content").html(`<div class="alert alert-danger">Error: ${error.message}</div>`);
        } finally {
            $("#texts-loader").addClass("d-none");
            $("#texts-content").removeClass("opacity-50");
        }
    }

    // Initial load
    loadTexts();

    $("#search").trigger("focus");

    if ($('#modal-achievements').length) {
        $('#modal-achievements').modal('show');
    }

    $("#texts-filter-form").on("submit", function(e) {
        e.preventDefault();
        current_params.s = $("#s").val().trim();
        current_params.p = 1;
        loadTexts();
    });

    /**
     * Deletes selected texts
     */
    $(document).on("click", "#mDelete, .imDelete", async function() {
        if (confirm("Really delete?")) {
            let ids = [];

            if ($(this).attr("id") === "mDelete") {
                $("input.chkbox-selrow:checked").each(function() {
                    ids.push($(this).attr("data-idText"));
                });
            } else if ($(this).hasClass("imDelete")) {
                ids.push($(this).closest('tr').find('input').attr("data-idText"));
            }

            if (ids.length === 0) return;

            try {
                const form_data = new URLSearchParams();
                form_data.append('textIDs', JSON.stringify(ids));
                form_data.append('is_archived', current_params.sa == "1" ? 1 : 0);

                const response = await fetch("/ajax/removetext.php", {
                    method: "POST",
                    body: form_data
                });

                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                const data = await response.json();
                if (!data.success) throw new Error(data.error_msg || 'Failed to delete texts.');

                loadTexts();
            } catch (error) {
                console.error(error);
                alert(`Oops! ${error.message}`);
            }
        }
    });

    /**
     * Archives/Unarchives selected texts
     */
    $(document).on("click", "#mArchive, .imArchive", async function() {
        const archivetxt = $(this).text().trim() === "Archive";
        let ids = [];

        if ($(this).attr("id") === "mArchive") {
            $("input.chkbox-selrow:checked").each(function() {
                ids.push($(this).attr("data-idText"));
            });    
        } else if ($(this).hasClass("imArchive")) {
            ids.push($(this).closest('tr').find('input').attr("data-idText"));
        }

        if (ids.length === 0) return;

        try {
            const form_data = new URLSearchParams();
            form_data.append('textIDs', JSON.stringify(ids));
            form_data.append('archivetext', archivetxt);

            const response = await fetch("/ajax/archivetext.php", {
                method: "POST",
                body: form_data
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
            const data = await response.json();
            if (!data.success) throw new Error(data.error_msg || 'Failed to archive texts.');

            loadTexts();
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
    });

    /**
     * Shares selected text
     */
    $(document).on("click", ".imShare", async function() {
        if (confirm("Sharing this text is irreversible. Are you sure?")) {
            let id = $(this).closest('tr').find('input').attr("data-idText");
            if (id === undefined) return;

            try {
                const form_data = new URLSearchParams({ textID: id });
                const response = await fetch("/ajax/sharetext.php", {
                    method: "POST",
                    body: form_data
                });
                
                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                const data = await response.json();
                if (!data.success) throw new Error(data.error_msg || 'Sharing text failed.');
                
                loadTexts();
            } catch (error) {
                console.error(error);
                alert(`Oops! ${error.message}`);
            }
        }
    });

    $(document).on("click", ".imEdit", function() {
        let id = $(this).closest('tr').find('input').attr("data-idText");
        if (id !== undefined) window.location.href = "addtext?id=" + encodeURIComponent(id);
    });

    function toggleActionMenu() {
        if ($("input.chkbox-selrow:checked").length === 0) {
            $("#actions-menu").addClass("disabled");
        } else {
            $("#actions-menu").removeClass("disabled");
        }
    }

    $(document).on("change", ".chkbox-selrow", toggleActionMenu);

    $(document).on("click", "#chkbox-selall", function(e) {
        $(".chkbox-selrow").prop("checked", $(this).prop("checked"));
        toggleActionMenu();
    });

    /**
     * Handle Filter menu clicks
     */
    $(document).on("click", "#filter-dropdown .dropdown-item", function(e) {
        e.preventDefault();
        const $item = $(this);

        if ($item.is('.sa')) {
            $item.toggleClass("active");
            current_params.sa = $item.hasClass('active') ? 1 : 0;
        } else {
            const is_type = $item.is('.ft');
            const selector = is_type ? '.ft' : '.fl';
            
            $item.parent().find(selector + '.active').removeClass('active');
            $item.addClass('active');

            if (is_type) {
                current_params.ft = $item.data('value') || 0;
            } else {
                current_params.fl = $item.data('value') || 0;
            }
        }

        current_params.p = 1;
        loadTexts();
    });

    /**
     * Handle Sorting
     */
    $(document).on("click", "#dropdown-menu-sort .o", function(e) {
        e.preventDefault();
        current_params.o = $(this).data('value') || 0;
        current_params.p = 1;
        loadTexts();
    });

    /**
     * Handle Pagination clicks
     */
    $(document).on("click", ".pagination a", function(e) {
        e.preventDefault();
        const url = new URL($(this).attr('href'), window.location.origin);
        current_params.p = url.searchParams.get('p') || 1;
        loadTexts();
    });

    /**
     * Hides welcome message
     */
    $(document).on("click", "#welcome-close", function(e) {
        setCookie("hide_welcome_msg", true, 365 * 10);
    });

    // Handle browser back/forward
    window.onpopstate = function(event) {
        if (event.state) {
            current_params = event.state;
            $("#s").val(current_params.s);
            loadTexts();
        }
    };
});
