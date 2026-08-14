// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function() {
    const list = ListLoader.create({
        endpoint: "/ajax/gettexts.php",
        content_selector: "#texts-content",
        loader_selector: "#texts-loader",
        parameter_defaults: { s: "", o: 0, ft: 0, fl: 0, sa: 0, p: 1 },
        on_render: () => {
            if (typeof initTooltips === "function") {
                initTooltips();
            }
            toggleActionMenu();
        },
        on_popstate: (params) => {
            $("#s").val(params.s);
        }
    });

    /**
     * Determines whether a click originated from an interactive element inside a table row.
     *
     * @param {HTMLElement} element
     * @returns {boolean}
     */
    function isInteractiveRowElement(element) {
        return $(element).closest("a, button, input, label, .dropdown-menu").length > 0;
    }

    list.initialize();

    $("#search").trigger("focus");

    if ($('#modal-achievements').length) {
        $('#modal-achievements').modal('show');
    }

    $("#texts-filter-form").on("submit", function(e) {
        e.preventDefault();
        list.updateParams({ s: $("#s").val().trim() }, { reset_page: true });
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
                form_data.append('is_archived', list.getParams().sa == "1" ? 1 : 0);

                const response = await fetch("/ajax/removetext.php", {
                    method: "POST",
                    body: form_data
                });

                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                const data = await response.json();
                if (!data.success) throw new Error(data.error_msg || 'Failed to delete texts.');

                list.reload();
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

            list.reload();
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
                
                list.reload();
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
     * Toggles the row checkbox when the user clicks a non-interactive part of the row.
     */
    $(document).on("click", "#texts-content tbody tr", function(e) {
        if (isInteractiveRowElement(e.target)) {
            return;
        }

        const $checkbox = $(this).find(".chkbox-selrow").first();

        if ($checkbox.length === 0) {
            return;
        }

        $checkbox.prop("checked", !$checkbox.prop("checked")).trigger("change");
    });

    /**
     * Handle Filter menu clicks
     */
    $(document).on("click", "#filter-dropdown .dropdown-item", function(e) {
        e.preventDefault();
        const $item = $(this);

        if ($item.is('.sa')) {
            $item.toggleClass("active");
            list.updateParams({ sa: $item.hasClass('active') ? 1 : 0 }, { reset_page: true });
        } else {
            const is_type = $item.is('.ft');
            const selector = is_type ? '.ft' : '.fl';
            
            $item.parent().find(selector + '.active').removeClass('active');
            $item.addClass('active');

            if (is_type) {
                list.updateParams({ ft: $item.data('value') || 0 }, { reset_page: true });
            } else {
                list.updateParams({ fl: $item.data('value') || 0 }, { reset_page: true });
            }
        }
    });

    /**
     * Handle Sorting
     */
    $(document).on("click", "#dropdown-menu-sort .o", function(e) {
        e.preventDefault();
        list.updateParams({ o: $(this).data('value') || 0 }, { reset_page: true });
    });

    /**
     * Handle Pagination clicks
     */
    $(document).on("click", ".pagination a", function(e) {
        e.preventDefault();
        const url = new URL($(this).attr('href'), window.location.origin);
        list.updateParams({ p: url.searchParams.get('p') || 1 });
    });

    /**
     * Hides welcome message
     */
    $(document).on("click", "#welcome-close", function() {
        setCookie("hide_welcome_msg", true, 365 * 10);
    });

});
