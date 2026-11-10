// SPDX-License-Identifier: GPL-3.0-or-later

/** Same-document pagination: annotation wrappers never become part of a saved location. */
class EbookPagination {
    constructor(viewport, text, { mode = 'auto', onChange = () => {} } = {}) {
        this.viewport = viewport;
        this.text = text;
        this.mode = mode;
        this.onChange = onChange;
        this.page = 0;
        this.pages = 1;
        this.columns = 1;
        this.offset = 0;
        this.ready = false;
        this.frame = null;
        this.scroll_timer = null;
        this.resize = new ResizeObserver(() => this.scheduleLayout());
        this.resize.observe(viewport.parentElement);
        this.mutations = new MutationObserver(() => this.scheduleLayout());
        this.mutations.observe(text, { childList: true, subtree: true, characterData: true });
        this.onScroll = () => {
            if (this.mode !== 'scroll' || !this.ready) return;
            clearTimeout(this.scroll_timer);
            this.scroll_timer = setTimeout(() => this.notify(), 120);
        };
        this.onLoad = () => this.scheduleLayout();
        viewport.addEventListener('scroll', this.onScroll);
        text.addEventListener('load', this.onLoad, true);
        this.onFocus = event => this.reveal(event.target);
        text.addEventListener('focusin', this.onFocus);
        document.fonts.ready.then(() => this.scheduleLayout());
        document.fonts.addEventListener('loadingdone', this.onLoad);
    }

    setContent(offset = 0) {
        this.ready = true;
        this.offset = Math.max(0, Number(offset) || 0);
        this.layout();
    }

    setMode(mode) {
        this.mode = ['auto', 'single', 'scroll'].includes(mode) ? mode : 'auto';
        this.layout();
    }

    scheduleLayout() {
        if (!this.ready || this.frame !== null) return;
        this.frame = requestAnimationFrame(() => {
            this.frame = null;
            this.layout();
        });
    }

    layout() {
        if (!this.ready) return;
        const anchor = this.offset;
        const parent = this.viewport.parentElement;
        const style = getComputedStyle(parent);
        const available = parent.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight);
        const font_size = parseFloat(getComputedStyle(this.text).fontSize);
        this.columns = this.mode === 'auto' && available >= Math.max(1080, font_size * 58) ? 2 : 1;
        this.gap = this.columns === 2 ? Math.max(48, font_size * 3) : 0;
        this.width = Math.floor(Math.min(available, this.columns === 2 ? 1440 : 760));
        this.viewport.style.width = this.width + 'px';
        this.viewport.classList.toggle('ebook-scrolling', this.mode === 'scroll');
        this.viewport.classList.toggle('ebook-spread', this.columns === 2);
        this.viewport.scrollLeft = 0;
        this.text.style.transform = '';
        this.text.style.columnGap = this.gap + 'px';
        this.text.style.columnWidth = this.mode === 'scroll' ? 'auto' : ((this.width - this.gap) / this.columns) + 'px';
        this.text.style.columnCount = this.mode === 'scroll' ? 'auto' : String(this.columns);
        this.text.style.height = this.mode === 'scroll' ? 'auto' : this.viewport.clientHeight + 'px';
        this.text.style.setProperty('--ebook-page-height', this.viewport.clientHeight + 'px');
        this.rtl = getComputedStyle(this.text).direction === 'rtl';
        this.stride = (this.width + this.gap) / this.columns;
        // Range bounds also work for RTL columns extending to the left of the viewport.
        const range = document.createRange();
        range.selectNodeContents(this.text);
        const origin = this.text.getBoundingClientRect();
        let extent = 0;
        for (const rect of range.getClientRects()) {
            if (!rect.width || !rect.height) continue;
            extent = Math.max(extent, this.rtl ? origin.right - rect.left : rect.right - origin.left);
        }
        this.pages = this.mode === 'scroll' ? 1 : Math.max(1, Math.ceil((extent + this.gap - 1) / this.stride));
        this.restore(anchor);
    }

    /** UTF-16 offsets in textContent are stable when words are wrapped or unwrapped. */
    textNodes() {
        const walker = document.createTreeWalker(this.text, NodeFilter.SHOW_TEXT);
        const nodes = [];
        let offset = 0;
        while (walker.nextNode()) {
            const node = walker.currentNode;
            nodes.push({ node, offset });
            offset += node.length;
        }
        return nodes;
    }

    isVisible(rect) {
        const bounds = this.viewport.getBoundingClientRect();
        return rect.width > 0 && rect.height > 0 && rect.right > bounds.left + 1
            && rect.left < bounds.right - 1 && rect.bottom > bounds.top + 1 && rect.top < bounds.bottom - 1;
    }

    capture() {
        for (const { node, offset } of this.textNodes()) {
            if (!node.textContent.trim()) continue;
            const range = document.createRange();
            range.selectNodeContents(node);
            if (!Array.from(range.getClientRects()).some(rect => this.isVisible(rect))) continue;
            // A phrase may straddle pages. Locate its first visible character.
            for (let i = 0; i < node.length; i++) {
                range.setStart(node, i);
                range.setEnd(node, i + 1);
                if (this.isVisible(range.getBoundingClientRect())) return offset + i;
            }
        }
        return 0;
    }

    restore(offset) {
        const nodes = this.textNodes();
        const entry = nodes.find(({ node, offset: start }) => offset < start + node.length) || nodes.at(-1);
        this.page = 0;
        this.text.style.transform = '';
        this.viewport.scrollTop = 0;
        if (entry && entry.node.length) {
            const range = document.createRange();
            const index = Math.min(entry.node.length - 1, Math.max(0, offset - entry.offset));
            range.setStart(entry.node, index);
            range.setEnd(entry.node, index + 1);
            this.revealRect(range.getBoundingClientRect());
        } else {
            this.notify();
        }
    }

    reveal(element) {
        if (!element || !this.text.contains(element)) return;
        if (this.mode !== 'scroll') this.viewport.scrollLeft = 0;
        this.revealRect(element.getClientRects()[0] || element.getBoundingClientRect());
    }

    revealRect(rect) {
        const bounds = this.viewport.getBoundingClientRect();
        if (this.mode === 'scroll') {
            this.viewport.scrollTop += rect.top - bounds.top;
            this.notify();
        } else {
            const distance = this.rtl ? bounds.right - rect.right : rect.left - bounds.left;
            this.goToPage(this.page + Math.floor((distance + 1) / this.stride));
        }
    }

    goToPage(page) {
        this.viewport.scrollLeft = 0;
        this.viewport.scrollTop = 0;
        const last = Math.floor((this.pages - 1) / this.columns) * this.columns;
        this.page = Math.max(0, Math.min(last, Math.floor(page / this.columns) * this.columns));
        const distance = this.page * this.stride * (this.rtl ? 1 : -1);
        this.text.style.transform = `translateX(${distance}px)`;
        this.notify();
    }

    turn(direction) {
        if (this.mode === 'scroll') {
            const maximum = this.viewport.scrollHeight - this.viewport.clientHeight;
            if ((direction < 0 && this.viewport.scrollTop <= 1)
                || (direction > 0 && this.viewport.scrollTop >= maximum - 1)) return false;
            this.viewport.scrollTop += direction * this.viewport.clientHeight * 0.9;
            this.notify();
            return true;
        }
        const next = this.page + direction * this.columns;
        if (next < 0 || next >= this.pages) return false;
        this.goToPage(next);
        return true;
    }

    goToEnd() {
        if (this.mode === 'scroll') {
            this.viewport.scrollTop = this.viewport.scrollHeight;
            this.notify();
        } else {
            this.goToPage(this.pages - 1);
        }
    }

    progress(value) {
        if (this.mode === 'scroll') {
            this.viewport.scrollTop = value * (this.viewport.scrollHeight - this.viewport.clientHeight);
            this.notify();
        } else {
            const last = Math.floor((this.pages - 1) / this.columns) * this.columns;
            this.goToPage(Math.round(value * last / this.columns) * this.columns);
        }
    }

    notify() {
        this.offset = this.capture();
        this.onChange({
            offset: this.offset, page: this.page, pages: this.pages, columns: this.columns,
            scrolling: this.mode === 'scroll'
        });
    }

    destroy() {
        this.ready = false;
        this.resize.disconnect();
        this.mutations.disconnect();
        cancelAnimationFrame(this.frame);
        clearTimeout(this.scroll_timer);
        this.viewport.removeEventListener('scroll', this.onScroll);
        this.text.removeEventListener('load', this.onLoad, true);
        this.text.removeEventListener('focusin', this.onFocus);
        document.fonts.removeEventListener('loadingdone', this.onLoad);
    }
}
