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
        formality: [
            "Is [word] considered formal, neutral, or informal? In which settings should it be used?",
            "What are some alternatives to [word] that would sound more formal?",
            "What are some more casual or slang alternatives to [word]?",
            "Are there situations where using [word] might be inappropriate due to formality or tone?",
            "Does the level of formality of [word] change depending on the region, dialect, or culture?"
        ],

        synonyms: [
            "What are the subtle differences between [word] and its closest synonyms?",
            "In a nutshell, what is the difference between [word] and ... ",
            "What are some synonyms of [word]?",
            "What are some antonyms of [word]?",
            "Are there words that are often confused with [word] but actually mean something different?"
        ],

        context: [
            "Is [word] more commonly found in spoken or written language? Can you give examples?",
            "In what professional fields or industries is [word] frequently used?",
            "How does the use of [word] differ across countries or dialects? Are there any regional variations?",
            "Does [word] carry cultural connotations that may not translate directly into other languages?",
            "How is [word] commonly pronounced in different regions or accents?",
            "Are there contexts where using [word] could be considered rude or inappropriate?",
            "Are there double meanings or slang uses of [word] in different cultures that learners should be aware of?"
        ],

        practical: [
            "What are some common collocations with [word]?",
            "Does [word] have any idiomatic uses or expressions?",
            "Can you provide several example sentences that illustrate different meanings or nuances of [word]?"
        ],

        pop: [
            "Has the meaning of [word] changed or evolved over time? If so, how?",
            "Is [word] currently trending or used in pop culture, social media, or memes?",
            "What are some well-known books, movies, or songs that feature [word]?",
            "How do different generations use [word] differently?"
        ],

        personalized: [
            "What are the most common mistakes learners from my native language make when using [word]?",
            // "Does [word] exist in my native language but mean something different?",
            // "Is [word] a 'false friend' with a similar-looking word in my native language?",
            "What are the most common translations to my native language of [word]?",
            "For beginners, what's the easiest way to remember and use [word] correctly?",
            "For advanced learners, what are some nuanced uses of [word] that natives commonly use?",
            "What memory tricks or mnemonics can help learners remember [word]?"
        ]
    };

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
        const current_word = $('#ask-ai-bot-modal').attr('data-word');

        if (category) {
            prompt_select.removeAttr('disabled').empty().append('<option value="">Choose a prompt...</option>');
            prompts[category].forEach((prompt, index) => {
                const formatted_prompt = replaceWordWithQuotes(prompt, current_word);
                prompt_select.append(`<option value="${index}">${formatted_prompt}</option>`);
            });
        } else {
            prompt_select.attr('disabled', true).empty().append('<option value="">First select a category...</option>');
            $('#custom-prompt').val('');
        }
    });

    // Handle prompt selection
    $('#prompt-select').change(function () {
        const category = $('#prompt-category').val();
        const prompt_index = $(this).val();
        const current_word = $('#ask-ai-bot-modal').attr('data-word');

        if (prompt_index !== '') {
            const selected_prompt = replaceWordWithQuotes(prompts[category][prompt_index], current_word);
            $('#custom-prompt').val(selected_prompt);
        } else {
            $('#custom-prompt').val('');
        }
    });
    
    $('#btn-ask-ai-bot').click(async function () {
        const custom_prompt = $('#custom-prompt').val();
        if (custom_prompt) {
            const current_word = $('#ask-ai-bot-modal').attr('data-word');
            const prompt = replaceWordWithQuotes(custom_prompt, current_word);
    
            $('#prompt-form').hide();
            $('#ai-answer').show();
            $('#normal-footer').hide();
            $('#back-footer').show();
            $('#text-ai-answer').html(''); // Clear previous response
    
            let isFirstChunk = true;
            let markdownResponse = '';
    
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
                const converter = new showdown.Converter();
                
                while (true) {
                    const { value, done } = await reader.read();
                    if (done) break;
                    
                    let chunk = decoder.decode(value, { stream: true });
                    
                    if (isFirstChunk) {
                        chunk = chunk.trimStart();
                        isFirstChunk = false;
                    }
                    
                    markdownResponse += chunk;
                    
                    // Render progressively
                    const html = converter.makeHtml(markdownResponse);
                    $('#text-ai-answer').html(html);
                }
            } catch (error) {
                console.error('Error:', error);
                $('#text-ai-answer').html('<p>Failed to get response from AI. Please try again.</p>');
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