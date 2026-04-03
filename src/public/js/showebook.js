// SPDX-License-Identifier: GPL-3.0-or-later

$(document).ready(function () {
    const doclang = $("html").attr("lang");
    const ebook_id = $("#text").attr("data-idText");
    const book = ePub();
    const media_controller = ReaderHelpers.resolveMediaController();
    const text = document.getElementById("text");
    const reader = document.getElementById("readerpage");
    const next = document.getElementById("next");
    let text_pos = "";
    let has_unsaved_reviews = false;

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
    function getEbook(ebook_id, fetchStatusHandler, book, reader) {
        return fetch("/ajax/getebook.php?id=" + ebook_id)
            .then(fetchStatusHandler)
            .then(response => response.arrayBuffer())
            .then(arraybuffer => {
                const book_open_promise = book.open(arraybuffer);
                const rendition = book.renderTo("text", {
                    flow: "scrolled-doc"
                });

                setupRendition(rendition, reader);

                return book_open_promise;
            })
            .catch((error) => {
                alert(`Oops! ${error.message}`);
                window.location.replace("/texts");
                throw error;
            });
    }

    /**
     * Returns the promise that resolves to the book sections collection for the active epub.js build.
     * @param {object} book
     * @returns {Promise<object>|undefined}
     */
    function getLoadedSections(book) {
        if (!book.loaded) {
            return undefined;
        }

        return book.loaded.sections || book.loaded.spine;
    }

    /**
     * Preloads section documents so later manual section rendering stays responsive.
     * @param {object} book
     * @returns {void}
     */
    function preloadBookSections(book) {
        const loaded_sections = getLoadedSections(book);

        if (!loaded_sections || typeof loaded_sections.then !== "function") {
            return;
        }

        loaded_sections.then((sections) => {
            if (!sections) {
                return;
            }

            if (typeof sections.each === "function") {
                sections.each((section) => {
                    section.load(book.load.bind(book));
                });
                return;
            }

            if (typeof sections.forEach === "function") {
                sections.forEach((section) => {
                    section.load(book.load.bind(book));
                });
            }
        });
    }

    /**
     * Returns a book section using the API available in the current epub.js build.
     * @param {object} book
     * @param {string|number} item
     * @returns {object|null}
     */
    function getBookSection(book, item) {
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
     * Applies reader typography defaults using the theming API available in the active epub.js build.
     * @param {object} rendition
     * @param {HTMLElement} reader
     * @returns {void}
     */
    function applyReaderThemeDefaults(rendition, reader) {
        const body_styles = {
            "font-family": reader.style.fontFamily + " !important",
            "font-size": reader.style.fontSize + " !important",
            "text-align": reader.style.textAlign + " !important",
            "line-height": reader.style.lineHeight + " !important",
            "padding": "0 5% !important"
        };

        if (typeof rendition.themes.default === "function") {
            rendition.themes.default({
                body: body_styles
            });
            return;
        }

        if (typeof rendition.themes.font === "function" && reader.style.fontFamily) {
            rendition.themes.font(reader.style.fontFamily);
        }

        if (typeof rendition.themes.fontSize === "function" && reader.style.fontSize) {
            rendition.themes.fontSize(reader.style.fontSize);
        }

        if (typeof rendition.themes.appendRule === "function") {
            if (reader.style.textAlign) {
                rendition.themes.appendRule("text-align", reader.style.textAlign, true);
            }

            if (reader.style.lineHeight) {
                rendition.themes.appendRule("line-height", reader.style.lineHeight, true);
            }

            rendition.themes.appendRule("padding", "0 5%", true);
        }
    }

    /**
     * Returns the active reader theme class expected by epub.js.
     * @param {HTMLElement} reader
     * @returns {string|null}
     */
    function getReaderThemeName(reader) {
        const theme_names = ["darkmode", "lightmode", "sepiamode"];

        for (const theme_name of theme_names) {
            if (reader.classList.contains(theme_name)) {
                return theme_name;
            }
        }

        return null;
    }

    /**
     * Applies theme registration and reader defaults to a rendition once it exists.
     * @param {object} rendition
     * @param {HTMLElement} reader
     * @returns {void}
     */
    function setupRendition(rendition, reader) {
        rendition.themes.register("darkmode", "/css/showebook.css");
        rendition.themes.register("lightmode", "/css/showebook.css");
        rendition.themes.register("sepiamode", "/css/showebook.css");

        applyReaderThemeDefaults(rendition, reader);

        const reader_theme_name = getReaderThemeName(reader);
        if (reader_theme_name) {
            rendition.themes.select(reader_theme_name);
        }
    }

    /**
     * Handles the next chapter button click.
     *
     * @param {Event} event
     * @returns {Promise<void>}
     */
    async function handleNextChapterClick(event) {
        event.preventDefault();
        disposeTooltip(next);

        const save_succeeded = await saveWords();
        if (!save_succeeded) {
            return;
        }

        const url = next.getAttribute("href");
        await display(url);
    }

    /**
     * Saves review state and reading position before closing the ebook reader.
     *
     * @returns {Promise<void>}
     */
    async function handleCloseEbookClick() {
        // save word status before closing
        const save_succeeded = await saveWords();
        if (!save_succeeded) {
            return;
        }

        // save book position to resume reading from there later
        let audio_pos = 0;
        const audio = document.getElementById("audioplayer");
        const video = document.getElementById("videoplayer");

        if (audio) {
            if (typeof AudioController !== "undefined" && AudioController.isPlaylist()) {
                audio_pos = AudioController.getPlaylistPositionString();
            } else {
                audio_pos = audio.currentTime;
            }
        } else if (video) {
            audio_pos = VideoController.getCurrentTime();
        }

        if (text_pos) {
            const position_saved = await saveTextAndAudioPos(text_pos, audio_pos);
            if (!position_saved) {
                return;
            }

            // don't show confirmation dialog when closing window
            window.parent.show_confirmation_dialog = false;
            window.location.replace("/texts");
        }
    }

    /**
     * Destroys the ebook instance when the parent window unloads.
     *
     * @returns {void}
     */
    function handleReaderUnload() {
        book.destroy();
    }

    /**
     * Updates status of all underlined words & phrases
     * @returns {Promise<boolean>}
     */
    async function saveWords() {
        if (!has_unsaved_reviews) {
            return true;
        }

        if ($("#text").find(".reviewing").length === 0) {
            has_unsaved_reviews = false;
            return true;
        }

        try {
            await ReaderHelpers.saveReviewProgress({
                words_selector: "#text .reviewing"
            });
            has_unsaved_reviews = false;
            return true;
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
                const url = a.getAttribute("href");
                document.getElementById("opener").click();
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
        $pubdate.textContent = (meta.pubdate && meta.pubdate.trim() !== "") ? new Intl.DateTimeFormat("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric"
        }).format(new Date(meta.pubdate)) : "Not available";

        const url = await getBookCoverUrl(book);
        if (url) {
            $cover.src = url;
        }
    }

    /**
     * Resets the next chapter button before rendering a new section.
     * @returns {void}
     */
    function resetNextChapterButton() {
        disposeTooltip(next);
        next.textContent = "";
        next.classList.add('d-none');
        next.href = "#";
    } 

    /**
     * Toggles the loading state for the reader content area.
     * @param {boolean} is_loading
     * @returns {void}
     */
    function setLoadingState(is_loading) {
        $(".loading-spinner-container").toggleClass("show", is_loading);
        $("#text-container").toggleClass("show", !is_loading);
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
        has_unsaved_reviews = $("#text").find(".reviewing").length > 0;
        scrollToPageTop();
    } 

    /**
     * Updates navigation state after a section render completes.
     *
     * @param {object} section
     * @param {string|number} item
     * @returns {void}
     */
    function updateSectionNavigation(section, item) {
        updateNextChapterButton(section);
        text_pos = item;
        updateToc(section.href);
    }

    /**
     * Renders a section, annotates its content, and updates reader UI state.
     * @param {object} section
     * @param {string|number} item
     * @returns {Promise<void>}
     */
    async function renderSection(section, item) {
        const ebook_html = await section.render(book.load.bind(book));
        const $parsed = cleanEbookHTML(ebook_html);

        setLoadingState(true);

        try {
            const text_html = await loadAnnotatedHtml($parsed.html());
            renderSectionContent(text_html);
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
        } finally {
            setLoadingState(false);
        }

        updateSectionNavigation(section, item);
    } 

    /**
     * Displays an ebook section in the reader.
     * @param {string|number} item
     * @returns {Promise<object|undefined>}
     */
    async function display(item) {
        let section = getBookSection(book, item);

        resetNextChapterButton();

        if (section) {
            await renderSection(section, item);
        }

        return section;
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
     * Updates the next chapter button, preferring TOC order so it behaves like clicking a TOC item.
     * @param {object} section
     */
    function updateNextChapterButton(section) {
        let next_href = '';
        let next_label = 'Next chapter';
        const $nav = document.getElementById('toc');
        const current_toc_link = getCurrentTocLink($nav, section.href);
        const next_toc_link = current_toc_link ? current_toc_link.nextElementSibling : null;

        if (next_toc_link && next_toc_link.classList.contains('toc-item')) {
            next_href = next_toc_link.getAttribute('href');
            next_label = next_toc_link.textContent;
        } else {
            const next_section = section.next();

            if (!next_section) {
                return;
            }

            next_href = next_section.href;

            const next_nav = getNavigationItem(book, next_section.href);
            if (next_nav && next_nav.label) {
                next_label = next_nav.label;
            }
        }

        next.textContent = next_label + ' »';
        next.href = next_href;

        if (!isMobileDevice()) {
            setNewTooltip(next, 'Go to next chapter & mark underlined words as reviewed');
        }

        next.classList.remove('d-none');
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
     * Removes formatting attributes while preserving minimal image attributes.
     *
     * @param {JQuery} $parsed
     * @returns {void}
     */
    function stripEbookAttributes($parsed) {
        $parsed.find("*").each(function () {
            const tag = this.tagName.toLowerCase();
            if (tag !== "img") {
                $(this).removeAttr("class").removeAttr("style").removeAttr("id");
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
        });

        // Apply classes and update the title if the link was found
        if ($selector) {
            $selector.classList.add('bg-primary', 'text-light');
        }

        if (!current_nav && $selector) {
            current_nav = getNavigationItem(book, $selector.getAttribute('href'));
        }

        if (current_nav && current_nav.label) {
            $title.textContent = ' - ' + current_nav.label;
        } else if ($selector) {
            $title.textContent = ' - ' + $selector.textContent;
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
                await display(saved_text_pos);
            } else {
                await display(1);
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
            await display(1);
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
            return true;
        } catch (error) {
            console.error(error);
            alert(`Oops! ${error.message}`);
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

        next.addEventListener("click", handleNextChapterClick, false);
        $("body").on("click", "#btn-close-ebook", handleCloseEbookClick);
        parent.window.addEventListener("unload", handleReaderUnload);

        await getEbook(ebook_id, fetchStatusHandler, book, reader);

        preloadBookSections(book);

        await Promise.all([
            setTextAndAudioPos(),
            buildToc(),
            populateBookMetadata()
        ]);
    }

    initializeEbookReader().catch((error) => {
        console.error(error);
    });
});
