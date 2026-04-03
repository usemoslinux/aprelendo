// SPDX-License-Identifier: GPL-3.0-or-later

const ReaderHelpers = (() => {
    /**
     * Posts URL-encoded form data and returns the JSON response payload.
     *
     * @param {string} url
     * @param {Object|URLSearchParams} form_fields
     * @param {string} default_error_message
     * @returns {Promise<object>}
     */
    async function postFormJson(url, form_fields, default_error_message) {
        const form_data = form_fields instanceof URLSearchParams
            ? form_fields
            : new URLSearchParams(form_fields);

        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: form_data
        });

        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.error_msg || default_error_message);
        }

        return data;
    }

    /**
     * Fetches user words for a text fragment and returns annotated HTML.
     *
     * @param {string} text_html
     * @param {string} doclang
     * @returns {Promise<string>}
     */
    async function annotateText(text_html, doclang) {
        const data = await postFormJson("/ajax/getuserwords.php", {
            txt: text_html
        }, "Failed to get user words for underlining");

        return TextUnderliner.apply(data.payload, doclang);
    }

    /**
     * Returns unique lowercase text values from matching elements.
     *
     * @param {string} selector
     * @returns {string[]}
     */
    function collectUniqueWords(selector) {
        const unique_words = [];

        $(selector).each(function () {
            const word = $(this)
                .text()
                .toLowerCase()
                .trim();

            if ($.inArray(word, unique_words) === -1) {
                unique_words.push(word);
            }
        });

        return unique_words;
    }

    /**
     * Builds the payload used to update the user score after review.
     *
     * @returns {object}
     */
    function buildReviewData() {
        return {
            words: {
                new: getUniqueElements(".reviewing.new"),
                learning: getUniqueElements(".reviewing.learning"),
                forgotten: getUniqueElements(".reviewing.forgotten")
            },
            texts: { reviewed: 1 }
        };
    }

    /**
     * Saves reviewed word status and updates the user score.
     *
     * @param {object} options
     * @param {string} options.words_selector
     * @param {?Array} options.text_ids
     * @param {boolean|undefined} options.archive_text
     * @returns {Promise<object>}
     */
    async function saveReviewProgress({
        words_selector = "#text .reviewing",
        text_ids = undefined,
        archive_text = undefined
    } = {}) {
        const reviewed_words = collectUniqueWords(words_selector);

        if (reviewed_words.length === 0) {
            return {
                gems_earned: 0,
                reviewed_words: reviewed_words
            };
        }

        const update_words_payload = {
            words: JSON.stringify(reviewed_words)
        };

        if (text_ids !== undefined) {
            update_words_payload.textIDs = JSON.stringify(text_ids);
        }

        if (archive_text !== undefined) {
            update_words_payload.archivetext = archive_text;
        }

        await postFormJson(
            "/ajax/updatewords.php",
            update_words_payload,
            "Failed to update words status."
        );

        const update_user_score_data = await postFormJson(
            "/ajax/updateuserscore.php",
            { review_data: JSON.stringify(buildReviewData()) },
            "Failed to update user score."
        );

        return {
            gems_earned: update_user_score_data.gems_earned || 0,
            reviewed_words: reviewed_words
        };
    }

    /**
     * Submits the text statistics form after a successful save.
     *
     * @param {object} options
     * @param {number} options.gems_earned
     * @param {number} options.is_shared
     * @returns {void}
     */
    function submitTextStatsForm({ gems_earned, is_shared }) {
        const url = "/textstats";
        const total_words = Number($(".word").length) + Number($(".phrase").length);
        const form = $(
            '<form action="' +
            url +
            '" method="post">' +
            '<input type="hidden" name="created" value="' +
            $(".reviewing.new").length +
            '" />' +
            '<input type="hidden" name="learning" value="' +
            $(".reviewing.learning").length +
            '" />' +
            '<input type="hidden" name="learned" value="' +
            $(".learned").length +
            '" />' +
            '<input type="hidden" name="forgotten" value="' +
            $(".reviewing.forgotten").length +
            '" />' +
            '<input type="hidden" name="total" value="' +
            total_words +
            '" />' +
            '<input type="hidden" name="gems_earned" value="' +
            gems_earned +
            '" />' +
            '<input type="hidden" name="is_shared" value="' +
            is_shared +
            '" />' +
            "</form>"
        );

        $("body").append(form);
        form.trigger("submit");
    }

    /**
     * Returns the active media controller for text/ebook pages.
     *
     * @returns {object|null}
     */
    function resolveMediaController() {
        if (typeof AudioController !== "undefined") {
            return AudioController;
        }

        if (typeof VideoController !== "undefined") {
            return VideoController;
        }

        return null;
    }

    /**
     * Initializes reader dictionary and word-selection behavior.
     *
     * @param {object} options
     * @param {object} options.action_btns
     * @param {object|null} options.controller
     * @param {string} options.source
     * @returns {void}
     */
    function initializeReaderActions({ action_btns, controller, source }) {
        Dictionaries.fetchURIs();

        if (!controller) {
            return;
        }

        const link_builder = source === "video"
            ? LinkBuilder.forTranslationInVideo
            : LinkBuilder.forTranslationInText;

        WordSelection.setupEvents({
            actionBtns: action_btns,
            controller: controller,
            linkBuilder: link_builder
        });
    }

    /**
     * Resolves a static value or callback option.
     *
     * @param {*|function():*} option
     * @returns {*}
     */
    function resolveOptionValue(option) {
        return typeof option === "function" ? option() : option;
    }

    /**
     * Returns the word anchor set targeted by add/remove operations.
     *
     * @param {function():jQuery} get_word_anchors
     * @returns {jQuery}
     */
    function resolveWordAnchors(get_word_anchors) {
        if (typeof get_word_anchors === "function") {
            return get_word_anchors();
        }

        return $("a.word");
    }

    /**
     * Builds the HTML wrapper used to mark reviewed words in the text.
     *
     * @param {string} class_name
     * @param {string} extra_attributes
     * @returns {string}
     */
    function buildWordWrapperHtml(class_name, extra_attributes = "") {
        const trimmed_attributes = String(extra_attributes).trim();
        const attribute_html = trimmed_attributes !== "" ? " " + trimmed_attributes : "";

        return `<a class="${class_name}"${attribute_html}></a>`;
    }

    /**
     * Applies add/forgot underlining to every matching word or phrase occurrence.
     *
     * @param {object} options
     * @param {jQuery} options.$selword
     * @param {function():jQuery} options.get_word_anchors
     * @param {string} options.extra_attributes
     * @returns {void}
     */
    function underlineSelectedWord({
        $selword,
        get_word_anchors,
        extra_attributes = ""
    }) {
        const sel_text = $selword.text();
        const is_phrase = $selword.length > 1;
        const $word_anchors = resolveWordAnchors(get_word_anchors);

        if (is_phrase) {
            const first_word = $selword.eq(0).text();
            const phrase_length = $selword.filter(".word").length;
            const $filter_phrase = $word_anchors.filter(function () {
                return (
                    $(this)
                        .text()
                        .toLowerCase() === first_word.toLowerCase()
                );
            });

            $filter_phrase.each(function () {
                const $last_word = $(this)
                    .nextAll("a.word")
                    .slice(0, phrase_length - 1)
                    .last();

                const $phrase = $(this)
                    .nextUntil($last_word)
                    .addBack()
                    .next("a.word")
                    .addBack();

                if ($phrase.text().toLowerCase() === sel_text.toLowerCase()) {
                    $phrase.wrapAll(buildWordWrapperHtml("word reviewing new", extra_attributes));
                    $phrase.contents().unwrap();
                }
            });

            return;
        }

        const $filter_word = $word_anchors.filter(function () {
            return (
                $(this)
                    .text()
                    .toLowerCase() === sel_text.toLowerCase()
            );
        });

        $filter_word.each(function () {
            const $word = $(this);
            const wrapper_class = $word.is(".new, .learning, .learned, .forgotten")
                ? "word reviewing forgotten"
                : "word reviewing new";

            $word.wrap(buildWordWrapperHtml(wrapper_class, extra_attributes));
        });

        $filter_word.contents().unwrap();
    }

    /**
     * Rebuilds the removed word markup using the latest server state.
     *
     * @param {object} options
     * @param {jQuery} options.$selword
     * @param {string} options.doclang
     * @param {function():jQuery} options.get_word_anchors
     * @returns {Promise<void>}
     */
    async function refreshRemovedWordMarkup({
        $selword,
        doclang,
        get_word_anchors
    }) {
        const get_user_words_data = await postFormJson("/ajax/getuserwords.php", {
            txt: $selword.text()
        }, "Failed to get user words for re-underlining.");

        const $result = $(TextUnderliner.apply(get_user_words_data.payload, doclang));
        const result_word_nodes = $result.filter(".word").get();
        const lang_has_no_word_separators = TextProcessor.langHasNoWordSeparators(doclang);
        const user_words = Array.isArray(get_user_words_data.payload.user_words)
            ? get_user_words_data.payload.user_words
            : [];
        const word_status_map = new Map(
            user_words.map(function (user_word_item) {
                return [String(user_word_item.word).toLowerCase(), user_word_item.status];
            })
        );

        resolveWordAnchors(get_word_anchors)
            .filter(function () {
                return (
                    $(this)
                        .text()
                        .toLowerCase() === $selword.text().toLowerCase()
                );
            })
            .each(function () {
                const $cur_filter = $(this);
                const cur_filter_text = $cur_filter.text();

                for (const result_word_node of result_word_nodes) {
                    const node_text = result_word_node.textContent;
                    let cur_word_match = null;

                    if (lang_has_no_word_separators) {
                        cur_word_match = new RegExp(
                            "(?<![^])" + node_text + "(?![$])",
                            "iug"
                        ).exec(cur_filter_text);
                    } else {
                        cur_word_match = new RegExp(
                            "(?<![\\p{L}|^])" + node_text + "(?![\\p{L}|$])",
                            "iug"
                        ).exec(cur_filter_text);
                    }

                    result_word_node.textContent = cur_word_match ? cur_word_match[0] : "";

                    const word = result_word_node.textContent.toLowerCase();
                    const user_word_status = word_status_map.get(word);

                    if (user_word_status == 2) {
                        result_word_node.classList.remove("learning");
                        result_word_node.classList.add("new");
                    } else if (user_word_status == 3) {
                        result_word_node.classList.remove("learning");
                        result_word_node.classList.add("forgotten");
                    }
                }

                $cur_filter.replaceWith($result.clone());
            });
    }

    /**
     * Binds add/forgot/remove action button handlers for reader pages.
     *
     * @param {object} options
     * @param {string} options.doclang
     * @param {object} options.action_btns
     * @param {object|null} options.controller
     * @param {function():string} options.get_source_id
     * @param {*|function():*} options.text_is_shared
     * @param {boolean} options.sentence_with_context
     * @param {function():jQuery} options.get_word_anchors
     * @param {string|function():string} [options.get_new_word_attributes]
     * @param {string|function():string} [options.word_value_mode]
     * @param {function(object):void} [options.on_add_success]
     * @returns {void}
     */
    function bindWordActionButtons({
        doclang,
        action_btns,
        controller,
        get_source_id,
        text_is_shared,
        sentence_with_context,
        get_word_anchors,
        get_new_word_attributes = "",
        word_value_mode = "original",
        on_add_success = null
    }) {
        $("#btn-add, #btn-forgot")
            .off("click.reader_helpers")
            .on("click.reader_helpers", async function () {
                const $action_button = $(this);
                const $selword = WordSelection.get();
                const sel_text = $selword.text();
                const is_phrase = $selword.length > 1 ? 1 : 0;
                const sent_word_value = word_value_mode === "lowercase"
                    ? sel_text.toLowerCase()
                    : sel_text;

                ActionBtns.setActionMenuLoading($action_button);

                try {
                    await postFormJson("/ajax/addword.php", {
                        word: sent_word_value,
                        is_phrase: is_phrase,
                        source_id: resolveOptionValue(get_source_id),
                        text_is_shared: resolveOptionValue(text_is_shared),
                        sentence: SentenceExtractor.extractSentence($selword, sentence_with_context)
                    }, "Failed to add word.");

                    underlineSelectedWord({
                        $selword: $selword,
                        get_word_anchors: get_word_anchors,
                        extra_attributes: resolveOptionValue(get_new_word_attributes)
                    });

                    TextProcessor.updateAnchorsList();

                    if (typeof on_add_success === "function") {
                        on_add_success({
                            $selword: $selword,
                            sel_text: sel_text,
                            is_phrase: is_phrase
                        });
                    }
                } catch (error) {
                    console.error(error);
                    alert(`Oops! ${error.message}`);
                } finally {
                    ActionBtns.clearActionMenuLoading($action_button);

                    if (action_btns && typeof action_btns.hide === "function") {
                        action_btns.hide();
                    }

                    if (controller && typeof controller.resume === "function") {
                        controller.resume();
                    }
                }
            });

        $("#btn-remove")
            .off("click.reader_helpers")
            .on("click.reader_helpers", async function () {
                const $action_button = $(this);
                const $selword = WordSelection.get();

                ActionBtns.setActionMenuLoading($action_button);

                try {
                    await postFormJson("/ajax/removeword.php", {
                        word: $selword.text().toLowerCase()
                    }, "Failed to remove word.");

                    await refreshRemovedWordMarkup({
                        $selword: $selword,
                        doclang: doclang,
                        get_word_anchors: get_word_anchors
                    });

                    TextProcessor.updateAnchorsList();
                } catch (error) {
                    console.error(error);
                    alert(`Oops! ${error.message}`);
                } finally {
                    ActionBtns.clearActionMenuLoading($action_button);

                    if (action_btns && typeof action_btns.hide === "function") {
                        action_btns.hide();
                    }

                    if (controller && typeof controller.resume === "function") {
                        controller.resume();
                    }
                }
            });
    }

    /**
     * Binds a standard warning for pages with unsaved reader changes.
     *
     * @param {function():boolean} get_should_warn
     * @returns {void}
     */
    function bindBeforeUnloadWarning(get_should_warn) {
        $(window)
            .off("beforeunload.reader_helpers")
            .on("beforeunload.reader_helpers", function () {
                if (get_should_warn()) {
                    return "Press Save before you go or your changes will be lost.";
                }
            });
    }

    return {
        postFormJson,
        annotateText,
        saveReviewProgress,
        submitTextStatsForm,
        resolveMediaController,
        initializeReaderActions,
        bindWordActionButtons,
        bindBeforeUnloadWarning
    };
})();
