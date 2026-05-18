// SPDX-License-Identifier: GPL-3.0-or-later

document.addEventListener("DOMContentLoaded", () => {
    const alert_box = document.getElementById("alert-box");
    const set_list = document.getElementById("confusion-set-list");
    const set_form = document.getElementById("confusion-set-form");
    const set_id_input = document.getElementById("set-id");
    const set_title_input = document.getElementById("set-title");
    const set_words_input = document.getElementById("set-words");
    const browse_sets_btn = document.getElementById("browse-sets-btn");
    const new_set_btn = document.getElementById("new-set-btn");
    const delete_set_btn = document.getElementById("delete-set-btn");
    const public_sets_modal_elem = document.getElementById("public-sets-modal");
    const public_set_list = document.getElementById("public-set-list");
    const play_set_select = document.getElementById("play-set-select");
    const nuance_page = document.getElementById("nuance-page");
    const start_battle_btn = document.getElementById("start-battle-btn");
    const play_help = document.getElementById("play-help");
    const battle_goal = document.getElementById("battle-goal");
    const battle_card = document.getElementById("battle-card");
    const battle_title = document.getElementById("battle-title");
    const battle_counter = document.getElementById("battle-counter");
    const battle_stage = document.getElementById("battle-stage");
    const battle_loading = document.getElementById("battle-loading");
    const battle_question = document.getElementById("battle-question");
    const battle_sentence = document.getElementById("battle-sentence");
    const battle_choices = document.getElementById("battle-choices");
    const battle_feedback = document.getElementById("battle-feedback");
    const next_card_btn = document.getElementById("next-card-btn");
    const battle_results = document.getElementById("battle-results");

    let sets = [];
    let public_sets = [];
    let selected_set_id = 0;
    let battle_cards = [];
    let battle_words = [];
    let current_card_index = 0;
    let correct_answers = 0;
    let selected_answers = [];
    const has_lingobot = nuance_page.dataset.hasLingobot === "1";
    const start_battle_btn_text = start_battle_btn.textContent.trim();
    const public_sets_modal = new bootstrap.Modal(public_sets_modal_elem);
    const CLASH_MIN_X = 42;
    const CLASH_MAX_X = 58;
    const OUTCOME_ANIMATION_MS = 700;

    Dictionaries.fetchURIs();

    /**
     * Escapes HTML before writing user content into templates.
     * @param {string} value
     * @returns {string}
     */
    function escapeHtml(value) {
        const div = document.createElement("div");
        div.textContent = value;
        return div.innerHTML;
    }

    /**
     * Shows a Bootstrap alert message.
     * @param {string} message
     * @param {string} type
     */
    function showAlert(message, type = "success") {
        alert_box.className = `alert alert-${type}`;
        alert_box.textContent = message;
        window.scrollTo(0, 0);
    }

    /**
     * Clears the alert box.
     */
    function clearAlert() {
        alert_box.className = "d-none";
        alert_box.textContent = "";
    }

    /**
     * Sends a request to the confusion sets endpoint.
     * @param {string} action
     * @param {FormData|null} form_data
     * @returns {Promise<Object>}
     */
    async function sendRequest(action, form_data = null) {
        const options = {
            headers: {
                Accept: "application/json",
            },
        };

        if (form_data) {
            form_data.set("action", action);
            options.method = "POST";
            options.body = form_data;
        } else {
            options.method = "GET";
        }

        const url = form_data
            ? "/ajax/confusionsets.php"
            : `/ajax/confusionsets.php?action=${action}`;
        const response = await fetch(url, options);
        return response.json();
    }

    /**
     * Loads sets from the server.
     */
    async function loadSets() {
        const data = await sendRequest("list");

        if (!data.success) {
            showAlert(data.error_msg || "Could not load sets.", "danger");
            return;
        }

        sets = data.payload.sets;
        renderSets();
        renderPlayOptions();
    }

    /**
     * Renders the set list.
     */
    function renderSets() {
        if (sets.length === 0) {
            set_list.innerHTML = `
                <div class="text-secondary">
                    No nuance sets yet.
                </div>
            `;
            return;
        }

        set_list.innerHTML = sets
            .map((set) => {
                const active_class = set.id === selected_set_id ? "active" : "";
                const word_count =
                    set.words.length === 1
                        ? "1 word"
                        : `${set.words.length} words`;

                return `
                <button type="button" class="list-group-item list-group-item-action ${active_class}"
                    data-set-id="${set.id}">
                    <div class="d-flex justify-content-between gap-2">
                        <span class="fw-semibold">${escapeHtml(set.title)}</span>
                        <span class="badge text-bg-light">${word_count}</span>
                    </div>
                    <div class="small text-truncate">
                        ${escapeHtml(set.words.join(", "))}
                    </div>
                </button>
            `;
            })
            .join("");
    }

    /**
     * Loads public sets for the active language.
     */
    async function loadPublicSets() {
        public_set_list.innerHTML = `
            <div class="placeholder-glow">
                <p><span class="placeholder col-8"></span></p>
                <p><span class="placeholder col-6"></span></p>
            </div>
        `;
        public_sets_modal.show();

        try {
            const data = await sendRequest("public_list");

            if (!data.success) {
                public_set_list.innerHTML = `
                    <div class="alert alert-danger mb-0">
                        ${escapeHtml(data.error_msg || "Could not load public sets.")}
                    </div>
                `;
                return;
            }

            public_sets = data.payload.sets;
            renderPublicSets();
        } catch (error) {
            public_set_list.innerHTML = `
                <div class="alert alert-danger mb-0">
                    ${escapeHtml(error.message || "Could not load public sets.")}
                </div>
            `;
        }
    }

    /**
     * Renders public sets that can be copied.
     */
    function renderPublicSets() {
        if (public_sets.length === 0) {
            public_set_list.innerHTML = `
                <div class="text-secondary">
                    No sets from other users are available for this language yet.
                </div>
            `;
            return;
        }

        public_set_list.innerHTML = public_sets
            .map((set) => {
                const word_count =
                    set.words.length === 1
                        ? "1 word"
                        : `${set.words.length} words`;

                return `
                    <div class="list-group-item">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                            <div>
                                <div class="fw-semibold">${escapeHtml(set.title)}</div>
                                <div class="small text-secondary">
                                    ${word_count}: ${escapeHtml(set.words.join(", "))}
                                </div>
                            </div>
                            <div class="d-grid align-self-md-center">
                                <button type="button" class="btn btn-sm btn-outline-primary copy-public-set-btn"
                                    data-set-id="${set.id}">
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            })
            .join("");
    }

    /**
     * Copies a public set into the current user's list.
     * @param {number} source_set_id
     * @param {HTMLButtonElement} button
     */
    async function copyPublicSet(source_set_id, button) {
        button.disabled = true;
        button.textContent = "Copying...";

        const form_data = new FormData();
        form_data.set("id", source_set_id);
        try {
            const data = await sendRequest("copy_public", form_data);

            if (!data.success) {
                button.disabled = false;
                button.textContent = "Copy";
                showAlert(data.error_msg || "Could not copy this set.", "danger");
                return;
            }

            sets = data.payload.sets;
            selectSet(Number(data.payload.id));
            renderPlayOptions();
            public_sets_modal.hide();
            showAlert("Set copied.");
        } catch (error) {
            button.disabled = false;
            button.textContent = "Copy";
            showAlert(error.message || "Could not copy this set.", "danger");
        }
    }

    /**
     * Renders the play tab selector.
     */
    function renderPlayOptions() {
        if (sets.length === 0) {
            play_set_select.innerHTML = "<option>No sets available</option>";
            play_set_select.disabled = true;
            start_battle_btn.disabled = true;
            renderBattleGoal(0);
            return;
        }

        play_set_select.disabled = false;
        start_battle_btn.disabled = !has_lingobot;
        play_set_select.innerHTML = sets
            .map((set) => {
                return `<option value="${set.id}">${escapeHtml(set.title)} (${set.words.length})</option>`;
            })
            .join("");
        updateBattleGoalFromSelectedSet();

        if (!has_lingobot) {
            play_help.innerHTML =
                "Configure Lingobot in your profile to generate fair contrastive cards.";
        }
    }

    /**
     * Selects a set for editing.
     * @param {number} set_id
     */
    function selectSet(set_id) {
        const selected_set = sets.find((set) => set.id === set_id);

        if (!selected_set) {
            return;
        }

        selected_set_id = selected_set.id;
        set_id_input.value = selected_set.id;
        set_title_input.value = selected_set.title;
        set_words_input.value = selected_set.words.join("\n");
        delete_set_btn.classList.remove("d-none");
        renderSets();
    }

    /**
     * Resets the editor for a new set.
     */
    function resetForm() {
        selected_set_id = 0;
        set_form.reset();
        set_id_input.value = "";
        delete_set_btn.classList.add("d-none");
        renderSets();
        set_title_input.focus();
    }

    /**
     * Shuffles a copy of an array.
     * @param {Array} values
     * @returns {Array}
     */
    function shuffle(values) {
        const shuffled_values = [...values];

        for (let i = shuffled_values.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled_values[i], shuffled_values[j]] = [
                shuffled_values[j],
                shuffled_values[i],
            ];
        }

        return shuffled_values;
    }

    /**
     * Builds the sessionStorage key for generated cards.
     * @param {Object} set
     * @returns {string}
     */
    function buildCacheKey(set) {
        return `nuance-battle:${set.id}:${set.words.join("|")}`;
    }

    /**
     * Returns the selected set.
     * @returns {Object|null}
     */
    function getSelectedPlaySet() {
        const set_id = Number(play_set_select.value);
        return sets.find((set) => set.id === set_id) || null;
    }

    /**
     * Clears the final stage artwork state.
     */
    function clearBattleOutcome() {
        battle_stage.classList.remove(
            "is-draw",
            "is-win",
            "is-lose",
            "is-ending-draw",
            "is-ending-win",
            "is-ending-lose",
        );
    }

    /**
     * Returns the final result from a score ratio.
     * @param {number} score_ratio
     * @returns {string}
     */
    function getBattleOutcome(score_ratio) {
        if (score_ratio <= 1 / 3) {
            return "lose";
        }

        if (score_ratio >= 2 / 3) {
            return "win";
        }

        return "draw";
    }

    /**
     * Shows the final stage artwork for the completed session.
     * @param {string} outcome
     */
    function setBattleOutcome(outcome) {
        clearBattleOutcome();
        battle_stage.classList.add(`is-${outcome}`);
    }

    /**
     * Returns the current score as a 0-1 ratio. A session with no answers starts centered.
     * @returns {number}
     */
    function getCurrentScoreRatio() {
        const answered_cards = selected_answers.length;

        return answered_cards > 0 ? correct_answers / answered_cards : 0.5;
    }

    /**
     * Moves the beam clash to the score ratio.
     * 0 means minimum user beam, 0.5 is centered, and 1 means maximum user beam.
     * @param {number} score_ratio
     */
    function setBattleClash(score_ratio) {
        const bounded_ratio = Math.min(Math.max(score_ratio, 0), 1);
        const clash_range = CLASH_MAX_X - CLASH_MIN_X;
        const clash_x = CLASH_MIN_X + bounded_ratio * clash_range;

        battle_stage.style.setProperty("--clash-x", `${clash_x}%`);
    }

    /**
     * Updates the beam clash point based on the current answer score.
     */
    function updateBattleStage() {
        setBattleClash(getCurrentScoreRatio());
    }

    /**
     * Shows a loading placeholder in the card counter.
     */
    function showBattleCounterPlaceholder() {
        battle_counter.className = "badge text-bg-secondary placeholder col-2";
        battle_counter.textContent = "";
        battle_counter.setAttribute("aria-hidden", "true");
    }

    /**
     * Updates the card counter text.
     * @param {string} text
     */
    function setBattleCounterText(text) {
        battle_counter.className = "badge text-bg-secondary";
        battle_counter.removeAttribute("aria-hidden");
        battle_counter.textContent = text;
    }

    /**
     * Animates the active beams before the final artwork is shown.
     * @param {string} outcome
     * @returns {Promise<void>}
     */
    function animateBattleOutcome(outcome) {
        clearBattleOutcome();

        if (outcome === "win") {
            setBattleClash(1);
        } else if (outcome === "lose") {
            setBattleClash(0);
        } else {
            setBattleClash(0.5);
        }

        battle_stage.classList.add(`is-ending-${outcome}`);

        return new Promise((resolve) => {
            window.setTimeout(resolve, OUTCOME_ANIMATION_MS);
        });
    }

    /**
     * Updates the visible goal text for the selected set.
     * @param {number} card_count
     */
    function renderBattleGoal(card_count) {
        if (card_count <= 0) {
            battle_goal.classList.add("d-none");
            battle_goal.textContent = "";
            return;
        }

        const win_count = Math.ceil(card_count * 2 / 3);
        const lose_count = Math.floor(card_count / 3);

        battle_goal.textContent =
            `Choose at least ${win_count} of ${card_count} correctly to win. ${lose_count} or fewer means you lose.`;
        battle_goal.classList.remove("d-none");
    }

    /**
     * Updates the visible goal text from the selected play set.
     */
    function updateBattleGoalFromSelectedSet() {
        const set = getSelectedPlaySet();
        renderBattleGoal(set ? set.words.length : 0);
    }

    /**
     * Shows or hides the loading state for battle generation.
     * @param {boolean} is_loading
     */
    function setBattleLoading(is_loading) {
        battle_card.classList.remove("d-none");
        battle_loading.classList.toggle("d-none", !is_loading);
        battle_question.classList.add("d-none");
        battle_results.classList.add("d-none");

        if (is_loading) {
            clearBattleOutcome();
            setBattleClash(0.5);
            showBattleCounterPlaceholder();
        }

        start_battle_btn.disabled =
            is_loading || !has_lingobot || sets.length === 0;
    }

    /**
     * Sets the Start Battle button loading state.
     * @param {boolean} is_loading
     */
    function setStartButtonLoading(is_loading) {
        start_battle_btn.disabled =
            is_loading || !has_lingobot || sets.length === 0;

        if (is_loading) {
            start_battle_btn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
                Creating cards...
            `;
            return;
        }

        start_battle_btn.textContent = start_battle_btn_text;
    }

    /**
     * Fetches or reuses generated battle cards for a set.
     * @param {Object} set
     * @returns {Promise<Array>}
     */
    async function getBattleCards(set) {
        const cache_key = buildCacheKey(set);
        const cached_cards = sessionStorage.getItem(cache_key);

        if (cached_cards) {
            try {
                return JSON.parse(cached_cards);
            } catch (error) {
                sessionStorage.removeItem(cache_key);
            }
        }

        setStartButtonLoading(true);
        const form_data = new FormData();
        form_data.set("set_id", set.id);

        try {
            const response = await fetch("/ajax/generatenuancecards.php", {
                method: "POST",
                headers: {
                    Accept: "application/json",
                },
                body: form_data,
            });

            if (!response.ok) {
                throw new Error(`HTTP error: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error_msg || "Could not generate cards.");
            }

            sessionStorage.setItem(cache_key, JSON.stringify(data.payload.cards));
            return data.payload.cards;
        } finally {
            setStartButtonLoading(false);
        }
    }

    /**
     * Starts a Nuance Battle session.
     */
    async function startBattle() {
        clearAlert();

        if (!has_lingobot) {
            showAlert(
                "Configure Lingobot in your profile to play Nuance Battle.",
                "warning",
            );
            return;
        }

        const set = getSelectedPlaySet();

        if (!set) {
            showAlert("Create a set before playing.", "warning");
            return;
        }

        try {
            battle_words = set.words;
            battle_title.textContent = set.title;
            setBattleLoading(true);

            const cards = await getBattleCards(set);
            battle_cards = shuffle(cards);
            current_card_index = 0;
            correct_answers = 0;
            selected_answers = [];
            renderBattleGoal(battle_cards.length);
            clearBattleOutcome();
            updateBattleStage();
            renderBattleCard();
        } catch (error) {
            showAlert(error.message, "danger");
            setBattleLoading(false);
            battle_card.classList.add("d-none");
        }
    }

    /**
     * Renders the active battle card.
     */
    function renderBattleCard() {
        const card = battle_cards[current_card_index];
        const shuffled_words = shuffle(battle_words);

        battle_loading.classList.add("d-none");
        battle_question.classList.remove("d-none");
        battle_results.classList.add("d-none");
        battle_feedback.className = "alert mt-3 d-none";
        battle_feedback.textContent = "";
        next_card_btn.classList.add("d-none");
        clearBattleOutcome();
        setBattleCounterText(`Card ${current_card_index + 1}/${battle_cards.length}`);
        battle_sentence.innerHTML = escapeHtml(card.sentence).replace(
            "____",
            '<span class="border-bottom border-2 px-4" aria-label="blank">&nbsp;</span>',
        );

        battle_choices.innerHTML = shuffled_words
            .map((word) => {
                return `
                <button type="button" class="btn btn-outline-primary battle-choice" data-word="${escapeHtml(word)}">
                    ${escapeHtml(word)}
                </button>
            `;
            })
            .join("");
        start_battle_btn.disabled = false;
    }

    /**
     * Enables or disables battle choice buttons.
     * @param {boolean} is_disabled
     */
    function setBattleChoicesDisabled(is_disabled) {
        document.querySelectorAll(".battle-choice").forEach((button) => {
            button.disabled = is_disabled;
        });
    }

    /**
     * Persists a Nuance Battle answer.
     * @param {string} word
     * @param {boolean} is_correct
     * @returns {Promise<void>}
     */
    async function updateNuanceCard(word, is_correct) {
        const form_data = new FormData();
        form_data.set("word", word);
        form_data.set("is_correct", is_correct ? "1" : "0");

        const response = await fetch("/ajax/updatenuancecard.php", {
            method: "POST",
            headers: {
                Accept: "application/json",
            },
            body: form_data,
        });

        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error_msg || "Could not update this word.");
        }
    }

    /**
     * Handles an answer choice.
     * @param {string} selected_word
     * @param {HTMLButtonElement} selected_button
     */
    async function answerCard(selected_word, selected_button) {
        const card = battle_cards[current_card_index];
        const is_correct = selected_word === card.target_word;

        setBattleChoicesDisabled(true);

        try {
            await updateNuanceCard(card.target_word, is_correct);
        } catch (error) {
            showAlert(error.message, "danger");
            setBattleChoicesDisabled(false);
            return;
        }

        selected_answers.push({
            target_word: card.target_word,
            selected_word,
            is_correct,
        });

        if (is_correct) {
            correct_answers++;
        }

        updateBattleStage();

        document.querySelectorAll(".battle-choice").forEach((button) => {
            const button_word = button.dataset.word;

            if (button_word === card.target_word) {
                button.classList.remove("btn-outline-primary");
                button.classList.add("btn-success");
            } else if (button === selected_button) {
                button.classList.remove("btn-outline-primary");
                button.classList.add("btn-danger");
            }
        });

        battle_sentence.innerHTML = escapeHtml(card.sentence).replace(
            "____",
            `<strong>${escapeHtml(card.target_word)}</strong>`,
        );
        battle_feedback.className = `alert mt-3 ${is_correct ? "alert-success" : "alert-warning"}`;
        battle_feedback.innerHTML = `
            <div class="fw-semibold">${is_correct ? "Correct" : "Not quite"}</div>
            <div>${escapeHtml(card.explanation)}</div>
        `;
        next_card_btn.textContent =
            current_card_index + 1 >= battle_cards.length
                ? "Show Results"
                : "Next";
        next_card_btn.classList.remove("d-none");
        next_card_btn.focus();
    }

    /**
     * Advances to the next battle card or results.
     */
    function advanceBattle() {
        current_card_index++;

        if (current_card_index >= battle_cards.length) {
            renderBattleResults();
            return;
        }

        renderBattleCard();
    }

    /**
     * Renders the battle session summary.
     */
    async function renderBattleResults() {
        battle_question.classList.add("d-none");
        battle_results.classList.add("d-none");
        setBattleCounterText(`${battle_cards.length}/${battle_cards.length} cards shown`);
        const score_ratio = correct_answers / battle_cards.length;
        const outcome = getBattleOutcome(score_ratio);
        let result_title = "Draw";

        if (outcome === "lose") {
            result_title = "You Lose";
        } else if (outcome === "win") {
            result_title = "You Win";
        }

        await animateBattleOutcome(outcome);
        setBattleOutcome(outcome);

        const rows = selected_answers
            .map((answer) => {
                const result_class = answer.is_correct
                    ? "text-success"
                    : "text-danger";
                const result_text = answer.is_correct
                    ? "Correct"
                    : `Picked ${escapeHtml(answer.selected_word)}`;

                return `
                <tr>
                    <td><a class="word fw-bold">${escapeHtml(answer.target_word)}</a></td>
                    <td class="${result_class}">${result_text}</td>
                </tr>
            `;
            })
            .join("");

        battle_results.innerHTML = `
            <h5>${result_title}</h5>
            <p class="text-secondary">You chose ${correct_answers} of ${battle_cards.length} words correctly.</p>
            <table class="table table-bordered table-striped text-center mx-auto mt-3 small" style="max-width: 550px">
                <thead>
                    <tr>
                        <th>Word</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
            <button type="button" class="btn btn-success" id="play-again-btn">Play Again</button>
        `;
        battle_results.classList.remove("d-none");
    }

    set_list.addEventListener("click", (event) => {
        const item = event.target.closest("[data-set-id]");

        if (!item) {
            return;
        }

        selectSet(Number(item.dataset.setId));
    });

    browse_sets_btn.addEventListener("click", () => {
        clearAlert();
        loadPublicSets();
    });

    public_set_list.addEventListener("click", (event) => {
        const button = event.target.closest(".copy-public-set-btn");

        if (!button) {
            return;
        }

        copyPublicSet(Number(button.dataset.setId), button);
    });

    new_set_btn.addEventListener("click", () => {
        clearAlert();
        resetForm();
    });

    set_form.addEventListener("submit", async (event) => {
        event.preventDefault();
        clearAlert();

        const form_data = new FormData(set_form);
        const data = await sendRequest("save", form_data);

        if (!data.success) {
            showAlert(data.error_msg || "Could not save this set.", "danger");
            return;
        }

        sets = data.payload.sets;
        selectSet(Number(data.payload.id));
        renderPlayOptions();
        showAlert("Set saved.");
    });

    delete_set_btn.addEventListener("click", async () => {
        if (!selected_set_id || !window.confirm("Delete this set?")) {
            return;
        }

        clearAlert();

        const form_data = new FormData();
        form_data.set("id", selected_set_id);
        const data = await sendRequest("delete", form_data);

        if (!data.success) {
            showAlert(data.error_msg || "Could not delete this set.", "danger");
            return;
        }

        sets = data.payload.sets;
        resetForm();
        renderPlayOptions();
        showAlert("Set deleted.");
    });

    start_battle_btn.addEventListener("click", startBattle);

    play_set_select.addEventListener("change", updateBattleGoalFromSelectedSet);

    battle_choices.addEventListener("click", (event) => {
        const selected_button = event.target.closest(".battle-choice");

        if (!selected_button) {
            return;
        }

        answerCard(selected_button.dataset.word, selected_button);
    });

    next_card_btn.addEventListener("click", advanceBattle);

    battle_results.addEventListener("click", (event) => {
        if (event.target.id === "play-again-btn") {
            startBattle();
        }
    });

    $("body").on("click", ".word", function () {
        StudyActionBtns.show($(this));
    });

    $(document).on("mouseup touchend", function (event) {
        if (
            $(event.target).is(".word") === false
            && !$(event.target).closest("#action-buttons").length > 0
        ) {
            event.stopPropagation();
            ActionBtns.hide();
        }
    });

    loadSets();
});
