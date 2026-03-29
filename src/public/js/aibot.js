// SPDX-License-Identifier: GPL-3.0-or-later

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

            if (!response.ok) { throw new Error('Failed to get AI response.'); }

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
            console.error(error);
            if (typeof onError === 'function') {
                onError(error);
            }
        }
    }

    return {
        streamReply
    };
})();
