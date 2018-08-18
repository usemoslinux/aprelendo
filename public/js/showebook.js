$(document).ready(function () {
    var ebook_id = $("script[src*='showebook.js']").attr('data-id');
    var currentSectionIndex = 0;
    var book = ePub();

    var formData = new FormData();
    formData.append('id', ebook_id);

    /**
     * Throws error if response.status !== 200
     * db/getebook.php returns 404 in case ebook was not found or if user is not allowed
     * to have access to it. 
     * @param response 
     */
    function fetchStatusHandler(response) {
        if (response.status === 200) {
            return response;
        } else {
            throw new Error(response.statusText);
        }
    }

    /**
     * Ajax call to fetch an ebook. Response has to be converted to arrayBuffer to allow 
     * epub.js (book.open function) to process it correctly
     */
    fetch('db/getebook.php', {
            method: 'POST',
            body: formData,
        })
        .then(fetchStatusHandler)
        .then(response => response.arrayBuffer())
        .then(arraybuffer => openBook(arraybuffer))
        .catch(function () {
            // window.location.replace("texts.php")
        });

    /**
     * Opens and renders an ebook using epub.js
     * @param {arrayBuffer} e 
     */
    function openBook(e) {
        var bookData = e;
        book.open(bookData);

        var rendition = book.renderTo("viewer", {
            width: "100%",
            height: window.innerHeight - 45,
            spread: "always"
        });

        // theming
        var reader = document.getElementById('readerpage');

        rendition.themes.register("darkmode", "/css/ebooks.css");
        rendition.themes.register("lightmode", "/css/ebooks.css");
        rendition.themes.register("sepiamode", "/css/ebooks.css");

        rendition.themes.default({
            h1: {
                'font-family': reader.style.fontFamily + ' !important'
            },
            p: {
                'font-family': reader.style.fontFamily + ' !important',
                'font-size': reader.style.fontSize + ' !important',
                'text-align': reader.style.textAlign + ' !important'
            }
        });

        rendition.themes.select(reader.className);

        book.ready.then(() => {

            // alert('ready');

            rendition.hooks.content.register(function (contents) {
                // alert('hook');
                // Add JQuery
                contents.addScript("https://code.jquery.com/jquery-3.3.1.min.js")
                    .then(function () {
                        // Add the rest of the scripts and stylesheets
                        Promise.all([
                            contents.addScript("/js/showtext.js"),
                            contents.addScript("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js")
                            // contents.addStylesheet("/css/ebooks.css")
                            // contents.addStylesheet("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css")
                        ]);

                    });

                // underline words
                $.ajax({
                        type: "POST",
                        url: "../db/underlinewords.php",
                        data: {
                            txt: contents.content.innerHTML
                        }
                    })
                    .done(function (result) {
                        contents.content.innerHTML = result;
                    })
                    .fail(function (xhr, ajaxOptions, thrownError) {
                        alert('There was an unexpected error when trying to underline words for this ebook!');
                    });

            });

            var next = document.getElementById("next");

            next.addEventListener("click", function (e) {
                book.package.metadata.direction === "rtl" ? rendition.prev() : rendition.next();
                this.blur();
                e.preventDefault();
            }, false);

            var prev = document.getElementById("prev");
            prev.addEventListener("click", function (e) {
                book.package.metadata.direction === "rtl" ? rendition.next() : rendition.prev();
                this.blur();
                e.preventDefault();
            }, false);

            var keyListener = function (e) {

                // Left Key
                if ((e.keyCode || e.which) == 37) {
                    book.package.metadata.direction === "rtl" ? rendition.next() : rendition.prev();
                }

                // Right Key
                if ((e.keyCode || e.which) == 39) {
                    book.package.metadata.direction === "rtl" ? rendition.prev() : rendition.next();
                }

            };

            rendition.on("keyup", keyListener);
            document.addEventListener("keyup", keyListener, false);

        })

        var title = document.getElementById("title");

        rendition.display(currentSectionIndex);

        rendition.on("rendered", function (section) {
            // alert('rendered');
            var current = book.navigation && book.navigation.get(section.href);

            if (current) {
                var $select = document.getElementById("toc");
                var $selected = $select.querySelector("option[selected]");
                if ($selected) {
                    $selected.removeAttribute("selected");
                }

                var $options = $select.querySelectorAll("option");
                for (var i = 0; i < $options.length; ++i) {
                    let selected = $options[i].getAttribute("ref") === current.href;
                    if (selected) {
                        $options[i].setAttribute("selected", "");
                    }
                }
            }

        });

        rendition.on("relocated", function (location) {
            // alert('relocated');
            var next = book.package.metadata.direction === "rtl" ? document.getElementById("prev") : document.getElementById("next");
            var prev = book.package.metadata.direction === "rtl" ? document.getElementById("next") : document.getElementById("prev");

            if (location.atEnd) {
                next.style.visibility = "hidden";
            } else {
                next.style.visibility = "visible";
            }

            if (location.atStart) {
                prev.style.visibility = "hidden";
            } else {
                prev.style.visibility = "visible";
            }

        });

        rendition.on("layout", function (layout) {
            let viewer = document.getElementById("viewer");

            if (layout.spread) {
                viewer.classList.remove('single');
            } else {
                viewer.classList.add('single');
            }
        });

        window.addEventListener("unload", function () {
            book.destroy();
        });

        var navigation = document.getElementById("navigation");
        var opener = document.getElementById("opener");
        opener.addEventListener("click", function (e) {
            navigation.classList.remove("closed");
            e.preventDefault();
        }, false);

        var closer = document.getElementById("closer");
        closer.addEventListener("click", function (e) {
            navigation.classList.add("closed");
            e.preventDefault();
        }, false);

        book.loaded.navigation.then(function(toc){
            var $nav = document.getElementById("toc"),
                docfrag = document.createDocumentFragment();
            var addTocItems = function (parent, tocItems) {
              var $ul = document.createElement("ul");
              tocItems.forEach(function(chapter) {
                var item = document.createElement("li");
                var link = document.createElement("a");
                link.textContent = chapter.label;
                link.href = chapter.href;
                item.appendChild(link);
      
                if (chapter.subitems) {
                  addTocItems(item, chapter.subitems)
                }
      
                link.onclick = function(){
                  var url = link.getAttribute("href");
                  rendition.display(url);
                  navigation.classList.add("closed");
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
      
          });

        book.loaded.metadata.then(function (meta) {
            var $title = document.getElementById("title");
            var $book_title = document.getElementById("book-title");
            var $author = document.getElementById("author");
            var $cover = document.getElementById("cover");

            if ($title != null) {
                $title.textContent = meta.title;
                $book_title.textContent = meta.title;
                $author.textContent = meta.creator;
                if (book.archive) {
                    book.archive.createUrl(book.cover)
                        .then(function (url) {
                            $cover.src = url;
                        })
                } else {
                    $cover.src = book.cover;
                }
            }
        });
    }
});