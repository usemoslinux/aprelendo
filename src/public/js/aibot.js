// SPDX-License-Identifier: GPL-3.0-or-later

const AIBot = (() => {
    async function streamReply(prompt, {
        onUpdate,   // called every time new markdown is available
        onDone,     // called once at the end, with full markdown
        onError     // called if something goes wrong
    } = {}) {
        let markdownResponse = '';
        let buffer = '';
        let finished = false;
        let reader;
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 75000);

        function processLine(line) {
            if (!line.trim()) return;
            const event = JSON.parse(line);
            if (event.type === 'error') {
                throw new Error(event.message || 'Failed to get response from AI. Please try again.');
            }
            if (event.type === 'done') {
                finished = true;
            } else if (event.type === 'delta' && typeof event.content === 'string') {
                markdownResponse += event.content;
                if (typeof onUpdate === 'function') onUpdate(markdownResponse);
            }
        }

        try {
            const response = await fetch('/ajax/getaireply.php', {
                method: 'POST',
                signal: controller.signal,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `prompt=${encodeURIComponent(prompt)}`
            });

            if (!response.ok) { throw new Error('Failed to get AI response.'); }

            reader = response.body.getReader();
            const decoder = new TextDecoder();

            while (true) {
                const { value, done } = await reader.read();
                buffer += done ? decoder.decode() : decoder.decode(value, { stream: true });
                let newline;
                while ((newline = buffer.indexOf('\n')) !== -1) {
                    processLine(buffer.slice(0, newline));
                    buffer = buffer.slice(newline + 1);
                }
                if (done) break;
            }
            if (buffer.trim()) processLine(buffer);
            if (!markdownResponse.trim()) throw new Error('Lingobot returned no answer. Please try again.');
            if (!finished) throw new Error('The AI response was interrupted. Please try again.');

            if (typeof onDone === 'function') {
                onDone(markdownResponse);
            }
        } catch (error) {
            controller.abort();
            if (error.name === 'AbortError') {
                error = new Error('Lingobot timed out. Please try again.');
            } else if (error instanceof SyntaxError) {
                error = new Error('Received an invalid AI response. Please reload the page and try again.');
            }
            console.error(error);
            if (typeof onError === 'function') {
                onError(error);
            }
        } finally {
            clearTimeout(timeout);
            if (reader) reader.releaseLock();
        }
    }

    return {
        streamReply
    };
})();
