// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function () {
    let current_params = {
        s: new URLSearchParams(window.location.search).get('s') || '',
        o: new URLSearchParams(window.location.search).get('o') || 0,
        p: new URLSearchParams(window.location.search).get('p') || 1
    };

    /**
     * Determines whether a click originated from an interactive element inside a table row.
     *
     * @param {HTMLElement} element
     * @returns {boolean}
     */
    function isInteractiveRowElement(element) {
        return $(element).closest("a, button, input, label, .dropdown-menu").length > 0;
    }

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
            if (typeof Tooltips !== 'undefined') {
                Tooltips.init();
            } else if (typeof initTooltips === 'function') {
                initTooltips();
            }
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

    // Fetch dictionary and translator URIs
    (async () => {
        await Dictionaries.fetchURIs();
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

    /**
     * Returns the word row data associated with a row action trigger.
     *
     * @param {HTMLElement} trigger_elem
     * @returns {{word: string, word_id: string, $row: jQuery}}
     */
    function getWordRowData(trigger_elem) {
        const $row = $(trigger_elem).closest('tr');

        return {
            word: String($row.attr('data-word') || '').trim(),
            word_id: String($row.find('.chkbox-selrow').attr('data-idWord') || '').trim(),
            $row: $row
        };
    }

    /**
     * Opens a word-specific external service when the corresponding base URI is available.
     *
     * @param {string} base_uri
     * @param {string} word
     * @returns {void}
     */
    function openWordService(base_uri, word) {
        if (!base_uri || !word) {
            return;
        }

        openInNewTab(LinkBuilder.forWordInDictionary(base_uri, word));
    }

    /**
     * Opens the AI bot modal with the selected word prefilled.
     *
     * @param {string} word
     * @returns {void}
     */
    function openAIBotModal(word) {
        if (!word) {
            return;
        }

        const $ai_bot_modal = $('#ask-ai-bot-modal');
        $ai_bot_modal.attr('data-word', word);
        $ai_bot_modal.modal('show');
    }

    /**
     * Marks a single word as forgotten using the existing add-word flow.
     *
     * @param {string} word
     * @returns {Promise<void>}
     */
    async function markWordAsForgotten(word) {
        const form_data = new URLSearchParams();
        form_data.append('word', word);
        form_data.append('text_is_shared', '0');

        const response = await fetch("/ajax/addword.php", {
            method: "POST",
            body: form_data
        });

        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error_msg || 'Failed to mark word as forgotten');
        }
    }

    /**
     * Deletes selected words or a single word row.
     *
     * @param {jQuery} $trigger_elem
     * @returns {Promise<void>}
     */
    async function deleteWords($trigger_elem) {
        let ids = [];

        if ($trigger_elem.attr("id") === "mDelete") {
            $("input.chkbox-selrow:checked").each(function () {
                ids.push($(this).attr("data-idWord"));
            });
        } else if ($trigger_elem.hasClass("imDelete")) {
            const row_data = getWordRowData($trigger_elem);

            if (row_data.word_id) {
                ids.push(row_data.word_id);
            }
        }

        if (ids.length === 0) {
            return;
        }

        const form_data = new URLSearchParams();
        form_data.append('wordIDs', JSON.stringify(ids));

        const response = await fetch("/ajax/removeword.php", {
            method: "POST",
            body: form_data
        });

        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error_msg || 'Failed to delete words');
        }
    }

    // Deletes selected words
    $(document).on("click", "#mDelete, .imDelete", async function () {
        if (confirm("Really delete?")) {
            try {
                await deleteWords($(this));
                loadWords(); // Reload list after deletion
            } catch (error) {
                console.error(error);
                alert(`Oops! ${error.message}`);
            }
        }
    });

    /**
     * Marks an individual word row as forgotten.
     */
    $(document).on("click", ".imForgot", async function () {
        const row_data = getWordRowData(this);

        if (!row_data.word) {
            return;
        }

        try {
            await markWordAsForgotten(row_data.word);
            loadWords();
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        }
    });

    /**
     * Opens the selected word in the configured dictionary.
     */
    $(document).on("click", ".imOpenDictionary", function () {
        const row_data = getWordRowData(this);
        const base_uris = Dictionaries.getURIs();

        openWordService(base_uris.dictionary, row_data.word);
    });

    /**
     * Opens the selected word in the configured image dictionary.
     */
    $(document).on("click", ".imOpenImageDictionary", function () {
        const row_data = getWordRowData(this);
        const base_uris = Dictionaries.getURIs();

        openWordService(base_uris.img_dictionary, row_data.word);
    });

    /**
     * Opens the selected word in the configured translator.
     */
    $(document).on("click", ".imOpenTranslator", function () {
        const row_data = getWordRowData(this);
        const base_uris = Dictionaries.getURIs();

        openWordService(base_uris.translator, row_data.word);
    });

    /**
     * Opens the AI bot modal for the selected word.
     */
    $(document).on("click", ".imOpenAIBot", function () {
        const row_data = getWordRowData(this);

        openAIBotModal(row_data.word);
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

    /**
     * Toggles the row checkbox when the user clicks a non-interactive part of the row.
     */
    $(document).on("click", "#words-content tbody tr", function (e) {
        if (isInteractiveRowElement(e.target)) {
            return;
        }

        const $checkbox = $(this).find(".chkbox-selrow").first();

        if ($checkbox.length === 0) {
            return;
        }

        $checkbox.prop("checked", !$checkbox.prop("checked")).trigger("change");
    });

    // Open dictionary modal
    $(document).on("click", ".word", function (e) {
        const base_uris = Dictionaries.getURIs();

        if (!base_uris.dictionary) {
            return;
        }

        const dic_link = LinkBuilder.forWordInDictionary(base_uris.dictionary, $(this).text());
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
