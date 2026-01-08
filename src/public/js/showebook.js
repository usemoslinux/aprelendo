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
    const doclang = $("html").attr("lang");
    const ebook_id = $("#text").attr("data-idText");
    const book = ePub();
    let text_pos = "";

    window.parent.show_confirmation_dialog = true; // show confirmation dialog on close

    let text = document.getElementById("text");

    let formData = new FormData();
    formData.append("id", ebook_id);

    /**
     * Throws error if response.status !== 200
     * ajax/getebook.php returns 404 in case ebook was not found or if user is not allowed
     * to have access to it.
     * @param response
     */
    function fetchStatusHandler(response) {
        if (response.status === 200) {
            return response;
        } else {
            throw new Error(response.statusText);
        }
    } // end fetchStatusHandler

    /**
     * Ajax call to fetch an ebook. Response has to be converted to arrayBuffer to allow
     * epub.js (book.open function) to process it correctly
     */
    fetch("ajax/getebook.php?id=" + ebook_id)
        .then(fetchStatusHandler)
        .then(response => response.arrayBuffer())
        .then(arraybuffer => book.open(arraybuffer))
        .catch(function (e) {
            alert('There was an unexpected problem opening this ebook file. Try again later.');
            window.location.replace("/texts");
        });

    let rendition = book.renderTo("text", {
        flow: "scrolled-doc"
    });

    // theming
    let reader = document.getElementById("readerpage");

    rendition.themes.register("darkmode", "/css/ebooks.min.css");
    rendition.themes.register("lightmode", "/css/ebooks.min.css");
    rendition.themes.register("sepiamode", "/css/ebooks.min.css");

    rendition.themes.default({
        body: {
            "font-family": reader.style.fontFamily + " !important",
            "font-size": reader.style.fontSize + " !important",
            "text-align": reader.style.textAlign + " !important",
            "line-height": reader.style.lineHeight + " !important",
            "padding": "0 5% !important"
        }
    });

    rendition.themes.select(reader.className);

    book.opened.then(function () {
        setTextAndAudioPos();
    });

    book.loaded.spine.then((spine) => {
        spine.each((item) => {
            item.load(book.load.bind(book));
        });
    });

    let next = document.getElementById("next");
    next.addEventListener(
        "click",
        function (e) {
            e.preventDefault();
            $(next).tooltip('hide');
            $.when(SaveWords()).then(function () {
                let url = next.getAttribute("href");
                display(url);
            });
        },
        false
    );

    $("body").on("click", "#btn-close-ebook", function () {
        // save word status before closing
        $.when(SaveWords()).then(function () {
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
                $.when(saveTextAndAudioPos(text_pos, audio_pos)).then(function () {
                    // don't show confirmation dialog when closing window
                    window.parent.show_confirmation_dialog = false;
                    window.location.replace("/texts");
                });
            }
        });
    }); // end #btn-close-ebook.on.click

    /**
     * Updates status of all underlined words & phrases
     */
    function SaveWords() {
        // build array with underlined words
        let oldwords = [];
        let word = "";

        // don't show confirmation dialog when closing window
        window.parent.show_confirmation_dialog = false;

        $("#text")
            .find(".reviewing")
            .each(function () {
                word = $(this)
                    .text()
                    .toLowerCase();
                if (jQuery.inArray(word, oldwords) == -1) {
                    oldwords.push(word);
                }
            });

        return $.ajax({
            type: "POST",
            url: "/ajax/updatewords.php",
            data: {
                words: oldwords
            }
        })
            .done(function (data) {
                if (data.error_msg == null) {
                    // update user score (gems)
                    const review_data = {
                        words: {
                            new: getUniqueElements('.reviewing.new'),
                            learning: getUniqueElements('.reviewing.learning'),
                            forgotten: getUniqueElements('.reviewing.forgotten')
                        },
                        texts: { reviewed: 1 }
                    };

                    return $.ajax({
                        type: "post",
                        url: "/ajax/updateuserscore.php",
                        data: review_data
                    })
                        .done(function (data) {
                            if (data.error_msg != null) {
                                alert("Oops! There was an unexpected error updating user score.");
                            }
                        })
                        .fail(function (XMLHttpRequest, textStatus, errorThrown) {
                            alert("Oops! There was an unexpected error updating user score.");
                        });
                } else {
                    alert("Oops! There was an error unexpected error saving this text.");
                }
            })
            .fail(function (XMLHttpRequest, textStatus, errorThrown) {
                alert("Oops! There was an error unexpected error saving this text.");
            });
    } // end SaveWords

    parent.window.addEventListener("unload", function () {
        book.destroy();
    }); // end parent.window.unload

    book.loaded.navigation.then(function (toc) {
        const $nav = document.getElementById("toc");
        const docfrag = document.createDocumentFragment();

        $nav.classList.add("list-group");

        const makeLink = (label, href, depth = 0) => {
            const a = document.createElement("a");
            a.href = href;
            a.className = "list-group-item list-group-item-action toc-item";

            a.style.setProperty("--depth", depth);
            a.dataset.depth = String(depth);
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
    });

    book.loaded.metadata.then(function (meta) {
        let $title = document.getElementById("title");
        let $book_title = document.getElementById("book-title");
        let $author = document.getElementById("author");
        let $publisher = document.getElementById("publisher");
        let $pubdate = document.getElementById("pubdate");
        let $cover = document.getElementById("cover");

        $title.textContent = (meta.title && meta.title.trim() !== "") ? meta.title : "Untitled";
        $book_title.textContent = (meta.title && meta.title.trim() !== "") ? meta.title : "Untitled";
        $author.textContent = (meta.creator && meta.creator.trim() !== "") ? meta.creator : "Unknown";
        $publisher.textContent = (meta.publisher && meta.publisher.trim() !== "") ? meta.publisher : "Unknown";
        $pubdate.textContent = (meta.pubdate && meta.pubdate.trim() !== "") ? new Intl.DateTimeFormat("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric"
        }).format(new Date(meta.pubdate)) : "Not available";

        if (book.archive) {
            book.archive.createUrl(book.cover).then(function (url) {
                $cover.src = url;
            });
        } else {
            $cover.src = book.cover;
        }
    }); // end book.loaded.metadata

    function display(item) {
        let section = book.spine.get(item);
        let text_html = '';

        function resetNextChapterBtn() {
            next.textContent = "";
            next.classList.add('d-none');
            next.href = "#";
        }

        resetNextChapterBtn();

        if (section) {
            section.render().then(function (ebook_html) {
                let $parsed = cleanEbookHTML(ebook_html);

                // underline text
                $(".loading-spinner-container").fadeIn(300);
                $("#text-container").hide();
                $.ajax({
                    type: "POST",
                    url: "/ajax/getuserwords.php",
                    data: { txt: $parsed.html() },
                    dataType: "json"
                })
                    .done(function (data) {
                        text_html = TextUnderliner.apply(data, doclang, false);
                        text.innerHTML = text_html;
                        TextProcessor.updateAnchorsList();
                        $("#text-container").fadeIn(300);
                        $(".loading-spinner-container").fadeOut(300);
                        scrollToPageTop();
                    })
                    .fail(function (xhr, ajaxOptions, thrownError) {
                        alert(
                            "There was an unexpected error when trying to underline words for this ebook!"
                        );
                    }); // end $.ajax  

                // create next chapter link on bottom of page
                let nextSection = section.next();

                if (nextSection) {
                    const nextNav = book.navigation.get(nextSection.href);
                    let nextLabel = '';

                    if (nextNav) {
                        nextLabel = nextNav.label;
                    } else {
                        nextLabel = "Next chapter";
                    }

                    next.textContent = nextLabel + " »";
                    next.href = nextSection.href;

                    if (!isMobileDevice()) {
                        next.setAttribute('data-bs-title', 'Go to next chapter & mark underlined words as reviewed');
                        new bootstrap.Tooltip(next, {
                            trigger: 'hover'
                        })
                    }

                    next.classList.remove('d-none');
                }

                text_pos = item;
                updateToc(item);
            });
        }

        return section;
    } // end display

    function cleanEbookHTML(html) {
        // Replace line breaks (\n or \r) with spaces.
        html = html.replace(/[\r\n]+/g, ' ');
        
        // Wrap the provided HTML into a container.
        let $parsed = $('<div/>').append(html);

        // Remove HTML comments.
        $parsed.contents().filter(function () {
            return this.nodeType === 8; // Node.COMMENT_NODE
        }).remove();

        // Remove linked stylesheets.
        $parsed.find('link[rel="stylesheet"]').remove();

        // Remove unwanted elements.
        $parsed.find('head, meta, style, title, br').remove();

        // Remove formatting from all elements.
        // For non-image elements, remove class, style, and id.
        // For images, remove any attribute except 'src' and 'alt'.
        $parsed.find('*').each(function () {
            let tag = this.tagName.toLowerCase();
            if (tag !== 'img') {
                $(this).removeAttr('class').removeAttr('style').removeAttr('id');
            } else {
                // For each image, iterate over its attributes in reverse order.
                for (let i = this.attributes.length - 1; i >= 0; i--) {
                    let attr_name = this.attributes[i].name;
                    if (attr_name !== 'src' && attr_name !== 'alt') {
                        $(this).removeAttr(attr_name);
                    }
                }
            }
        });

        // Define the set of block-level elements that should have newline separation.
        const block_elements = ['div', 'blockquote', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

        // Recursively process nodes to build a plain string.
        // For text nodes, return their content.
        // For images, return the cleaned outerHTML.
        // For block-level elements, insert newlines before and after their content.
        function processNode(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                return node.nodeValue;
            }
            if (node.nodeType === Node.ELEMENT_NODE) {
                let tag = node.tagName.toLowerCase();
                if (tag === 'img') {
                    // Return the image element as minimal HTML.
                    return node.outerHTML;
                } else {
                    let content = '';
                    $(node).contents().each(function () {
                        content += processNode(this);
                    });
                    if (block_elements.indexOf(tag) !== -1) {
                        return "\n" + content + "\n";
                    } else {
                        return content;
                    }
                }
            }
            return '';
        }

        // Process the entire container.
        let result = processNode($parsed[0]);
        
        // Normalize newlines by replacing multiple consecutive newlines with a single newline and trimming.
        result = result.replace(/\n\s*\n/g, "\n\n").trim();

        return $('<div/>').append(result);
    }

    function updateToc(current_chapter_url) {
        let $nav = document.getElementById('toc');
        let $bold_elements_in_nav = $nav.querySelector('.bg-primary');;
        let $title = document.getElementById('book-title-chapter');
        let $selector;

        // Remove existing bg-primary/text-light classes if present
        if ($bold_elements_in_nav) {
            $bold_elements_in_nav.classList.remove('bg-primary', 'text-light');
        }

        // Determine the proper link selector
        // If it's the first chapter, grab the first link; otherwise, do an exact match
        if (current_chapter_url === 1) {
            $selector = $nav.querySelector('a');
        } else {
            $selector = $nav.querySelector(`a[href="${current_chapter_url}"]`);
        }

        // Apply classes and update the title if the link was found
        if ($selector) {
            $selector.classList.add('bg-primary', 'text-light');
            $title.textContent = ' - ' + $selector.textContent;
        }
    } // end updateToc

    function setTextAndAudioPos() {
        // retrieve ebook & audio last reading position
        $.ajax({
            type: "POST",
            url: "/ajax/ebookposition.php",
            data: { mode: "GET", id: ebook_id },
            dataType: "json"
        })
            .done(function (data) {
                const text_pos = data.text_pos;
                const audio_pos = data.audio_pos;
                const audio_pos_number = parseFloat(audio_pos);
                const audio = document.getElementById("audioplayer");
                const video = document.getElementById("videoplayer");

                // load text position, if available
                if (text_pos) {
                    display(text_pos);
                } else {
                    display(1);
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
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                display(1);
                if (audio != null) {
                    audio.currentTime = 0;
                }
            });
    } // end setTextAndAudioPos

    function saveTextAndAudioPos(text_pos, audio_pos) {
        return $.ajax({
            type: "POST",
            url: "/ajax/ebookposition.php",
            data: { mode: "SAVE", id: ebook_id, audio_pos: audio_pos, text_pos: text_pos }
        })
            .done(function (data) {
                if (data.error_msg != null) {
                    alert('Oops! There was an error unexpected error saving text and audio position');
                }
            })
            .fail(function (xhr, ajaxOptions, thrownError) {
                alert('Oops! There was an error unexpected error saving text and audio position');
            });
    } // end saveTextAndAudioPos
});
