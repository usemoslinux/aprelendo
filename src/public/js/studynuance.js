// SPDX-License-Identifier: GPL-3.0-or-later

document.addEventListener('DOMContentLoaded', () => {
    const alert_box = document.getElementById('alert-box');
    const set_list = document.getElementById('confusion-set-list');
    const set_form = document.getElementById('confusion-set-form');
    const set_id_input = document.getElementById('set-id');
    const set_title_input = document.getElementById('set-title');
    const set_words_input = document.getElementById('set-words');
    const new_set_btn = document.getElementById('new-set-btn');
    const delete_set_btn = document.getElementById('delete-set-btn');
    const play_set_select = document.getElementById('play-set-select');

    let sets = [];
    let selected_set_id = 0;

    /**
     * Escapes HTML before writing user content into templates.
     * @param {string} value
     * @returns {string}
     */
    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value;
        return div.innerHTML;
    }

    /**
     * Shows a Bootstrap alert message.
     * @param {string} message
     * @param {string} type
     */
    function showAlert(message, type = 'success') {
        alert_box.className = `alert alert-${type}`;
        alert_box.textContent = message;
        window.scrollTo(0, 0);
    }

    /**
     * Clears the alert box.
     */
    function clearAlert() {
        alert_box.className = 'd-none';
        alert_box.textContent = '';
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
                'Accept': 'application/json'
            }
        };

        if (form_data) {
            form_data.set('action', action);
            options.method = 'POST';
            options.body = form_data;
        } else {
            options.method = 'GET';
        }

        const url = form_data ? '/ajax/confusionsets.php' : `/ajax/confusionsets.php?action=${action}`;
        const response = await fetch(url, options);
        return response.json();
    }

    /**
     * Loads sets from the server.
     */
    async function loadSets() {
        const data = await sendRequest('list');

        if (!data.success) {
            showAlert(data.error_msg || 'Could not load sets.', 'danger');
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

        set_list.innerHTML = sets.map((set) => {
            const active_class = set.id === selected_set_id ? 'active' : '';
            const word_count = set.words.length === 1 ? '1 word' : `${set.words.length} words`;

            return `
                <button type="button" class="list-group-item list-group-item-action ${active_class}"
                    data-set-id="${set.id}">
                    <div class="d-flex justify-content-between gap-2">
                        <span class="fw-semibold">${escapeHtml(set.title)}</span>
                        <span class="badge text-bg-light">${word_count}</span>
                    </div>
                    <div class="small text-secondary text-truncate">
                        ${escapeHtml(set.words.join(', '))}
                    </div>
                </button>
            `;
        }).join('');
    }

    /**
     * Renders the play tab selector.
     */
    function renderPlayOptions() {
        if (sets.length === 0) {
            play_set_select.innerHTML = '<option>No sets available</option>';
            play_set_select.disabled = true;
            return;
        }

        play_set_select.disabled = false;
        play_set_select.innerHTML = sets.map((set) => {
            return `<option value="${set.id}">${escapeHtml(set.title)} (${set.words.length})</option>`;
        }).join('');
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
        set_words_input.value = selected_set.words.join('\n');
        delete_set_btn.classList.remove('d-none');
        renderSets();
    }

    /**
     * Resets the editor for a new set.
     */
    function resetForm() {
        selected_set_id = 0;
        set_form.reset();
        set_id_input.value = '';
        delete_set_btn.classList.add('d-none');
        renderSets();
        set_title_input.focus();
    }

    set_list.addEventListener('click', (event) => {
        const item = event.target.closest('[data-set-id]');

        if (!item) {
            return;
        }

        selectSet(Number(item.dataset.setId));
    });

    new_set_btn.addEventListener('click', () => {
        clearAlert();
        resetForm();
    });

    set_form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearAlert();

        const form_data = new FormData(set_form);
        const data = await sendRequest('save', form_data);

        if (!data.success) {
            showAlert(data.error_msg || 'Could not save this set.', 'danger');
            return;
        }

        sets = data.payload.sets;
        selectSet(Number(data.payload.id));
        renderPlayOptions();
        showAlert('Set saved.');
    });

    delete_set_btn.addEventListener('click', async () => {
        if (!selected_set_id || !window.confirm('Delete this set?')) {
            return;
        }

        clearAlert();

        const form_data = new FormData();
        form_data.set('id', selected_set_id);
        const data = await sendRequest('delete', form_data);

        if (!data.success) {
            showAlert(data.error_msg || 'Could not delete this set.', 'danger');
            return;
        }

        sets = data.payload.sets;
        resetForm();
        renderPlayOptions();
        showAlert('Set deleted.');
    });

    loadSets();
});
