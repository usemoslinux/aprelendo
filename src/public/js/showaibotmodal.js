// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(initializeAIBotModal);

/**
 * Initializes the AI bot modal and binds all provider, prompt, and modal events.
 *
 * @returns {void}
 */
function initializeAIBotModal() {
    const prompt_templates = {
        formality: [
            'Is [word] considered formal, neutral, or informal? In which settings should it be used?',
            'What are some alternatives to [word] that would sound more formal?',
            'What are some more casual or slang alternatives to [word]?',
            'Are there situations where using [word] might be inappropriate due to formality or tone?',
            'Does the level of formality of [word] change depending on the region, dialect, or culture?'
        ],
        synonyms: [
            'What are the subtle differences between [word] and its closest synonyms?',
            'In a nutshell, what is the difference between [word] and ... ',
            'What are some synonyms of [word]?',
            'What are some antonyms of [word]?',
            'Are there words that are often confused with [word] but actually mean something different?'
        ],
        context: [
            'Is [word] more commonly found in spoken or written language? Can you give examples?',
            'In what professional fields or industries is [word] frequently used?',
            'How does the use of [word] differ across countries or dialects? Are there any regional variations?',
            'Does [word] carry cultural connotations that may not translate directly into other languages?',
            'How is [word] commonly pronounced in different regions or accents?',
            'Are there contexts where using [word] could be considered rude or inappropriate?',
            'Are there double meanings or slang uses of [word] in different cultures that learners should be aware of?'
        ],
        practical: [
            'What are some common collocations with [word]?',
            'Does [word] have any idiomatic uses or expressions?',
            'Can you provide several example sentences that illustrate different meanings or nuances of [word]?'
        ],
        pop: [
            'Has the meaning of [word] changed or evolved over time? If so, how?',
            'Is [word] currently trending or used in pop culture, social media, or memes?',
            'What are some well-known books, movies, or songs that feature [word]?',
            'How do different generations use [word] differently?'
        ],
        personalized: [
            'What are the most common mistakes learners from my native language make when using [word]?',
            'What are the most common translations to my native language of [word]?',
            'For beginners, what\'s the easiest way to remember and use [word] correctly?',
            'For advanced learners, what are some nuanced uses of [word] that natives commonly use?',
            'What memory tricks or mnemonics can help learners remember [word]?'
        ]
    };
    const provider_labels = {
        lingobot: 'Lingobot',
        claude: 'Claude',
        openai: 'OpenAI'
    };
    const provider_urls = {
        claude: 'https://claude.ai/new',
        openai: 'https://chatgpt.com/'
    };
    const default_provider = $('#ai-provider').val();
    const has_hf_token = $('#ask-ai-bot-modal').attr('data-has-hf-token') === '1';

    $('#ask-ai-bot-modal').on('show.bs.modal', handleModalShow);
    $('#ai-provider').on('change', handleProviderChange);
    $('#prompt-category').on('change', handleCategoryChange);
    $('#prompt-select').on('change', handlePromptSelectionChange);
    $('#btn-ask-ai-bot').on('click', handleAskAIBotClick);
    $(document).on('click', '#back-to-form', handleBackToFormClick);

    updateProviderUI();

    /**
     * Replaces all prompt placeholders with the currently selected word.
     *
     * @param {string} text - Prompt text that may include the `[word]` placeholder.
     * @param {string} word - Current selected word.
     * @returns {string} Prompt text with placeholders replaced.
     */
    function replaceWordWithQuotes(text, word) {
        const safe_word = word || '';
        return text.replace(/\[word\]/g, `"${safe_word}"`);
    }

    /**
     * Returns the word attached to the modal when the user opened it.
     *
     * @returns {string} The current selected word.
     */
    function getCurrentWord() {
        return $('#ask-ai-bot-modal').attr('data-word') || '';
    }

    /**
     * Returns the currently selected AI provider.
     *
     * @returns {string} The provider key.
     */
    function getSelectedProvider() {
        return $('#ai-provider').val() || default_provider;
    }

    /**
     * Clears any inline alert shown inside the AI modal.
     *
     * @returns {void}
     */
    function clearAIBotAlert() {
        $('#ai-bot-alert-box')
            .empty()
            .removeAttr('style')
            .removeClass()
            .addClass('d-none');
    }

    /**
     * Displays an inline alert message inside the AI modal.
     *
     * @param {string} message_html - Alert body HTML.
     * @param {string} alert_type - Bootstrap alert class name.
     * @param {?string} alert_title - Optional alert title.
     * @returns {void}
     */
    function showAIBotAlert(message_html, alert_type = 'alert-warning', alert_title = null) {
        $('#ai-bot-alert-box').removeClass('d-none');
        showMessage(message_html, alert_type, alert_title, 'ai-bot-alert-box');
    }

    /**
     * Resets the prompt-building form to its initial state.
     *
     * @returns {void}
     */
    function resetPromptForm() {
        $('#ai-provider').val(default_provider);
        $('#prompt-category').val('');
        $('#prompt-select')
            .prop('disabled', true)
            .empty()
            .append('<option value="">First select a category...</option>');
        $('#custom-prompt').val('');
        clearAIBotAlert();
        updateProviderUI();
    }

    /**
     * Restores the modal layout from answer view back to form view.
     *
     * @returns {void}
     */
    function showPromptForm() {
        $('#prompt-form').removeClass('d-none');
        $('#ai-answer').addClass('d-none');
        $('#text-ai-answer').val('');
        $('#back-footer').addClass('d-none');
        $('#normal-footer').removeClass('d-none');
    }

    /**
     * Updates helper text and button labels to match the selected provider.
     *
     * @returns {void}
     */
    function updateProviderUI() {
        const selected_provider = getSelectedProvider();
        const provider_label = provider_labels[selected_provider] || 'AI';
        let provider_help = `${provider_label} will open in a new tab with this prompt.`;

        if (selected_provider === 'lingobot') {
            provider_help = has_hf_token
                ? 'Lingobot uses your Hugging Face token and answers inside this modal.'
                : 'Configure your Hugging Face token in your profile to use Lingobot here.';
        }

        $('#ai-provider-help').text(provider_help);
        $('#btn-ask-ai-bot').text(selected_provider === 'lingobot' ? 'Ask AI' : 'Open in New Tab');
    }

    /**
     * Builds the external provider URL that receives the selected prompt.
     *
     * @param {string} provider_name - External provider key.
     * @param {string} prompt_text - Final prompt text.
     * @returns {string} Fully qualified provider URL.
     */
    function buildExternalProviderURL(provider_name, prompt_text) {
        const provider_url = new URL(provider_urls[provider_name]);
        provider_url.searchParams.set('q', prompt_text);

        return provider_url.toString();
    }

    /**
     * Streams a Lingobot response into the modal answer area.
     *
     * @param {string} prompt_text - Final prompt text.
     * @returns {void}
     */
    function askLingobot(prompt_text) {
        $('#prompt-form').addClass('d-none');
        $('#ai-answer').removeClass('d-none');
        $('#normal-footer').addClass('d-none');
        $('#back-footer').removeClass('d-none');
        $('#text-ai-answer').val('Lingobot is thinking...');

        AIBot.streamReply(prompt_text, {
            onUpdate(markdown_so_far) {
                $('#text-ai-answer').val(markdown_so_far);
            },
            onError() {
                $('#text-ai-answer').val('Failed to get response from AI. Please try again.');
            }
        });
    }

    /**
     * Opens Claude or OpenAI in a new tab with the selected prompt.
     *
     * @param {string} provider_name - External provider key.
     * @param {string} prompt_text - Final prompt text.
     * @returns {void}
     */
    function openExternalProvider(provider_name, prompt_text) {
        const provider_url = buildExternalProviderURL(provider_name, prompt_text);
        const provider_window = window.open(provider_url, '_blank', 'noopener,noreferrer');

        if (provider_window) {
            $('#ask-ai-bot-modal').modal('hide');
            return;
        }
    }

    /**
     * Resets the modal state each time the modal is opened.
     *
     * @returns {void}
     */
    function handleModalShow() {
        resetPromptForm();
        showPromptForm();
    }

    /**
     * Updates the provider-specific helper text when the provider changes.
     *
     * @returns {void}
     */
    function handleProviderChange() {
        clearAIBotAlert();
        updateProviderUI();
    }

    /**
     * Fills the prompt dropdown based on the selected category.
     *
     * @returns {void}
     */
    function handleCategoryChange() {
        const category = $('#prompt-category').val();
        const current_word = getCurrentWord();
        const $prompt_select = $('#prompt-select');

        if (category) {
            $prompt_select
                .prop('disabled', false)
                .empty()
                .append('<option value="">Choose a prompt...</option>');

            prompt_templates[category].forEach((prompt_template, index) => {
                const formatted_prompt = replaceWordWithQuotes(prompt_template, current_word);
                $prompt_select.append(`<option value="${index}">${formatted_prompt}</option>`);
            });
            return;
        }

        $prompt_select
            .prop('disabled', true)
            .empty()
            .append('<option value="">First select a category...</option>');
        $('#custom-prompt').val('');
    }

    /**
     * Copies the selected prompt template into the editable prompt textarea.
     *
     * @returns {void}
     */
    function handlePromptSelectionChange() {
        const category = $('#prompt-category').val();
        const prompt_index = $('#prompt-select').val();
        const current_word = getCurrentWord();

        if (prompt_index !== '') {
            const selected_prompt = replaceWordWithQuotes(
                prompt_templates[category][prompt_index],
                current_word
            );
            $('#custom-prompt').val(selected_prompt);
            return;
        }

        $('#custom-prompt').val('');
    }

    /**
     * Sends the prompt to Lingobot or opens the chosen external provider.
     *
     * @returns {void}
     */
    function handleAskAIBotClick() {
        const custom_prompt = ($('#custom-prompt').val() || '').trim();
        const current_word = getCurrentWord();
        const selected_provider = getSelectedProvider();

        if (!custom_prompt) {
            return;
        }

        clearAIBotAlert();

        const prompt_text = replaceWordWithQuotes(custom_prompt, current_word);

        if (selected_provider === 'lingobot') {
            if (!has_hf_token) {
                showAIBotAlert(
                    'Lingobot requires a Hugging Face token. Configure it in your profile or choose Claude/OpenAI.',
                    'alert-warning',
                    'Lingobot unavailable'
                );
                return;
            }

            askLingobot(prompt_text);
            return;
        }

        openExternalProvider(selected_provider, prompt_text);
    }

    /**
     * Returns from the answer screen to the prompt-building form.
     *
     * @returns {void}
     */
    function handleBackToFormClick() {
        showPromptForm();
        clearAIBotAlert();
    }
}
