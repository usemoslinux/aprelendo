// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function () {
    const doclang = $("html").attr("lang");
    const ebook_id = $("#text").attr("data-idText");
    const book = ePub({ replacements: 'blobUrl' });
    const media_controller = ReaderHelpers.resolveMediaController();
    const text = document.getElementById("text");
    const reader = document.getElementById("readerpage");
    let text_pos = "";
    let current_section = null;
    let navigating = false;
    let closing = false;
    let restoring = false;
    let save_timer = null;
    let save_queue = Promise.resolve(true);
    let pending_review = null;
    const reviewed_words = new Set();
    const reviewed_sections = new Set();
    const viewport = document.getElementById('ebook-viewport');
    const page_status = document.getElementById('ebook-page-status');
    const progress = document.getElementById('ebook-progress');
    const previous_page = document.getElementById('ebook-prev');
    const next_page = document.getElementById('ebook-next');
    const pagination = new EbookPagination(viewport, text, {
        mode: reader.dataset.ebookMode,
        onChange: updateVisibleLocation
    });
    text.dir = ['ar', 'fa', 'he', 'ur'].includes(doclang.split('-')[0]) ? 'rtl' : 'ltr';

    function showReaderMessage(message = '') {
        const element = document.getElementById('ebook-message');
        element.textContent = message;
        element.hidden = !message;
    }

    function clearSelection() {
        const had_selection = Boolean(text.querySelector('.highlighted'));
        TextActionBtns.hide();
        TextHighlighter.removeAll();
        if (had_selection && media_controller && typeof media_controller.resume === 'function') {
            media_controller.resume();
        }
    }

    function updateVisibleLocation(state) {
        if (!current_section || restoring) return;
        clearSelection();
        for (const word of text.querySelectorAll('.reviewing')) {
            if (Array.from(word.getClientRects()).some(rect => pagination.isVisible(rect))) {
                word.classList.add('ebook-visited');
            }
        }
        const maximum = viewport.scrollHeight - viewport.clientHeight;
        const fraction = state.scrolling
            ? (maximum > 0 ? viewport.scrollTop / maximum : 1)
            : (state.pages > state.columns
                ? state.page / (Math.floor((state.pages - 1) / state.columns) * state.columns) : 1);
        page_status.textContent = state.scrolling
            ? `Chapter progress · ${Math.round(fraction * 100)}%`
            : `Chapter · ${state.columns === 2 && state.page + 1 < state.pages ? 'Pages' : 'Page'} ${state.page + 1}`
                + (state.columns === 2 && state.page + 1 < state.pages ? `–${Math.min(state.page + 2, state.pages)}` : '')
                + ` of ${state.pages}`;
        progress.value = String(Math.round(fraction * 1000));
        progress.setAttribute('aria-valuetext', page_status.textContent);
        progress.disabled = false;
        previous_page.disabled = !(current_section.prev && current_section.prev())
            && (state.scrolling ? viewport.scrollTop <= 1 : state.page === 0);
        next_page.disabled = !current_section.next()
            && (state.scrolling ? viewport.scrollTop >= maximum - 1 : state.page + state.columns >= state.pages);
        text_pos = JSON.stringify({ version: 1, href: current_section.href, offset: state.offset });
        clearTimeout(save_timer);
        if (!closing) save_timer = setTimeout(() => queuePositionSave(), 800);
    }

    function getAudioPosition() {
        const audio = document.getElementById('audioplayer');
        if (audio) {
            return typeof AudioController !== 'undefined' && AudioController.isPlaylist()
                ? AudioController.getPlaylistPositionString() : audio.currentTime;
        }
        return document.getElementById('videoplayer') && typeof VideoController !== 'undefined'
            ? VideoController.getCurrentTime() : 0;
    }

    function queuePositionSave() {
        if (!text_pos) return Promise.resolve(true);
        const position = text_pos;
        const audio_position = getAudioPosition();
        save_queue = save_queue.then(() => saveTextAndAudioPos(position, audio_position));
        return save_queue;
    }

    async function turnPage(direction) {
        if (navigating || closing || !current_section) return;
        clearSelection();
        if (pagination.turn(direction)) return;
        const section = direction < 0
            ? (current_section.prev ? current_section.prev() : null) : current_section.next();
        if (section) await display(section.href, { end: direction < 0 });
    }

    /** Normalize mouse/trackpad input and avoid turning many pages in a wheel burst. */
    function bindWheelNavigation() {
        let accumulated = 0;
        let last_event = 0;
        let last_turn = -Infinity;
        const progress_area = progress.closest('.ebook-progress');
        const handleWheel = event => {
            if (event.defaultPrevented || event.ctrlKey || event.metaKey || event.altKey
                || document.querySelector('.modal.show, .offcanvas.show')) return;
            // The explicit scrolling layout keeps native scrolling over the book.
            if (pagination.mode === 'scroll' && event.currentTarget !== progress_area) return;
            event.preventDefault();
            if (navigating || closing || !current_section) return;
            const horizontal = Math.abs(event.deltaX) > Math.abs(event.deltaY);
            let delta = horizontal ? event.deltaX * (pagination.rtl ? -1 : 1) : event.deltaY;
            if (event.deltaMode === 1) delta *= parseFloat(getComputedStyle(text).lineHeight) || 24;
            else if (event.deltaMode === 2) delta *= viewport.clientHeight;
            const now = performance.now();
            if (now - last_event > 180 || Math.sign(delta) !== Math.sign(accumulated)) accumulated = 0;
            last_event = now;
            if (now - last_turn < 320) return;
            accumulated += delta;
            if (Math.abs(accumulated) >= 40) {
                const direction = Math.sign(accumulated);
                accumulated = 0;
                last_turn = now;
                turnPage(direction);
            }
        };
        document.getElementById('text-container').addEventListener('wheel', handleWheel, { passive: false });
        progress_area.addEventListener('wheel', handleWheel, { passive: false });
    }

    window.parent.show_confirmation_dialog = true; // show confirmation dialog on close

    /**
     * Throws error if response.status !== 200
     * ajax/getebook.php returns 404 in case ebook was not found or if user is not allowed
     * to have access to it.
     * @param response
     */
    function fetchStatusHandler(response) {
        if (response.status !== 200) {
            throw new Error(response.statusText);
        }
        return response;
    } 

    /**
     * Ajax call to fetch an ebook. Response has to be converted to arrayBuffer to allow
     * epub.js (book.open function) to process it correctly
     */
    function getEbook(ebook_id, fetchStatusHandler, book) {
        return fetch("/ajax/getebook.php?id=" + ebook_id)
            .then(fetchStatusHandler)
            .then(response => response.arrayBuffer())
            .then(arraybuffer => {
                const book_open_promise = book.open(arraybuffer);
                return book_open_promise;
            })
            .catch((error) => {
                alert(`Oops! ${error.message}`);
                window.location.replace("/texts");
                throw error;
            });
    }

    /**
     * Returns a book section using the API available in the current epub.js build.
     * @param {object} book
     * @param {string|number} item
     * @returns {object|null}
     */
    function getBookSection(book, item) {
        if (typeof item === 'string' && /^\d+$/.test(item)) item = Number(item);
        if (typeof book.section === "function") {
            return book.section(item);
        }

        if (book.sections && typeof book.sections.get === "function") {
            return book.sections.get(item);
        }

        if (book.spine && typeof book.spine.get === "function") {
            return book.spine.get(item);
        }

        return null;
    }

    /**
     * Returns a normalized array of TOC items for the active epub.js build.
     * @param {object|Array} navigation_or_toc
     * @returns {Array}
     */
    function getNavigationToc(navigation_or_toc) {
        if (Array.isArray(navigation_or_toc)) {
            return navigation_or_toc;
        }

        if (navigation_or_toc && Array.isArray(navigation_or_toc.toc)) {
            return navigation_or_toc.toc;
        }

        return [];
    }

    /**
     * Returns a TOC/navigation item for a chapter href across supported epub.js builds.
     * @param {object} book
     * @param {string} chapter_href
     * @returns {object|null}
     */
    function getNavigationItem(book, chapter_href) {
        if (!book.navigation) {
            return null;
        }

        if (book.navigation.toc && typeof book.navigation.toc.get === "function") {
            return book.navigation.toc.get(chapter_href);
        }

        if (typeof book.navigation.get === "function") {
            return book.navigation.get(chapter_href);
        }

        return null;
    }

    /**
     * Returns normalized book metadata across supported epub.js builds.
     * @param {object} book
     * @returns {Promise<object>}
     */
    function getBookMetadata(book) {
        if (book.loaded && book.loaded.metadata) {
            return book.loaded.metadata;
        }

        const metadata = book.packaging && book.packaging.metadata ? book.packaging.metadata : null;

        return Promise.resolve({
            title: metadata ? (metadata.get("title") || "") : "",
            creator: metadata ? (metadata.get("creator") || "") : "",
            publisher: metadata ? (metadata.get("publisher") || "") : "",
            pubdate: metadata ? (metadata.get("pubdate") || metadata.get("date") || "") : ""
        });
    }

    /**
     * Returns a cover URL across supported epub.js builds.
     * @param {object} book
     * @returns {Promise<string|null>}
     */
    function getBookCoverUrl(book) {
        if (typeof book.coverUrl === "function") {
            return book.coverUrl();
        }

        if (book.archive && typeof book.archive.createUrl === "function" && book.cover) {
            return book.archive.createUrl(book.cover);
        }

        return Promise.resolve(book.cover || null);
    }

    /**
     * Saves review state and reading position before closing the ebook reader.
     *
     * @returns {Promise<void>}
     */
    async function handleCloseEbookClick() {
        if (navigating || closing) return;
        closing = true;
        clearTimeout(save_timer);
        pagination.notify();
        TextActionBtns.hide();
        // save word status before closing
        const save_succeeded = await saveWords();
        if (!save_succeeded) {
            closing = false;
            return;
        }

        if (text_pos) {
            const position_saved = await queuePositionSave();
            if (!position_saved) {
                closing = false;
                return;
            }

            // don't show confirmation dialog when closing window
            window.parent.show_confirmation_dialog = false;
            window.location.replace("/texts");
        }
        closing = false;
    }

    /**
     * Destroys the ebook instance when the parent window unloads.
     *
     * @returns {void}
     */
    function handleReaderUnload() {
        pagination.destroy();
        clearTimeout(save_timer);
        book.destroy();
    }

    /**
     * Updates status of all underlined words & phrases
     * @returns {Promise<boolean>}
     */
    async function saveWords() {
        try {
            if (!pending_review) {
                const words = new Map();
                text.querySelectorAll('.reviewing.ebook-visited').forEach(element => {
                    const name = element.textContent.toLowerCase().trim();
                    if (!reviewed_words.has(name)) words.set(name, element);
                });
                if (!words.size) return true;
                const elements = $(Array.from(words.values()));
                pending_review = {
                    words: Array.from(words.keys()),
                    data: ReaderHelpers.buildReviewData(elements, reviewed_sections.has(current_section.href) ? 0 : 1),
                    href: current_section.href,
                    words_saved: false
                };
            }
            // A score-save retry must not advance the words' learning status twice.
            if (!pending_review.words_saved) {
                await ReaderHelpers.postFormJson('/ajax/updatewords.php', {
                    words: JSON.stringify(pending_review.words)
                }, 'Failed to save reviewed words.');
                pending_review.words_saved = true;
            }
            await ReaderHelpers.postFormJson('/ajax/updateuserscore.php', {
                review_data: JSON.stringify(pending_review.data)
            }, 'Failed to save review score.');
            pending_review.words.forEach(word => reviewed_words.add(word));
            reviewed_sections.add(pending_review.href);
            pending_review = null;
            // More pages may have been visited after a failed save and before retrying.
            return saveWords();
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
            return false;
        }
    }

    /**
     * Builds the table of contents UI for the loaded ebook.
     *
     * @returns {Promise<void>}
     */
    async function buildToc() {
        const navigation_or_toc = await book.loaded.navigation;
        const $nav = document.getElementById("toc");
        const docfrag = document.createDocumentFragment();
        const toc = getNavigationToc(navigation_or_toc);

        $nav.classList.add("list-group");

        const makeLink = (label, href, depth = 0) => {
            const a = document.createElement("a");
            a.href = href;
            a.className = "list-group-item list-group-item-action toc-item";

            a.style.setProperty("--depth", depth);
            a.dataset.depth = String(depth);
            a.dataset.chapter_href = normalizeChapterHref(href);
            a.dataset.chapter_path = normalizeChapterHref(href, false);
            a.setAttribute("aria-level", String(depth + 1));

            a.textContent = label;

            a.onclick = function () {
                TextActionBtns.hide();
                const url = a.getAttribute("href");
                bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('navigation')).hide();
                display(url);
                return false;
            };
            return a;
        };

        const addFlatItems = (items, depth = 0) => {
            items.forEach((chapter) => {
                const hasChildren = Array.isArray(chapter.subitems) && chapter.subitems.length > 0;
                docfrag.appendChild(makeLink(chapter.label, chapter.href, depth));
                if (hasChildren) addFlatItems(chapter.subitems, depth + 1);
            });
        };

        addFlatItems(toc, 0);
        $nav.appendChild(docfrag);

        if ($nav.offsetHeight + 60 < window.innerHeight) {
            $nav.classList.add("fixed");
        }

        if (current_section) updateToc(current_section.href);
    }

    /**
     * Populates ebook metadata in the off-canvas sidebar.
     *
     * @returns {Promise<void>}
     */
    async function populateBookMetadata() {
        const meta = await getBookMetadata(book);
        const $title = document.getElementById("title");
        const $book_title = document.getElementById("book-title");
        const $author = document.getElementById("author");
        const $publisher = document.getElementById("publisher");
        const $pubdate = document.getElementById("pubdate");
        const $cover = document.getElementById("cover");

        $title.textContent = (meta.title && meta.title.trim() !== "") ? meta.title : "Untitled";
        $book_title.textContent = (meta.title && meta.title.trim() !== "") ? meta.title : "Untitled";
        $author.textContent = (meta.creator && meta.creator.trim() !== "") ? meta.creator : "Unknown";
        $publisher.textContent = (meta.publisher && meta.publisher.trim() !== "") ? meta.publisher : "Unknown";
        const publication_date = new Date(meta.pubdate);
        $pubdate.textContent = !Number.isNaN(publication_date.getTime()) ? new Intl.DateTimeFormat("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric"
        }).format(publication_date) : "Not available";

        const url = await getBookCoverUrl(book);
        if (url) {
            $cover.src = url;
        }
    }

    /**
     * Toggles the loading state for the reader content area.
     * @param {boolean} is_loading
     * @returns {void}
     */
    function setLoadingState(is_loading) {
        $(".loading-spinner-container").toggleClass("show", is_loading);
        $("#text-container").toggleClass("show", !is_loading).attr('aria-busy', String(is_loading));
    } 

    /**
     * Loads user word data and returns the annotated section HTML.
     * @param {string} clean_html
     * @returns {Promise<string>}
     */
    async function loadAnnotatedHtml(clean_html) {
        return ReaderHelpers.annotateText(clean_html, doclang);
    } 

    /**
     * Renders annotated section HTML into the text container.
     * @param {string} text_html
     * @returns {void}
     */
    function renderSectionContent(text_html) {
        text.innerHTML = text_html;
        TextProcessor.updateAnchorsList();
    } 

    /**
     * Updates navigation state after a section render completes.
     *
     * @param {object} section
     * @param {string|number} item
     * @returns {void}
     */
    function updateSectionNavigation(section, item) {
        current_section = section;
        updateToc(typeof item === 'number' || /^\d+$/.test(item) ? section.href : item);
    }

    /**
     * Renders a section, annotates its content, and updates reader UI state.
     * @param {object} section
     * @param {string|number} item
     * @returns {Promise<void>}
     */
    async function renderSection(section, item, options = {}) {
        const ebook_html = await section.render(book.load.bind(book));
        const $parsed = cleanEbookHTML(ebook_html);
        const text_html = await loadAnnotatedHtml($parsed.html());
        restoring = true;
        renderSectionContent(text_html);
        updateSectionNavigation(section, item);
        await document.fonts.ready;
        pagination.setContent(options.offset || 0);
        if (options.end) pagination.goToEnd();
        else if (!options.offset) scrollToChapterFragment(item);
        restoring = false;
        pagination.notify();
    } 

    /**
     * Displays an ebook section in the reader.
     * @param {string|number} item
     * @returns {Promise<object|undefined>}
     */
    async function display(item, options = {}) {
        if (navigating || closing) return;
        navigating = true;
        clearTimeout(save_timer);
        clearSelection();
        try {
            const section = getBookSection(book, item);
            if (!section) throw new Error('This chapter could not be found. Choose another chapter from Contents.');
            if (current_section && !(await saveWords())) return;
            if (current_section && !(await queuePositionSave())) return;
            showReaderMessage();
            setLoadingState(true);
            await renderSection(section, item, options);
            return section;
        } catch (error) {
            console.error(error);
            showReaderMessage(error.message);
        } finally {
            restoring = false;
            navigating = false;
            setLoadingState(false);
        }
    } 

    /**
     * Normalizes an internal EPUB chapter href so spine and TOC links can be matched reliably.
     * @param {string|number|null} chapter_href
     * @param {boolean} include_fragment
     * @returns {string}
     */
    function normalizeChapterHref(chapter_href, include_fragment = true) {
        if (chapter_href === null || chapter_href === undefined || chapter_href === '') {
            return '';
        }

        if (chapter_href === 1) {
            return '1';
        }

        try {
            const normalized_url = new URL(String(chapter_href), 'https://ebook.local/');
            return include_fragment ? normalized_url.pathname + normalized_url.hash : normalized_url.pathname;
        } catch (error) {
            const href_string = String(chapter_href);
            return include_fragment ? href_string : href_string.split('#')[0];
        }
    } 

    /**
     * Returns a decoded EPUB fragment identifier from a chapter href.
     * @param {string|number|null} chapter_href
     * @returns {string}
     */
    function getChapterFragment(chapter_href) {
        if (chapter_href === null || chapter_href === undefined) {
            return '';
        }

        try {
            return decodeURIComponent(new URL(String(chapter_href), 'https://ebook.local/').hash.slice(1));
        } catch (error) {
            const fragment = String(chapter_href).split('#')[1] || '';
            try {
                return decodeURIComponent(fragment);
            } catch (decode_error) {
                return fragment;
            }
        }
    }

    /**
     * Moves the reader to the requested in-section EPUB fragment, if present.
     * @param {string|number} chapter_href
     * @returns {void}
     */
    function scrollToChapterFragment(chapter_href) {
        const fragment = getChapterFragment(chapter_href);
        const target = fragment ? Array.from(text.querySelectorAll('[id]')).find(element => element.id === fragment) : null;

        if (target) {
            pagination.reveal(target);
            return;
        }

        pagination.restore(0);
    }

    /**
     * Returns the TOC link for the current chapter using normalized href matching.
     * @param {HTMLElement} $nav
     * @param {string|number|null} current_chapter_url
     * @returns {HTMLAnchorElement|null}
     */
    function getCurrentTocLink($nav, current_chapter_url) {
        const toc_links = Array.from($nav.querySelectorAll('a.toc-item'));
        const normalized_href = normalizeChapterHref(current_chapter_url);
        const normalized_path = normalizeChapterHref(current_chapter_url, false);

        if (normalized_href === '1') {
            return toc_links[0] || null;
        }

        return toc_links.find(($link) => $link.dataset.chapter_href === normalized_href)
            || toc_links.find(($link) => $link.dataset.chapter_path === normalized_path)
            || null;
    } 

    /**
     * Wraps raw ebook HTML into a detached container after normalizing line breaks.
     *
     * @param {string} html
     * @returns {JQuery}
     */
    function parseEbookHtml(html) {
        const normalized_html = html.replace(/[\r\n]+/g, " ");
        return $("<div/>").append(normalized_html);
    }

    /**
     * Removes comments and non-content elements from parsed ebook HTML.
     *
     * @param {JQuery} $parsed
     * @returns {void}
     */
    function removeEbookNonContent($parsed) {
        $parsed.contents().filter(function () {
            return this.nodeType === 8;
        }).remove();

        $parsed.find("link[rel='stylesheet']").remove();
        $parsed.find("head, meta, style, title, br").remove();
    }

    /**
     * Removes formatting attributes while preserving fragment target IDs.
     *
     * @param {JQuery} $parsed
     * @returns {void}
     */
    function stripEbookAttributes($parsed) {
        $parsed.find("*").each(function () {
            const tag = this.tagName.toLowerCase();
            if (tag !== "img") {
                $(this).removeAttr("class").removeAttr("style");
                return;
            }

            for (let i = this.attributes.length - 1; i >= 0; i--) {
                const attr_name = this.attributes[i].name;
                if (attr_name !== "src" && attr_name !== "alt") {
                    $(this).removeAttr(attr_name);
                }
            }
        });
    }

    /**
     * Serializes a parsed ebook node into simplified HTML/text.
     *
     * @param {Node} node
     * @param {string[]} block_elements
     * @returns {string}
     */
    function serializeEbookNode(node, block_elements) {
        if (node.nodeType === Node.TEXT_NODE) {
            return node.nodeValue;
        }

        if (node.nodeType !== Node.ELEMENT_NODE) {
            return "";
        }

        const tag = node.tagName.toLowerCase();
        if (tag === "img") {
            return node.outerHTML;
        }

        let content = "";
        $(node).contents().each(function () {
            content += serializeEbookNode(this, block_elements);
        });

        if (node.id) {
            const $target = $("<span/>").attr("id", node.id).append(content);
            content = $target.prop("outerHTML");
        }

        if (block_elements.includes(tag)) {
            return "\n" + content + "\n";
        }

        return content;
    }

    /**
     * Converts parsed ebook HTML into a simplified container ready for annotation.
     *
     * @param {JQuery} $parsed
     * @returns {JQuery}
     */
    function buildCleanEbookContainer($parsed) {
        const block_elements = ["div", "blockquote", "p", "h1", "h2", "h3", "h4", "h5", "h6"];
        let result = serializeEbookNode($parsed[0], block_elements);

        result = result.replace(/\n\s*\n/g, "\n\n").trim();

        return $("<div/>").append(result);
    }

    /**
     * Removes most ebook formatting while preserving the content needed for word annotation.
     *
     * @param {string} html
     * @returns {JQuery}
     */
    function cleanEbookHTML(html) {
        const $parsed = parseEbookHtml(html);

        removeEbookNonContent($parsed);
        stripEbookAttributes($parsed);

        return buildCleanEbookContainer($parsed);
    }

    /**
     * Highlights the current TOC item and updates the chapter title in the header.
     *
     * @param {string|number|null} current_chapter_url
     * @returns {void}
     */
    function updateToc(current_chapter_url) {
        let $nav = document.getElementById('toc');
        let $active_items_in_nav = $nav.querySelectorAll('.toc-item.bg-primary');
        let $title = document.getElementById('book-title-chapter');
        let $selector = getCurrentTocLink($nav, current_chapter_url);
        let current_nav = getNavigationItem(book, current_chapter_url);

        $active_items_in_nav.forEach(($item) => {
            $item.classList.remove('bg-primary', 'text-light');
            $item.removeAttribute('aria-current');
        });

        // Apply classes and update the title if the link was found
        if ($selector) {
            $selector.classList.add('bg-primary', 'text-light');
            $selector.setAttribute('aria-current', 'location');
        }

        if (!current_nav && $selector) {
            current_nav = getNavigationItem(book, $selector.getAttribute('href'));
        }

        if (current_nav && current_nav.label) {
            $title.textContent = current_nav.label;
        } else if ($selector) {
            $title.textContent = $selector.textContent;
        } else {
            $title.textContent = '';
        }
    } 

    /**
     * Restores the saved ebook section and media playback position.
     *
     * @returns {Promise<void>}
     */
    async function setTextAndAudioPos() {
        // retrieve ebook & audio last reading position
        try {
            const data = await ReaderHelpers.postFormJson("/ajax/ebookposition.php", {
                mode: "GET",
                id: ebook_id
            }, "Failed to get text and audio position.");

            const saved_text_pos = data.payload.text_pos;
            const audio_pos = data.payload.audio_pos;
            const audio_pos_number = parseFloat(audio_pos);
            const audio = document.getElementById("audioplayer");
            const video = document.getElementById("videoplayer");

            // load text position, if available
            if (saved_text_pos) {
                let location = null;
                try { location = JSON.parse(saved_text_pos); } catch (error) { /* Legacy chapter href. */ }
                if (location && location.version === 1 && typeof location.href === 'string') {
                    const section = await display(location.href, { offset: location.offset });
                    if (!section) await display(0);
                } else {
                    await display(saved_text_pos);
                }
            } else {
                await display(0);
            }

            // load audio position, if available
            if (audio != null) {
                if (audio_pos && audio_pos.includes('|') && typeof AudioController !== "undefined") {
                    AudioController.setPlaylistPositionFromString(audio_pos);
                } else if (!isNaN(audio_pos_number)) {
                    audio.currentTime = audio_pos_number;
                } else {
                    audio.currentTime = 0;
                }
            } else if (video != null) {
                initializeVideoPlayer(audio_pos_number);
            }
        } catch (error) {
            console.error(error);
            await display(0);
            const audio = document.getElementById("audioplayer");
            if (audio != null) {
                audio.currentTime = 0;
            }
        }
    } 

    /**
     * Persists the current ebook section and media playback position.
     *
     * @param {string|number} text_pos
     * @param {string|number} audio_pos
     * @returns {Promise<boolean>}
     */
    async function saveTextAndAudioPos(text_pos, audio_pos) {
        try {
            await ReaderHelpers.postFormJson("/ajax/ebookposition.php", {
                mode: "SAVE",
                id: ebook_id,
                audio_pos: audio_pos,
                text_pos: text_pos
            }, "Failed to save text and audio position.");
            showReaderMessage();
            return true;
        } catch (error) {
            console.error(error);
            showReaderMessage('Reading position could not be saved. It will retry on your next page turn.');
            return false;
        }
    }

    /**
     * Initializes ebook startup flow and binds page-level events.
     *
     * @returns {Promise<void>}
     */
    async function initializeEbookReader() {
        ReaderHelpers.initializeReaderActions({
            action_btns: TextActionBtns,
            controller: media_controller,
            source: "text"
        });

        ReaderHelpers.bindWordActionButtons({
            doclang: doclang,
            action_btns: TextActionBtns,
            controller: media_controller,
            get_source_id: function () {
                return ebook_id;
            },
            text_is_shared: function () {
                return new URLSearchParams(window.location.search).get("sh");
            },
            sentence_with_context: true,
            get_word_anchors: function () {
                return TextProcessor.getTextContainer().find("a.word");
            }
        });

        ReaderHelpers.bindBeforeUnloadWarning(function () {
            return window.parent.show_confirmation_dialog;
        });

        $("body").on("click", "#btn-close-ebook", handleCloseEbookClick);
        parent.window.addEventListener("unload", handleReaderUnload);
        previous_page.addEventListener('click', () => turnPage(-1));
        next_page.addEventListener('click', () => turnPage(1));
        bindWheelNavigation();
        progress.addEventListener('input', () => {
            if (!navigating && !closing) pagination.progress(Number(progress.value) / 1000);
        });
        document.addEventListener('keydown', event => {
            if (event.defaultPrevented || event.altKey || event.ctrlKey || event.metaKey || event.shiftKey
                || event.target.closest('input, textarea, select, button, a, [contenteditable="true"]')
                || document.querySelector('.modal.show, .offcanvas.show')
                || text.querySelector('.highlighted') || window.getSelection()?.toString()) return;
            const direction = { ArrowLeft: pagination.rtl ? 1 : -1, ArrowRight: pagination.rtl ? -1 : 1,
                PageUp: -1, PageDown: 1 }[event.key];
            if (direction) {
                event.preventDefault();
                turnPage(direction);
            }
        });
        document.addEventListener('reader-preferences-changed', event => {
            clearSelection();
            pagination.setMode(event.detail.ebook_mode || pagination.mode);
        });
        document.getElementById('navigation').addEventListener('shown.bs.offcanvas', () => {
            document.querySelector('#toc [aria-current]')?.scrollIntoView({ block: 'nearest' });
        });
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden' && !navigating && !closing) {
                clearTimeout(save_timer);
                queuePositionSave();
            }
        });

        await getEbook(ebook_id, fetchStatusHandler, book);

        await Promise.all([buildToc(), populateBookMetadata()]);
        await setTextAndAudioPos();
    }

    initializeEbookReader().catch((error) => {
        console.error(error);
    });
});
