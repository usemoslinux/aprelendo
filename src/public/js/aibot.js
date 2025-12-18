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

const AIBot = (() => {
    async function streamReply(prompt, {
        onUpdate,   // called every time new markdown is available
        onDone,     // called once at the end, with full markdown
        onError     // called if something goes wrong
    } = {}) {
        let isFirstChunk = true;
        let markdownResponse = '';

        try {
            const response = await fetch('/ajax/getaireply.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
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

                let chunk = decoder.decode(value, { stream: true });

                if (isFirstChunk) {
                    chunk = chunk.trimStart();
                    isFirstChunk = false;
                }

                markdownResponse += chunk;

                if (typeof onUpdate === 'function') {
                    onUpdate(markdownResponse);
                }
            }

            if (typeof onDone === 'function') {
                onDone(markdownResponse);
            }
        } catch (error) {
            console.error('Error:', error);
            if (typeof onError === 'function') {
                onError(error);
            }
        }
    }

    return {
        streamReply
    };
})();
