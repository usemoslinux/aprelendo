// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function() {
    const list = ListLoader.create({
        endpoint: "/ajax/getsharedtexts.php",
        content_selector: "#shared-texts-content",
        loader_selector: "#shared-texts-loader",
        parameter_defaults: { s: "", o: 0, ft: 0, fl: 0, p: 1 },
        on_render: () => {
            if (typeof initTooltips === "function") {
                initTooltips();
            }
        },
        on_popstate: (params) => {
            $("#s").val(params.s);
        }
    });

    list.initialize();

    $("#shared-texts-filter-form").on("submit", function(e) {
        e.preventDefault();
        list.updateParams({ s: $("#s").val().trim() }, { reset_page: true });
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
            list.updateParams({ ft: $item.data('value') || 0 }, { reset_page: true });
        } else {
            list.updateParams({ fl: $item.data('value') || 0 }, { reset_page: true });
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

});
