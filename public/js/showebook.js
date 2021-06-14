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
    var doclang = $("html").attr("lang");
    var ebook_id = $("script[src*='showebook-min.js']").attr("data-id");
    var book = ePub();

    window.parent.show_confirmation_dialog = false; // don't show confirmation dialog on close
    
    var $viewer = document.getElementById("viewer");

    var formData = new FormData();
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
        .catch(function(e) {
            alert('There was an unexpected problem opening this ebook file. Try again later.');
            window.location.replace("texts.php");
        });

    var rendition = book.renderTo("viewer", {
        flow: "scrolled-doc"
    });

    // theming
    var reader = document.getElementById("readerpage");

    rendition.themes.register("darkmode", "/css/ebooks-min.css");
    rendition.themes.register("lightmode", "/css/ebooks-min.css");
    rendition.themes.register("sepiamode", "/css/ebooks-min.css");

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

    book.opened.then(function(){
        var key = book.key() + "-lastpos";
        var last_pos = localStorage.getItem(key);

        if (last_pos) {
            display(last_pos);
        } else {
            display(1);
        }
    });

    book.loaded.spine.then((spine) => {
        spine.each((item) => {
            item.load(book.load.bind(book));
        });
    });

    var next = document.getElementById("next");
    next.addEventListener(
        "click",
        function(e) {
            e.preventDefault();
            $.when(SaveWords()).then(function() {
                var url = next.href.substr(next.href.indexOf('/', 8) + 1);
                display(url);
                $("html, body").animate({
                    scrollTop: 0
                }, "fast");
            });
        },
        false
    );
        
    var prev = document.getElementById("prev");
    prev.addEventListener(
        "click",
        function(e) {
            e.preventDefault();
            $.when(SaveWords()).then(function() {
                var url = prev.href.substr(prev.href.indexOf('/', 8) + 1);
                display(url);
                $("html, body").animate({
                    scrollTop: 0
                }, "fast");
            });
        },
        false
    );

    $("body").on("click", "#btn-close-ebook", function() {
        window.location.replace("texts.php");
    }); // end #btn-close-ebook.on.click

    /**
     * Updates status of all underlined words & phrases
     */
    function SaveWords() {
        // build array with underlined words
        var $pagereader = $(document).find('iframe[id^="epubjs"]');
        var oldwords = [];
        var word = "";

        $(document)
            .find(".learning")
            .each(function() {
                word = $(this)
                    .text()
                    .toLowerCase();
                if (jQuery.inArray(word, oldwords) == -1) {
                    oldwords.push(word);
                }
            });

        $.ajax({
                type: "POST",
                url: "/ajax/archivetext.php",
                async: false,
                data: {
                    words: oldwords
                }
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Oops! There was an error updating the database.");
            });

        // don't show confirmation dialog when closing window
        window.parent.show_confirmation_dialog = false;

    } // end SaveWords

    parent.window.addEventListener("unload", function() {
        book.destroy();
    }); // end parent.window.unload

    var navigation = document.getElementById("navigation");
    var main = document.getElementById("main");
    var opener = document.getElementById("opener");

    opener.addEventListener(
        "click",
        function(e) {
            navigation.classList.toggle("sidebar-closed");
            main.classList.toggle("sidebar-opened");
            e.preventDefault();
        },
        false
    ); // end opener.on.click

    book.loaded.navigation.then(function(toc) {
        var $nav = document.getElementById("toc"),
            docfrag = document.createDocumentFragment();
        var key = book.key() + "-lastpos";
        var current_section_href = localStorage.getItem(key);
        
        var addTocItems = function(parent, tocItems) {
            var $ul = document.createElement("ul");
            tocItems.forEach(function(chapter) {
                var item = document.createElement("li");
                var link = document.createElement("a");
                link.textContent = chapter.label;
                link.href = chapter.href;

                // link.innerHTML = current_section_href == chapter.href ? '<b>'+ link.innerHTML + '</b>' : link.innerHTML;

                item.appendChild(link);

                if (chapter.subitems) {
                    addTocItems(item, chapter.subitems);
                }

                link.onclick = function() {
                    var url = link.getAttribute("href");
                    display(url);
                    opener.click();
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

    book.loaded.metadata.then(function(meta) {
        var $title = document.getElementById("title");
        var $book_title = document.getElementById("book-title");
        var $author = document.getElementById("author");
        var $cover = document.getElementById("cover");

        if ($title != null) {
            $title.textContent = meta.title;
            $book_title.textContent = meta.title;
            $author.textContent = meta.creator;
            if (book.archive) {
                book.archive.createUrl(book.cover).then(function(url) {
                    $cover.src = url;
                });
            } else {
                $cover.src = book.cover;
            }
        }
    }); // book.loaded.metadata

    function display(item){
        var section = book.spine.get(item);
        if(section) {
          section.render().then(function(ebook_html){
            var $parsed = $('<div/>').append(ebook_html);
            $parsed.find('*').removeAttr("class").removeAttr("style");
            // $parsed.find("div").each(function(index, element){
            //     var $elem = $(element);
            //     $elem.replaceWith('<p>' + $elem.text() + '</p>')
            // });

            // underline text
            $(".loading-spinner").fadeIn(1000);
            $.ajax({
                type: "POST",
                url: "/ajax/getuserwords.php",
                data: { txt: $parsed.html() },
                dataType: "json"
            })
            .done(function(data) {
                $viewer.innerHTML = underlineWords(data, doclang);
                $(".loading-spinner").fadeOut(1000);
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                alert(
                    "There was an unexpected error when trying to underline words for this ebook!"
                );
            }); // end $.ajax  

            // create previous and next links on top and bottom of page
            var nextSection = section.next();
            var prevSection = section.prev();
    
            if (nextSection) {
                nextNav = book.navigation.get(nextSection.href);
    
                if (nextNav) {
                    nextLabel = nextNav.label;
                } else {
                    nextLabel = "next";
                }
    
                next.textContent = nextLabel + " »";
                next.href = nextSection.href;
            } else {
                next.textContent = "";
            }
    
            if (prevSection) {
                prevNav = book.navigation.get(prevSection.href);
    
                if (prevNav) {
                    prevLabel = prevNav.label;
                } else {
                    prevLabel = "previous";
                }
                
                prev.textContent = "« " + prevLabel;
                prev.href = prevSection.href;
            } else {
                prev.textContent = "";
            }

            // save book position to resume reading from there later
            localStorage.setItem(book.key() + "-lastpos", item);
            updateToc(item);
          });
        }

        return section;
    }

    function updateToc(current_chapter_url) {
        var $nav = document.getElementById('toc');
        if ($nav.querySelector('.font-weight-bold') !== null) {
            $nav.querySelector('.font-weight-bold').classList.remove('font-weight-bold');
        }
        
        $nav.querySelector('a[href*="' + current_chapter_url + '"]').classList.add('font-weight-bold');
    }
});