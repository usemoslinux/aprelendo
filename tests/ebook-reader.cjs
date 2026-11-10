// SPDX-License-Identifier: GPL-3.0-or-later
// NODE_PATH=/path/to/test/node_modules node tests/ebook-reader.cjs
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { chromium, firefox, webkit } = require('playwright');
const JSZip = require('jszip');
const root = path.resolve(__dirname, '..');
const asset = name => path.join(root, 'src/public', name);

const main = fs.readFileSync(asset('showebook.php'), 'utf8').match(/<main\b[\s\S]*?<\/main>/)[0].replace(/<\?php[\s\S]*?\?>/g, '');
const shell = `<!doctype html><html lang="en"><head><meta name="viewport" content="width=device-width, initial-scale=1"></head><body id="readerpage" class="ebook-reader lightmode" data-ebook-mode="auto">
${main}<div id="navigation"><div id="toc"></div></div>
<span id="title"></span><span id="author"></span><span id="publisher"></span><span id="pubdate"></span><img id="cover">
<audio id="audioplayer"></audio><div id="action-buttons" class="invisible" style="width:220px;height:42px"></div></body></html>`;

async function setup(page) {
    await page.route('http://reader.test/', route => route.fulfill({ contentType: 'text/html', body: shell }));
    await page.goto('http://reader.test/');
    await page.addStyleTag({ path: require.resolve('bootstrap/dist/css/bootstrap.min.css') });
    await page.addStyleTag({ path: asset('css/styles.css') });
    await page.addStyleTag({ path: asset('css/showebook.css') });
    await page.addScriptTag({ path: path.join(path.dirname(require.resolve('jquery')), 'jquery.slim.js') });
    await page.addScriptTag({ path: asset('js/ebookpagination.js') });
    await page.evaluate(() => {
        document.getElementById('text-container').style.cssText = 'font:20px/1.5 Georgia';
        document.getElementById('text-container').classList.add('show');
        document.getElementById('text').dataset.idtext = '1';
    });
}

async function paginationChecks(page) {
    await setup(page);
    await page.setViewportSize({ width: 1920, height: 900 });
    assert.equal(await page.locator('#main').evaluate(element => element.getBoundingClientRect().width), 1920,
        'reader shell fills wide screens without a maximum width');
    for (const theme of ['lightmode', 'sepiamode', 'darkmode']) {
        const background = await page.evaluate(theme => {
            document.body.className = `ebook-reader ${theme}`;
            return getComputedStyle(document.body).backgroundImage;
        }, theme);
        assert.equal(background, 'none', 'ebook reader has no wallpaper or background image');
    }
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.evaluate(() => {
        const text = document.getElementById('text');
        text.innerHTML = Array.from({ length: 1800 }, (_, i) => `<a class="word" data-index="${i}">passage${i}</a> `).join('');
        window.paginator = new EbookPagination(document.getElementById('ebook-viewport'), text);
        paginator.setContent();
    });
    assert.equal(await page.evaluate(() => paginator.columns), 2, 'wide reader uses two pages');
    const coverage = await page.evaluate(() => {
        const counts = new Map();
        let turns = 0;
        do {
            for (const word of document.querySelectorAll('#text .word')) {
                if (Array.from(word.getClientRects()).some(rect => paginator.isVisible(rect))) {
                    counts.set(word.dataset.index, (counts.get(word.dataset.index) || 0) + 1);
                }
            }
            if (++turns > 100) throw new Error('Pagination did not terminate');
        } while (paginator.turn(1));
        return { size: counts.size, duplicates: [...counts.values()].filter(n => n !== 1).length };
    });
    assert.deepEqual(coverage, { size: 1800, duplicates: 0 }, 'every word appears on exactly one spread');

    await page.evaluate(() => {
        paginator.goToPage(4);
        window.savedOffset = paginator.offset;
        const entry = paginator.textNodes().find(entry => entry.offset === savedOffset);
        window.savedWord = entry.node.parentElement;
    });
    await page.setViewportSize({ width: 390, height: 844 });
    await page.waitForFunction(() => paginator.columns === 1 && paginator.width < 390);
    assert.ok(await page.evaluate(() => Array.from(savedWord.getClientRects()).some(rect => paginator.isVisible(rect))),
        'resize keeps the previous passage visible');
    await page.evaluate(() => {
        savedOffset = paginator.offset;
        savedWord = paginator.textNodes().find(entry => entry.offset === savedOffset).node.parentElement;
        document.getElementById('text-container').style.fontSize = '28px';
        paginator.layout();
    });
    assert.ok(await page.evaluate(() => Array.from(savedWord.getClientRects()).some(rect => paginator.isVisible(rect))),
        'font change keeps the passage visible');
    await page.evaluate(() => {
        paginator.setMode('scroll');
        paginator.restore(savedOffset);
    });
    assert.ok(await page.evaluate(() => Array.from(savedWord.getClientRects()).some(rect => paginator.isVisible(rect))),
        'scroll mode restores the passage');

    await page.setViewportSize({ width: 1440, height: 900 });
    await page.evaluate(() => {
        document.getElementById('text-container').style.fontSize = '20px';
        paginator.text.dir = 'rtl';
        paginator.setMode('auto');
        paginator.restore(0);
        paginator.turn(1);
    });
    assert.ok(await page.evaluate(() => paginator.offset > 0), 'RTL page turns advance the text');
    await page.evaluate(() => {
        paginator.text.dir = 'ltr';
        paginator.text.innerHTML = '<a class="word">Short chapter.</a>';
        paginator.setContent();
    });
    assert.equal(await page.evaluate(() => paginator.pages), 1, 'short chapter has no phantom page');
    assert.equal(await page.evaluate(() => paginator.turn(1)), false, 'short chapter ends immediately');
    await page.evaluate(() => paginator.destroy());
}

async function mobileLayoutChecks(page) {
    await page.setViewportSize({ width: 412, height: 915 });
    await setup(page);
    await page.evaluate(() => {
        document.getElementById('book-title').textContent = 'A very long book title that must fit in a narrow reader';
        document.getElementById('book-title-chapter').textContent = 'A long chapter heading that should truncate instead of widening the reader';
        const text = document.getElementById('text');
        text.innerHTML = '<a class="word">A readable passage with words and punctuation.</a> '.repeat(1500);
        window.paginator = new EbookPagination(document.getElementById('ebook-viewport'), text);
        paginator.setContent();
    });
    for (const size of [{ width: 412, height: 915 }, { width: 320, height: 568 }, { width: 915, height: 412 }]) {
        await page.setViewportSize(size);
        await page.waitForTimeout(100);
        const bounds = await page.evaluate(() => Object.fromEntries(
            ['#main', '#header', '#text-container', '#ebook-viewport', '.ebook-footer', '#ebook-progress', '#ebook-next']
                .map(selector => {
                    const rect = document.querySelector(selector).getBoundingClientRect();
                    return [selector, { left: rect.left, right: rect.right, top: rect.top, bottom: rect.bottom }];
                })
        ));
        for (const [selector, rect] of Object.entries(bounds)) {
            assert.ok(rect.left >= -1 && rect.right <= size.width + 1, `${selector} fits ${size.width}px viewport: ${JSON.stringify(rect)}`);
            assert.ok(rect.top >= -1 && rect.bottom <= size.height + 1, `${selector} fits ${size.height}px height: ${JSON.stringify(rect)}`);
        }
        assert.ok(Math.abs(bounds['.ebook-footer'].bottom - size.height) < 2, 'footer sits at the bottom of the screen');
        assert.equal(await page.evaluate(() => paginator.columns), 1, 'small screens use one page');
        if (size.width <= 600) {
            const align = await page.evaluate(() => getComputedStyle(document.querySelector('.ebook-heading')).textAlign);
            assert.equal(align, 'left', 'header title is left-aligned on mobile');
        }
    }
    await page.evaluate(() => paginator.destroy());
}

async function makeBook() {
    const zip = new JSZip();
    zip.file('mimetype', 'application/epub+zip');
    zip.file('META-INF/container.xml', `<?xml version="1.0"?><container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container"><rootfiles><rootfile full-path="OPS/package.opf" media-type="application/oebps-package+xml"/></rootfiles></container>`);
    zip.file('OPS/package.opf', `<?xml version="1.0"?><package xmlns="http://www.idpf.org/2007/opf" version="3.0" unique-identifier="id"><metadata xmlns:dc="http://purl.org/dc/elements/1.1/"><dc:identifier id="id">reader-test</dc:identifier><dc:title>Reader fixture</dc:title><dc:language>en</dc:language><dc:creator>Test Author</dc:creator></metadata><manifest><item id="nav" href="nav.xhtml" properties="nav" media-type="application/xhtml+xml"/><item id="first" href="first.xhtml" media-type="application/xhtml+xml"/><item id="second" href="second.xhtml" media-type="application/xhtml+xml"/><item id="image" href="image.svg" media-type="image/svg+xml"/></manifest><spine><itemref idref="first"/><itemref idref="second"/></spine></package>`);
    zip.file('OPS/nav.xhtml', `<html xmlns="http://www.w3.org/1999/xhtml" xmlns:epub="http://www.idpf.org/2007/ops"><head><title>Contents</title></head><body><nav epub:type="toc"><ol><li><a href="first.xhtml">First chapter</a><ol><li><a href="first.xhtml#ending">Ending</a></li></ol></li><li><a href="second.xhtml">Second chapter</a></li></ol></nav></body></html>`);
    zip.file('OPS/first.xhtml', `<html xmlns="http://www.w3.org/1999/xhtml"><head><title>First chapter</title></head><body><p>firstword ${'reading a book is enjoyable. '.repeat(500)}</p><p id="ending">hiddenword Last passage.</p></body></html>`);
    zip.file('OPS/second.xhtml', `<html xmlns="http://www.w3.org/1999/xhtml"><head><title>Second chapter</title></head><body><p>Second chapter.</p><img src="image.svg" alt="Illustration"/></body></html>`);
    zip.file('OPS/image.svg', '<svg xmlns="http://www.w3.org/2000/svg" width="2000" height="3000"><rect width="2000" height="3000" fill="teal"/></svg>');
    return zip.generateAsync({ type: 'nodebuffer' });
}

async function integrationChecks(page, epub) {
    await setup(page);
    const requests = [];
    let saved_position = null;
    let fail_score = false;
    await page.route('**/ajax/**', async route => {
        const request = route.request();
        const url = new URL(request.url());
        const data = Object.fromEntries(new URLSearchParams(request.postData() || ''));
        requests.push({ endpoint: url.pathname, data });
        if (url.pathname.endsWith('/getebook.php')) {
            return route.fulfill({ status: 200, body: epub, contentType: 'application/epub+zip', headers: { 'Access-Control-Allow-Origin': '*' } });
        }
        let payload = {};
        if (url.pathname.endsWith('/ebookposition.php')) {
            if (data.mode === 'SAVE') saved_position = data.text_pos;
            payload = { text_pos: saved_position, audio_pos: '2|42' };
        } else if (url.pathname.endsWith('/getuserwords.php')) {
            payload = { text: data.txt, user_words: [
                { word: 'firstword', status: 2, is_phrase: 0 },
                { word: 'hiddenword', status: 1, is_phrase: 0 }
            ] };
        } else if (url.pathname.endsWith('/updateuserscore.php') && fail_score) {
            fail_score = false;
            return route.fulfill({ json: { success: false, error_msg: 'Intentional score failure' }, headers: { 'Access-Control-Allow-Origin': '*' } });
        }
        return route.fulfill({ json: { success: true, payload }, headers: { 'Access-Control-Allow-Origin': '*' } });
    });
    // Give relative fetches a real origin without requiring PHP or a database.
    await page.evaluate(() => {
        const nativeFetch = window.fetch;
        window.fetch = (url, options) => nativeFetch(new URL(url, 'http://reader.test/'), options);
        window.alerts = [];
        window.alert = message => alerts.push(message);
        window.Dictionaries = { fetchURIs() {}, getURIs() { return {}; } };
        window.LinkBuilder = { forTranslationInText() { return '#'; }, forWordInDictionary() { return '#'; } };
        window.AudioController = {
            pause() { window.audioPaused = true; }, resume() { window.audioPaused = false; },
            isPlaylist() { return true; }, getPlaylistPositionString() { return '2|42'; },
            setPlaylistPositionFromString(value) { window.restoredAudio = value; }
        };
        window.bootstrap = { Offcanvas: { getOrCreateInstance() { return { hide() {} }; } } };
    });
    await page.addScriptTag({ path: require.resolve('jszip/dist/jszip.min.js') });
    await page.addScriptTag({ path: require.resolve('@intity/epub-js/dist/epub.min.js') });
    await page.addScriptTag({ path: asset('js/underlinewords.js') });
    await page.addScriptTag({ path: asset('js/readerhelpers.js') });
    await page.addScriptTag({ path: asset('js/actionbtns.js') });
    await page.addScriptTag({ path: asset('js/wordselection.js') });
    await page.addScriptTag({ path: asset('js/showebook.js') });
    await page.waitForFunction(() => document.getElementById('ebook-page-status').textContent.includes('of'));
    assert.equal(await page.locator('#book-title').textContent(), 'Reader fixture');
    assert.equal(await page.locator('#toc a').count(), 3, 'nested contents loaded from real EPUB');
    assert.equal(await page.locator('#text .ebook-visited').count(), 1, 'only visible review word is eligible');
    assert.equal(requests.filter(item => item.endpoint.endsWith('/updatewords.php')).length, 0, 'opening does not award reviews');
    assert.equal(await page.evaluate(() => restoredAudio), '2|42', 'playlist position restored');
    const start_status = await page.locator('#ebook-page-status').textContent();
    await page.locator('#ebook-viewport').hover();
    await page.mouse.wheel(0, 100);
    await page.waitForFunction(start => document.getElementById('ebook-page-status').textContent !== start, start_status);
    const next_status = await page.locator('#ebook-page-status').textContent();
    await page.evaluate(() => {
        for (let i = 0; i < 10; i++) {
            document.getElementById('ebook-viewport').dispatchEvent(new WheelEvent('wheel', { deltaY: 100, bubbles: true, cancelable: true }));
        }
    });
    assert.equal(await page.locator('#ebook-page-status').textContent(), next_status, 'wheel burst turns only one spread');
    await page.waitForTimeout(350);
    await page.locator('#ebook-progress').hover();
    await page.mouse.wheel(0, -100);
    await page.waitForFunction(start => document.getElementById('ebook-page-status').textContent === start, start_status);
    await page.waitForTimeout(350);
    await page.mouse.wheel(0, 100);
    await page.waitForFunction(next => document.getElementById('ebook-page-status').textContent === next, next_status);
    await page.waitForTimeout(350);
    await page.locator('#ebook-viewport').hover();
    await page.mouse.wheel(0, -100);
    await page.waitForFunction(start => document.getElementById('ebook-page-status').textContent === start, start_status);
    await page.locator('#text .ebook-visited').click();
    assert.ok(await page.evaluate(() => audioPaused), 'word selection pauses audio');
    assert.equal(await page.locator('#text .highlighted').count(), 1, 'word selection works inside the spread');
    await page.locator('#ebook-next').click();
    assert.equal(await page.locator('#text .highlighted').count(), 0, 'page turn clears stale word selection');
    assert.equal(await page.evaluate(() => audioPaused), false, 'page turn resumes selection-paused audio');
    await page.locator('#ebook-prev').click();

    await page.evaluate(() => {
        const word = Array.from(document.querySelectorAll('#text .word')).find(element => {
            const rect = element.getBoundingClientRect();
            return rect.left > innerWidth / 2 && rect.top < 150;
        });
        ActionBtns.show($(word));
    });
    const popup = await page.locator('#action-buttons').boundingBox();
    assert.ok(popup.x >= 0 && popup.x + popup.width <= 1440 && popup.y >= 0, 'right-page popup stays onscreen');
    assert.equal(await page.evaluate(() => document.getElementById('ebook-viewport').style.overflowY), '', 'scrolling is not disabled while action menu is visible');

    fail_score = true;
    await page.locator('#toc a').last().click();
    await page.waitForFunction(() => alerts.length === 1);
    assert.equal(await page.locator('#book-title-chapter').textContent(), 'First chapter', 'failed save keeps current chapter');
    await page.locator('#toc a').last().click();
    await page.waitForFunction(() => document.getElementById('book-title-chapter').textContent === 'Second chapter');
    assert.equal(requests.filter(item => item.endpoint.endsWith('/updatewords.php')).length, 1, 'score retry does not advance word status twice');
    const word_save = requests.find(item => item.endpoint.endsWith('/updatewords.php'));
    assert.deepEqual(JSON.parse(word_save.data.words), ['firstword'], 'hidden word excluded from review');
    const score = JSON.parse(requests.find(item => item.endpoint.endsWith('/updateuserscore.php')).data.review_data);
    assert.deepEqual(score.words, { new: 1, learning: 0, forgotten: 0 }, 'score uses visited words only');
    await page.waitForFunction(() => document.querySelector('#text img')?.naturalWidth > 0);
    const image = await page.locator('#text img').boundingBox();
    const viewport = await page.locator('#ebook-viewport').boundingBox();
    assert.ok(image.height <= viewport.height && image.width <= viewport.width, 'large EPUB image fits page');

    await page.locator('#ebook-prev').click();
    await page.waitForFunction(() => document.getElementById('book-title-chapter').textContent === 'First chapter');
    await page.waitForFunction(() => document.querySelector('#text .ebook-visited.learning'));
    assert.ok(!(await page.locator('#ebook-page-status').textContent()).includes('Pages 1–2'), 'previous chapter opens at its last spread');
    await page.locator('#toc a').nth(1).click();
    await page.waitForFunction(() => document.getElementById('book-title-chapter').textContent === 'Ending');
    await page.waitForTimeout(1000);
    const location = JSON.parse(saved_position);
    assert.equal(location.version, 1);
    assert.equal(location.href, 'first.xhtml');
    assert.ok(location.offset > 0, 'fragment navigation persists an in-chapter offset');
    assert.equal(requests.filter(item => item.endpoint.endsWith('/ebookposition.php') && item.data.mode === 'SAVE').at(-1).data.audio_pos,
        '2|42', 'playlist position saved alongside text');
    const new_saves = requests.filter(item => item.endpoint.endsWith('/updatewords.php'));
    assert.equal(new_saves.filter(item => JSON.parse(item.data.words).includes('firstword')).length, 1,
        'revisiting chapter does not review the same word twice in the session');
}

(async () => {
    const epub = await makeBook();
    const engines = (process.env.BROWSERS || 'chromium,firefox,webkit').split(',');
    for (const engine of [chromium, firefox, webkit].filter(engine => engines.includes(engine.name()))) {
        const browser = await engine.launch();
        try {
            const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
            const errors = [];
            page.on('pageerror', error => errors.push(error.message));
            await paginationChecks(page);
            await page.close();
            const mobile = await browser.newPage({
                viewport: { width: 412, height: 915 }, deviceScaleFactor: 2.625,
                isMobile: engine.name() !== 'firefox', hasTouch: true
            });
            mobile.on('pageerror', error => errors.push(error.message));
            await mobileLayoutChecks(mobile);
            await mobile.close();
            const integration = await browser.newPage({ viewport: { width: 1440, height: 900 } });
            integration.on('pageerror', error => errors.push(error.message));
            await integrationChecks(integration, epub);
            assert.deepEqual(errors, [], 'no uncaught browser errors');
            console.log(`${engine.name()}: mobile sizing, wheel navigation, pagination, reflow, RTL, EPUB navigation, images, review retries and persistence passed`);
        } finally {
            await browser.close();
        }
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
