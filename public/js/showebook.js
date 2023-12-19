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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */
$(document).ready(function() {
    const doclang = $("html").attr("lang");
    const ebook_id = $("#viewer").attr("data-idText");
    const book = ePub();
    let text_pos = "";

    window.parent.show_confirmation_dialog = true; // show confirmation dialog on close

    let $viewer = document.getElementById("viewer");

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

    let rendition = book.renderTo("viewer", {
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
            $.when(SaveWords()).then(function () {
                let url = next.getAttribute("href");
                display(url);
                $("html, body").animate({
                    scrollTop: 0
                }, "fast");
            });
        },
        false
    );

    let prev = document.getElementById("prev");
    prev.addEventListener(
        "click",
        function (e) {
            e.preventDefault();
            let url = prev.getAttribute("href");
            display(url);
            $("html, body").animate({
                scrollTop: 0
            }, "fast");
        },
        false
    );

    $("body").on("click", "#btn-close-ebook", function () {
        // save word status before closing
        $.when(SaveWords()).then(function () {
            // save book position to resume reading from there later
            let audio = document.getElementById("audioplayer");
            let audio_pos = audio != null ? audio.currentTime : 0;
        
            if (text_pos) {
                $.when(saveTextAndAudioPos(text_pos, audio_pos)).then(function() {
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

        $(document)
            .find(".learning")
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
            url: "/ajax/archivetext.php",
            // async: false,
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
        let $nav = document.getElementById("toc"),
            docfrag = document.createDocumentFragment();

        const addTocItems = function (parent, tocItems) {
            let $ul = document.createElement("ul");
            tocItems.forEach(function (chapter) {
                let item = document.createElement("li");
                let link = document.createElement("a");
                link.textContent = chapter.label;
                link.href = chapter.href;

                item.appendChild(link);

                if (chapter.subitems) {
                    addTocItems(item, chapter.subitems);
                }

                link.onclick = function () {
                    const url = link.getAttribute("href");

                    document.getElementById("opener").click();

                    display(url);
                    $("html, body").animate({
                        scrollTop: 0
                    }, "fast");
                    return false;
                };

                $ul.appendChild(item);
            });
            parent.appendChild($ul);
        };

        addTocItems(docfrag, toc);

        $nav.appendChild(docfrag);

        if ($nav.offsetHeight + 60 < window.innerHeight) {
            $nav.classList.add("fixed");
        }
    }); // end book.loaded.navigation

    book.loaded.metadata.then(function (meta) {
        let $title = document.getElementById("title");
        let $book_title = document.getElementById("book-title");
        let $author = document.getElementById("author");
        let $cover = document.getElementById("cover");

        if ($title != null) {
            $title.textContent = meta.title;
            $book_title.textContent = meta.title;
            $author.textContent = meta.creator;
            if (book.archive) {
                book.archive.createUrl(book.cover).then(function (url) {
                    $cover.src = url;
                });
            } else {
                $cover.src = book.cover;
            }
        }
    }); // end book.loaded.metadata

    function display(item) {
        let section = book.spine.get(item);

        if (section) {
            section.render().then(function (ebook_html) {
                let $parsed = cleanEbookHTML(ebook_html);

                // underline text
                $(".loading-spinner").fadeIn(1000);
                $.ajax({
                    type: "POST",
                    url: "/ajax/getuserwords.php",
                    data: { txt: $parsed.html() },
                    dataType: "json"
                })
                    .done(function (data) {
                        $viewer.innerHTML = underlineWords(data, doclang, false);
                        $(".loading-spinner").fadeOut(1000);
                    })
                    .fail(function (xhr, ajaxOptions, thrownError) {
                        alert(
                            "There was an unexpected error when trying to underline words for this ebook!"
                        );
                    }); // end $.ajax  

                // create previous and next links on top and bottom of page
                let nextSection = section.next();
                let prevSection = section.prev();

                if (nextSection) {
                    const nextNav = book.navigation.get(nextSection.href);
                    let nextLabel = '';

                    if (nextNav) {
                        nextLabel = nextNav.label;
                    } else {
                        nextLabel = "next";
                    }

                    next.textContent = nextLabel + " »";
                    next.href = nextSection.href;
                    
                    if (!isMobileDevice) {
                        next.setAttribute('data-bs-title', 'Go to next chapter & mark underlined words as practiced');
                        new bootstrap.Tooltip(next);
                    }
                } else {
                    next.textContent = "";
                }

                if (prevSection) {
                    const prevNav = book.navigation.get(prevSection.href);
                    let prevLabel = '';

                    if (prevNav) {
                        prevLabel = prevNav.label;
                    } else {
                        prevLabel = "previous";
                    }

                    prev.textContent = "« " + prevLabel;
                    prev.href = prevSection.href;
                    
                    if (!isMobileDevice) {
                        prev.setAttribute('data-bs-title', 'Go to previous chapter');
                        new bootstrap.Tooltip(prev);
                    }
                } else {
                    prev.textContent = "";
                }

                text_pos = item;
                updateToc(item);
            });
        }

        return section;
    } // end display

    function cleanEbookHTML(html) {
        let $parsed = $('<div/>').append(html);
        
        // Remove HTML comments
        $parsed.contents().filter(function() {
            return this.nodeType === 8; // Node.COMMENT_NODE
        }).remove();

        // Remove styles and classes
        $parsed.find('*').removeAttr("class").removeAttr("style");
        $parsed.find('link[rel="stylesheet"]').remove();

        // Completely remove unwanted HTML elements
        $parsed.find('head, meta, style, title, br').remove();
        
        // Convert all DIV to P
        $parsed.find('div').replaceWith(function() {
            return $('<p/>').html($(this).html());
        });

        // Convert all blockquotes to P with italics
        $parsed.find('blockquote').replaceWith(function() {
            return $('<p/>').html($(this).html()).addClass('fst-italic');
        });

        // Convert all H to P
        $parsed.find('h1, h2, h3, h4, h5, h6').replaceWith(function() {
            let tagName = this.tagName.toLowerCase();
            let $heading = $(this);
            $heading.parent().after($heading);
            return $('<p/>').html($(this).text()).addClass(tagName);
        });
        
        // Extract images to parent
        $parsed.find('img').each(function() {
            let $img = $(this);
            $img.parent().after($img);
            $img.addClass('mx-auto d-block');
            $img.wrap('<p></p>');
        });

        // Remove all HTML from elements that are not IMG or P
        $parsed.find('*').not('img, p').each(function() {
            let $this = $(this);
            $this.replaceWith($(this).text());
        });

        // Trim HTML and remove empty elements
        $parsed.html($parsed.html().trim());
        $parsed.find(':empty').not('img').remove();
        $parsed.find('p:not(:has(img))').filter(function () {
            return $.trim($(this).text()) === '';
        }).remove();

        return $parsed;
    } // end cleanEbookHTML

    function updateToc(current_chapter_url) {
        let $nav = document.getElementById('toc');
        let $selector = $nav.querySelector('.fw-bold');
        if ($selector !== null) {
            $selector.classList.remove('fw-bold', 'text-primary');
        }

        $selector = $nav.querySelector('a[href*="' + current_chapter_url + '"]');
        if ($selector !== null) {
            $selector.classList.add('fw-bold', 'text-primary');
        }
    } // end updateToc

    function setTextAndAudioPos() {
        // retrieve ebook & audio last reading position
        $.ajax({
            type: "POST",
            url: "/ajax/ebookposition.php",
            data: { mode: "GET", id: ebook_id},
            dataType: "json"
        })
        .done(function(data) {
            const text_pos = data.text_pos;
            const audio_pos = parseFloat(data.audio_pos);
            const audio = document.getElementById("audioplayer");

            // load text position, if available
            if (text_pos) {
                display(text_pos);
            } else {
                display(1);
            }

            // load audio position, if available
            if (audio != null) {
                if (!isNaN(audio_pos)) {
                    audio.currentTime = audio_pos;
                } else {
                    audio.currentTime = 0;
                }
            }
        })
        .fail(function(xhr, ajaxOptions, thrownError) {
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
        .done(function(data) {
            if (data.error_msg != null) {
                alert('Oops! There was an error unexpected error saving text and audio position');
            }
        })
        .fail(function(xhr, ajaxOptions, thrownError) {
            alert('Oops! There was an error unexpected error saving text and audio position');
        });
    } // end saveTextAndAudioPos
});