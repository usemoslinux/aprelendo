/**
 * Copyright (C) 2018 Pablo Castagnino
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

$(document).ready(function () {
    var ebook_id = $("script[src*='showebook.js']").attr('data-id');
    var book = ePub();

    var formData = new FormData();
    formData.append('id', ebook_id);

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
    }

    /**
     * Ajax call to fetch an ebook. Response has to be converted to arrayBuffer to allow 
     * epub.js (book.open function) to process it correctly
     */
    fetch('ajax/getebook.php', {
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
            flow: "scrolled-doc"
        });

        // theming
        var reader = document.getElementById('readerpage');

        rendition.themes.register("darkmode", "/css/ebooks.css");
        rendition.themes.register("lightmode", "/css/ebooks.css");
        rendition.themes.register("sepiamode", "/css/ebooks.css");

        rendition.themes.default({
            body: {
                'font-family': reader.style.fontFamily + ' !important'
            },
            p: {
                'font-size': reader.style.fontSize + ' !important',
                'text-align': reader.style.textAlign + ' !important',
                'line-height': reader.style.lineHeight + ' !important'
            }
        });

        rendition.themes.select(reader.className);

        book.ready.then(function () {
            var key = book.key()+'-lastpos';
            var last_pos = localStorage.getItem(key);
            if (last_pos) {
                rendition.display(last_pos);
            }
            else {
                var hash = window.location.hash.slice(2);
                rendition.display(hash || undefined);
            }
            
            rendition.hooks.content.register(function (contents) {
                
                // Add JQuery
                contents.addScript("https://code.jquery.com/jquery-3.3.1.min.js")
                    .then(function () {
                        // Add the rest of the scripts and stylesheets
                        Promise.all([
                            contents.addScript("/js/showtext.js"),
                            contents.addScript("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js")
                        ]);

                        $('.loader').fadeIn(1000);
                        // underline words
                        $.ajax({
                            type: "POST",
                            url: "../ajax/underlinewords.php",
                            data: {
                                txt: contents.content.innerHTML
                            }
                        })
                        .done(function (result) {
                            contents.content.innerHTML = result;
                            $('.loader').fadeOut(1000);
                        })
                        .fail(function (xhr, ajaxOptions, thrownError) {
                            alert('There was an unexpected error when trying to underline words for this ebook!');
                        });
                    });


            });

            var next = document.getElementById("next");
            next.addEventListener("click", function(e){
                e.preventDefault();
                $.when( SaveWords() ).then(function() {
                    rendition.next();
                });
            }, false);

            var prev = document.getElementById("prev");
            prev.addEventListener("click", function(e){
                e.preventDefault();
                $.when( SaveWords() ).then(function() {
                    rendition.prev();
                });
            }, false);
           
        })

        rendition.on("rendered", function(section){
            var nextSection = section.next();
            var prevSection = section.prev();
      
            if(nextSection) {
              nextNav = book.navigation.get(nextSection.href);
      
              if(nextNav) {
                nextLabel = nextNav.label;
              } else {
                nextLabel = "next";
              }
      
              next.textContent = nextLabel + " »";
            } else {
              next.textContent = "";
            }
      
            if(prevSection) {
              prevNav = book.navigation.get(prevSection.href);
      
              if(prevNav) {
                prevLabel = prevNav.label;
              } else {
                prevLabel = "previous";
              }
      
              prev.textContent = "« " + prevLabel;
            } else {
              prev.textContent = "";
            }
      
          });

        $('body').on('click', '#btn-save', function() {
            var cfi = rendition.currentLocation().start.cfi;

            localStorage.setItem(book.key()+'-lastpos', cfi);

            window.location.replace('texts.php');
        });

        /**
         * Updates status of all underlined words & phrases
         */
        function SaveWords() {
            // build array with underlined words
            var $pagereader = $(document).find('iframe[id^="epubjs"]');
            var oldwords = [];
            var word = "";
            

            $pagereader.contents().find(".learning").each(function () {
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
                    data: {
                        words: oldwords
                    }
                })
                .fail(function (XMLHttpRequest, textStatus, errorThrown) {
                    alert("Oops! There was an error updating the database.");
                });
        }

        window.addEventListener("beforeunload", function () {
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