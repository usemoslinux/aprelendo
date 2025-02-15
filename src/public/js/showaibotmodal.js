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
    const prompts = {
        practical: [
            "What are some common collocations with [word]?",
            "Does [word] have any idiomatic uses or expressions?",
            "Can you provide some example sentences showing [word] in context?",
            "Are there any common mistakes people make when using [word]?"
        ],

        formality: [
            "Is [word] formal or informal?",
            "What are some more formal alternatives to [word]?",
            "What are some more informal alternatives to [word]?",
            "Are there situations where [word] is inappropriate or too formal/informal?",
            "How does the formality of [word] change in different contexts?"
        ],

        synonyms: [
            "In a nutshell, what is the difference between [word] and ...?",
            "What are some synonyms of [word]?",
            "What are some antonyms of [word]?"
        ],

        context: [
            "Is the term [word] used more in written or spoken language?",
            "In what professions or fields is [word] most commonly used?",
            "How does the use of [word] vary by region or country?",
            "Does the term [word] have different nuances depending on the cultural context?",
            "How is [word] commonly pronounced in different regions or accents?",
            "Are there any social situations where the use of the term [word] should be avoided?"
        ],

        pop: [
            "How has the meaning of [word] evolved in recent years?",
            "Has [word] gained or lost popularity over time?",
            "What current cultural references prominently feature [word]?",
            "How do different generations use [word] differently?"
        ]
    };
    // Replace with your Hugging Face API Key
    const API_KEY = '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';
    const MODEL = 'microsoft/Phi-3.5-mini-instruct'; // Replace with the model you want
    const language = 'English'; // Replace with the actual language variable

    // Function to replace [word] with quoted word
    function replaceWordWithQuotes(text, word) {
        return text.replace(/\[word\]/g, `"${word}"`);
    }

    // Handle modal show event
    $('#ask-ai-bot-modal').on('show.bs.modal', function () {
        // Reset form when modal is shown
        $('#prompt-category').val('');
        $('#prompt-select').attr('disabled', true).empty().append('<option value="">First select a category...</option>');
        $('#custom-prompt').val('');
        $('#prompt-form').show();
        $('#ai-answer').hide();
        $('#text-ai-answer').val('');
        $('#back-footer').hide();
        $('#normal-footer').show();
    });

    // Handle category selection
    $('#prompt-category').change(function () {
        const category = $(this).val();
        const prompt_select = $('#prompt-select');
        const currentWord = $('#ask-ai-bot-modal').attr('data-word');

        if (category) {
            prompt_select.removeAttr('disabled').empty().append('<option value="">Choose a prompt...</option>');
            prompts[category].forEach((prompt, index) => {
                const formattedPrompt = replaceWordWithQuotes(prompt, currentWord);
                prompt_select.append(`<option value="${index}">${formattedPrompt}</option>`);
            });
        } else {
            prompt_select.attr('disabled', true).empty().append('<option value="">First select a category...</option>');
            $('#custom-prompt').val('');
        }
    });

    // Handle prompt selection
    $('#prompt-select').change(function () {
        const category = $('#prompt-category').val();
        const promptIndex = $(this).val();
        const currentWord = $('#ask-ai-bot-modal').attr('data-word');

        if (promptIndex !== '') {
            const selected_prompt = replaceWordWithQuotes(prompts[category][promptIndex], currentWord);
            $('#custom-prompt').val(selected_prompt);
        } else {
            $('#custom-prompt').val('');
        }
    });

    $('#btn-ask-ai-bot').click(async function () {
        const custom_prompt = $('#custom-prompt').val();
        if (custom_prompt) {
            const currentWord = $('#ask-ai-bot-modal').attr('data-word');
            const prompt = replaceWordWithQuotes(custom_prompt, currentWord);
    
            $('#prompt-form').hide();
            $('#ai-answer').show();
            $('#normal-footer').hide();
            $('#back-footer').show();
            $('#text-ai-answer').val(''); // Clear previous response
    
            try {
                const response = await fetch('/ajax/getaireply.php', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `prompt=${encodeURIComponent(prompt)}`
                });
    
                if (!response.ok) {
                    throw new Error('Failed to get AI response');
                }
    
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
    
                while (true) {
                    const { value, done } = await reader.read();
                    if (done) break;
                    
                    const chunk = decoder.decode(value, { stream: true });
    
                    // Append the received content
                    $('#text-ai-answer').val($('#text-ai-answer').val() + chunk);
                }
            } catch (error) {
                console.error('Error:', error);
                $('#text-ai-answer').val('Failed to get response from AI. Please try again.');
            }
        }
    });

    // Handle Back button click
    $(document).on('click', '#back-to-form', function () {
        $('#back-footer').hide();
        $('#normal-footer').show();
        $('#ai-answer').hide();
        $('#text-ai-answer').val('');
        $('#prompt-form').show();
    });
});