// SPDX-License-Identifier: GPL-3.0-or-later

const ListLoader = (() => {
    function create({ endpoint, content_selector, loader_selector, parameter_defaults, on_render, on_popstate = () => {} }) {
        let current_params = readParams(parameter_defaults);

        function readParams(defaults) {
            const search_params = new URLSearchParams(window.location.search);
            const params = {};

            for (const [key, default_value] of Object.entries(defaults)) {
                params[key] = search_params.get(key) || default_value;
            }

            return params;
        }

        function updateHistory(method) {
            const query_string = new URLSearchParams(current_params).toString();
            const url = window.location.pathname + (query_string ? "?" + query_string : "");
            const history_method = method === "push" ? "pushState" : "replaceState";
            window.history[history_method]({ ...current_params }, "", url);
        }

        async function load({ history = "none" } = {}) {
            const $content = $(content_selector);
            $(loader_selector).removeClass("d-none");
            $content.addClass("d-none");

            if (history !== "none") {
                updateHistory(history);
            }

            try {
                const query_string = new URLSearchParams(current_params).toString();
                const response = await fetch(`${endpoint}?${query_string}`);

                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.error_msg || "Failed to fetch list.");
                }

                $content.html(data.payload.html);
                on_render();
            } catch (error) {
                console.error(error);
                $content.empty().append(
                    $("<div>").addClass("alert alert-danger").text("Error: " + error.message)
                );
            } finally {
                $(loader_selector).addClass("d-none");
                $content.removeClass("d-none");
            }
        }

        function updateParams(updates, { reset_page = false, history = "push" } = {}) {
            Object.assign(current_params, updates);
            if (reset_page && Object.hasOwn(current_params, "p")) {
                current_params.p = 1;
            }
            return load({ history });
        }

        function reload() {
            return load();
        }

        function initialize() {
            updateHistory("replace");
            load();

            window.addEventListener("popstate", () => {
                current_params = readParams(parameter_defaults);
                on_popstate({ ...current_params });
                load();
            });
        }

        function getParams() {
            return current_params;
        }

        return {
            getParams,
            initialize,
            reload,
            updateParams
        };
    }

    return { create };
})();
