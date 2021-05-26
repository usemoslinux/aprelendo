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
    var player = document.querySelector('video');
    var highlighting = false;
    var $sel_start, $sel_end;
    var start_sel_time, end_sel_time;
    var start_sel_pos_top; // used in mobile devices to activate "word/phrase selection mode"
    var swiping = false; // used in mobile devices to activate "word/phrase selection mode"
    var $selword = null; // jQuery object of the selected word/phrase
    var dictionary_URI = "";
    var translator_URI = "";
    var translate_paragraph_link = "";
    var resume_video = false;
    var video_paused = false;
    var show_confirmation_dialog = true; // confirmation dialog that shows when closing window before saving data
    var gems_earned = 0;
    var doclang = $("html").attr("lang");

    // Fix for mobile devices where vh includes hidden address bar
    // First we get the viewport height and we multiple it by 1% to get a value for a vh unit
    var vh = window.innerHeight * 0.01;
    // Then we set the value in the --vh custom property to the root of the document
    document.documentElement.style.setProperty('--vh', `${vh}px`);

    // ajax call to get dictionary & translator URIs
    $.ajax({
        url: "ajax/getdicuris.php",
        type: "GET",
        dataType: "json"
    }).done(function(data) {
        if (data.error_msg == null) {
            dictionary_URI = data.dictionary_uri;
            translator_URI = data.translator_uri;
        }
    });

    /**
     * Disable right click context menu 
     */
    $(document).on("contextmenu", ".word", function(e) {
        e.preventDefault();
        return false;
    }); // end .word.on.contextmenu

    /**
     * Word/Phrase selection start
     * @param {event object} e
     */
    $(document).on("mousedown touchstart", ".word", function(e) {
        e.stopPropagation();

        video_paused = player.paused;

        // if there is video playing
        if (!video_paused) {
            player.pause();
            resume_video = true;
        }

        if (e.which < 2) {
            // if left mouse button / touch...
            highlighting = true;
            $sel_start = $sel_end = $(this);
            if (e.type == "touchstart") {
                start_sel_time = new Date();
                start_sel_pos_top = $sel_start.offset().top - $(window).scrollTop();
            }
        } else if (e.which == 3) {
            // on right click show translation of the whole sentence
            $selword = $(this);
            window.open(buildTranslateParagraphLink());
        }
    }); // end .word.on.mousedown/touchstart
    
    /**
     * Word/Phrase selection end
     * @param {event object} e
     */
    $(document).on("mouseup touchend", ".word", function(e) {
        e.stopPropagation();

        end_sel_time = new Date();

        if (e.type == "touchend") {
            if (!swiping) {
                highlighting = (end_sel_time - start_sel_time) > 1000;
            }
            $('html').css({'overflow':'visible'});    
            swiping = false;
        }

        if (highlighting) {
            if (e.which < 2) {
                // if left mouse button / touch...
                highlighting = false;
                if ($sel_start === $sel_end) {
                    $selword = $(this);
                }
                showModal();
            }
        }
    }); // end .word.on.mouseup/touchend

    /**
     * Determines if an element is after another one
     * @param {Jquery object} sel
     */
    $.fn.isAfter = function(sel) {
        return this.prevUntil(sel).length !== this.prevAll().length;
    }; // end $.fn.isAfter

    /**
     * Word/Phrase selection
     * While user drags the mouse without releasing the mouse button
     * or while touches an elements an moves the pointer without releasing
     * Here we build the selected phrase & change its background color to gray
     * @param {event object} e
     */
    $(document).on("mouseover touchmove", ".word", function(e) {
        e.stopPropagation();

        end_sel_time = new Date();

        if (e.type == "touchmove") {
            var cur_sel_pos_top = $(this).offset().top - $(window).scrollTop();
            swiping = swiping ? swiping : Math.abs(start_sel_pos_top - cur_sel_pos_top) > 0;

            if (!swiping) {
                highlighting = (end_sel_time - start_sel_time) > 1000;
            }
        } 

        if (highlighting) {
            if (e.type == "touchmove") {
                $('html').css({'overflow':'hidden'});
            }
            
            $(".word").removeClass("highlighted");

            $sel_end =
                e.type === "mouseover" ? $(this) : $(
                          document.elementFromPoint(
                              e.originalEvent.touches[0].clientX,
                              e.originalEvent.touches[0].clientY
                          )
                      );

            // if $sel_start & $sel_end are on the same line, then...
            if ($sel_start.parent().get(0) === $sel_end.parent().get(0)) {
                if ($sel_end.isAfter($sel_start)) {
                    $sel_start
                        .nextUntil($sel_end.next(), ".word")
                        .addBack()
                        .addClass("highlighted");
                    $selword = $sel_start.nextUntil($sel_end.next()).addBack();
                } else {
                    $sel_start
                        .prevUntil($sel_end.prev(), ".word")
                        .addBack()
                        .addClass("highlighted");
                    $selword = $sel_end.nextUntil($sel_start.next()).addBack();
                }
            } else {
                $sel_end = $selword = $sel_start;
            }
        }
    }); // end .word.on.mouseover/touchmove

    /**
     * Sets Add & Delete buttons depending on whether selection exists in database
     */
    function setAddDeleteButtons() {
        var $btnremove = $("#btnremove");
        var $btnadd = $("#btnadd");

        var underlined_words_in_selection = $selword.filter(
            ".learning, .new, .forgotten, .learned"
        ).length;
        var words_in_selection = $selword.filter(".word").length;

        if (words_in_selection == underlined_words_in_selection) {
            if ($btnremove.is(":visible") === false) {
                $btnremove.show();
                $btnadd.text("Forgot");
            }
        } else {
            $btnremove.hide();
            $btnadd.text("Add");
        }
    } // end setAddDeleteButtons

    /**
     * Adds selected word or phrase to the database and underlines it in the text
     */
    $("#btnadd").on("click", function() {
        var sel_text = $selword.text();
        var is_phrase = $selword.length > 1 ? 1: 0;
        // add selection to "words" table
        $.ajax({
            type: "POST",
            url: "ajax/addword.php",
            data: {
                word: sel_text.toLowerCase(),
                is_phrase: is_phrase
            }
        })
            .done(function() {
                // if successful, underline word or phrase
                if (is_phrase) {
                    // if it's a phrase
                    var firstword = $selword.eq(0).text();
                    var phraseext = $selword.filter(".word").length;
                    var $filterphrase = $("a.word").filter(function() {
                        return (
                            $(this)
                                .text()
                                .toLowerCase() === firstword.toLowerCase()
                        );
                    });

                    $filterphrase.each(function() {
                        var lastword = $(this)
                            .nextAll("a.word")
                            .slice(0, phraseext - 1)
                            .last();
                        var phrase = $(this)
                            .nextUntil(lastword)
                            .addBack()
                            .next("a.word")
                            .addBack();

                        if (
                            phrase.text().toLowerCase() ===
                            sel_text.toLowerCase()
                        ) {
                            phrase.wrapAll(
                                "<a class='word reviewing new' data-toggle='modal' data-target='#myModal'></a>"
                            );

                            phrase.contents().unwrap();
                        }
                    });
                } else {
                    // if it's a word
                    var $filterword = $("a.word").filter(function() {
                        return (
                            $(this)
                                .text()
                                .toLowerCase() === sel_text.toLowerCase()
                        );
                    });

                    $filterword.each(function() {
                        var $word = $(this);
                        if ($word.is(".new, .learning, .learned, .forgotten")) {
                            $word.wrap(
                                "<a class='word reviewing forgotten' data-toggle='modal' data-target='#myModal'></a>"
                            );
                        } else {
                            $word.wrap(
                                "<a class='word reviewing new' data-toggle='modal' data-target='#myModal'></a>"
                            );
                        }
                    });

                    $filterword.contents().unwrap();
                }
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert(
                    "Oops! There was an error adding this word or phrase to the database."
                );
            });
    }); // end #btnadd.on.click

    /**
     * Builds translator link including the paragraph to translate as a parameter
     */
    function buildTranslateParagraphLink() {
        var $start_obj = $selword.prevUntil(":contains('.')").last();
        $start_obj = $start_obj.length > 0 ? $start_obj : $selword;
        var $end_obj = $selword
            .prev()
            .nextUntil(":contains('.')")
            .last()
            .next();
        $end_obj =
            $end_obj.length > 0 ? $end_obj : $selword.nextAll().last().next();
        var $sentence = $start_obj
            .nextUntil($end_obj)
            .addBack()
            .next()
            .addBack();
        var sentence = $sentence.text().replace(/(\r\n|\n|\r)/gm, " ");

        return translator_URI.replace("%s", encodeURIComponent(sentence));
    } // end buildTranslateParagraphLink

    /**
     * Shows message for high & medium frequency words in dictionary modal window
     * @param {string} word
     * @param {string} lg_iso
     */
    function getWordFrequency(word, lg_iso) {
        var $freqlvl = $("#bdgfreqlvl");

        // ajax call to get word frequency
        $.ajax({
            type: "GET",
            url: "/ajax/getwordfreq.php",
            data: { word: word, lg_iso: lg_iso }
        }).done(function(data) {
            if (data == 0){
                $freqlvl.hide();
            } else if (data < 81) {
                $freqlvl
                    .hide()
                    .text("High frequency word")
                    .removeClass()
                    .addClass("badge badge-danger")
                    .show();
            } else if (data < 97){
                $freqlvl
                    .hide()
                    .text("Medium frequency word")
                    .removeClass()
                    .addClass("badge badge-warning")
                    .show();
            }
        }).fail(function() {
            $freqlvl.hide();
        });;
    } // end getWordFrequency

    /**
     * Updates vh value on window resize
     * Fix for mobile devices where vh includes hidden address bar
     */
     $(window).on('resize', function () {
        vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    });
        
    /**
     * Shows dictionary when user clicks a word
     * All words are enclosed in a.word tags
     */
    function showModal() {
        getWordFrequency($selword.text(), doclang);
        setAddDeleteButtons();

        $("#iframe-loader").attr('class','lds-ellipsis m-auto');
        $("#dicFrame").attr('class','d-none');

        // build translate sentence url
        translate_paragraph_link = buildTranslateParagraphLink();

        // show dictionary
        var selword_text = $selword.text().replace(/(\r\n|\n|\r)/gm, " ");
        var url = dictionary_URI.replace("%s", encodeURIComponent(selword_text));

        $(parent.document)
            .find("#dicFrame")
            .get(0)
            .contentWindow.location.replace(url);
        $("#btnadd").focus();
        // the previous line loads iframe content without adding it to browser history,
        // as this one does: $('#dicFrame').attr('src', url);

        $(parent.document)
            .find("#myModal")
            .modal("show");
    } // end showModal

    /**
     * Remove selected word or phrase from database
     */
    $("#btnremove").on("click", function() {
        $.ajax({
            type: "POST",
            url: "ajax/removeword.php",
            data: {
                word: $selword.text().toLowerCase()
            }
        })
            .done(function() {
                var $filter = $("a.word").filter(function() {
                    return (
                        $(this)
                            .text()
                            .toLowerCase() === $selword.text().toLowerCase()
                    );
                });

                // ajax call to underline text
                $.ajax({
                    type: "POST",
                    url: "/ajax/getuserwords.php",
                    data: { txt: $selword.text() },
                    dataType: "json"
                })
                .done(function(data) {
                    // if everything went fine, remove the underlining and underline once again the whole selection
                    // also, the case of the word/phrase in the text has to be respected
                    // for phrases, we need to make sure that new underlining is added for each word

                    var $result = $(underlineWords(data, doclang));
                    var $cur_filter = {};
                    var cur_word = /""/;

                    $filter.each(function() {
                        $cur_filter = $(this);

                        $result.filter(".word").each(function(key) {
                            if (langs_with_no_word_separator.includes(doclang)) {
                                cur_word = new RegExp(
                                    "(?<![^])" + $(this).text() + "(?![$])",
                                    "iug"
                                ).exec($cur_filter.text());                            } 
                            else {
                                cur_word = new RegExp(
                                    "(?<![\\p{L}|^])" + $(this).text() + "(?![\\p{L}|$])",
                                    "iug"
                                ).exec($cur_filter.text());
                            }

                            $(this).text(cur_word);
                            
                            // check if any word marked by PHP as .learning should be marked as .new instead
                            var word = $(this).text().toLowerCase();
                            var user_word = data.user_words.find(function (element) {
                                return element.word == word;    
                            });

                            if (user_word !== undefined) {
                                if (user_word.status == 2) {
                                    $(this).removeClass("learning").addClass("new");                                    
                                } else if (user_word.status == 3) {
                                    $(this).removeClass("learning").addClass("forgotten");
                                }
                            }
                        });
                        
                        $cur_filter.replaceWith($result.clone());
                    });
                })
                .fail(function(xhr, ajaxOptions, thrownError) {
                    console.log("There was an unexpected error trying to underline words in this text")
                }); // end $.ajax    
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert(
                    "Oops! There was an error removing the word from the database."
                );
            });
    }); // end #btnremove.on.click

    /**
     * Finished studying this text. Archives text & saves new status of words/phrases
     * Executes when the user presses the big green button at the end
     */
    $("#btn-save-offline-video").on("click", archiveTextAndSaveWords);

    /**
     * Archives text and updates status of all underlined words & phrases
     */
    function archiveTextAndSaveWords() {
        // build array with underlined words
        var oldwords = [];
        var ids = [];
        var word = "";
        $(".learning").each(function() {
            word = $(this)
                .text()
                .toLowerCase();
            if ($.inArray(word, oldwords) == -1) {
                oldwords.push(word);
            }
        });

        ids.push($("#text-container").attr("data-textID")); // get text ID

        $.ajax({
            type: "POST",
            url: "ajax/archivetext.php",
            data: {
                words: oldwords,
                textIDs: JSON.stringify(ids)
            }
        })
            .done(function(data) {
                if (data.error_msg == null) {
                    // update user score (gems)
                    var review_data = {
                        words : { new:       $(".reviewing.new").length, 
                                  learning:  $(".reviewing.learning").length, 
                                  forgotten: $(".reviewing.forgotten").length },
                        texts : { reviewed:  1 }
                    };

                    $.ajax({
                        type: "post",
                        url: "ajax/updateuserscore.php",
                        data: review_data
                    })
                    .done(function(data) {
                        // show text review stats
                        if (data.error_msg == null) {
                            gems_earned = data.gems_earned;
                            show_confirmation_dialog = false;
                            var url = "/textstats.php";
                            var total_words =
                                Number($(".word").length) + Number($(".phrase").length);
                            var form = $(
                                '<form action="' +
                                    url +
                                    '" method="post">' +
                                    '<input type="hidden" name="created" value="' +
                                    $(".reviewing.new").length +
                                    '" />' +
                                    '<input type="hidden" name="reviewed" value="' +
                                    $(".reviewing.learning").length +
                                    '" />' +
                                    '<input type="hidden" name="learned" value="' +
                                    $(".learned").length +
                                    '" />' +
                                    '<input type="hidden" name="forgotten" value="' +
                                    $(".reviewing.forgotten").length +
                                    '" />' +
                                    '<input type="hidden" name="total" value="' +
                                    total_words +
                                    '" />' +
                                    '<input type="hidden" name="gems_earned" value="' +
                                    gems_earned +
                                    '" />' +
                                    '<input type="hidden" name="is_shared" value="1" />' +
                                    "</form>"
                            );
                            $("body").append(form);
                            form.submit();
                        } else {
                            alert("Oops! There was an unexpected error.");
                        }
                    })
                    .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("Oops! There was an unexpected error.");
                    });
                } else {
                    alert("Oops! There was an unexpected error.");
                }
            })
            .fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Oops! There was an error updating the database.");
            });
    } // end #btn-save-offline-video.on.click

    /**
     * Resumes video when modal window is closed
     */
    $("#myModal").on("hidden.bs.modal", function() {
        if (resume_video) {
            player.play();
            resume_video = false;
        }

        // removes word selection
        $selword.removeClass("highlighted");
    }); // end #myModal.on.hidden.bs.modal

    /**
     * Hides loader spinner when dictionary iframe finished loading
     */
    $("#dicFrame").on("load", function() {
        $("#iframe-loader").attr('class','d-none');
        $(this).removeClass();
    }); // end #dicFrame.on.load()

    $("#btn-translate").on("click", function() {
        window.open(translate_paragraph_link);
    }); // end #btn-translate.on.click()

    /**
     * Removes word highlighting when user opens dictionary for word
     */
    $("#text-container").on("click", function(e) {
        e.preventDefault();
        e.stopPropagation();

        highlighting = false;
        $("#text-container")
            .find(".highlighted")
            .removeClass("highlighted");
    }); // end #text-container.on.click

    /**
     * Open video selection dialog
     */
    $("#btn-selvideo").on("click", function (e) {
        e.preventDefault();
        $("#video-file-input").trigger('click');
    });

    /**
     * After user selects video, load it
     */
    $("#video-file-input").on("change", function () {
        if (this.files[0]) {
            var file = this.files[0];    
            var type = file.type;
            if (player.canPlayType(type)) {
                var fileURL = URL.createObjectURL(file);
                player.src = fileURL;
            }
        }
    });

    /**
     * Open subtitle selection dialog
     */
     $("#btn-selsubs").on("click", function (e) {
        e.preventDefault();
        $("#subs-file-input").trigger('click');
    });

    /**
     * After user selects subs, load them
     */
    $("#subs-file-input").on("change", function () {
        if (this.files[0]) {
            var file = this.files[0];    
            const reader = new FileReader();
            var srt = '';

            reader.addEventListener('load', (event) => {
                var srt = event.target.result;
                var data = parser.fromSrt(srt, true);
                var text = '';

                for (var i = 0; i < data.length; i++) {
                    var obj = data[i];
                    var line = '<div class="text-center"';
                    
                    for (var key in obj){
                      var value = obj[key];
                      switch (key) {
                          case 'startTime':
                              line += ' data-start="' + value + '"';
                              break;
                          case 'endTime':
                              line += ' data-end="' + value + '"';
                              break;
                          case 'text':
                              line += '>' + value;
                              break;
                          default:
                              break;
                      }
                    }
                    
                    line += '</div>';
                    text += line;
                }
                
                document.getElementById('text-container').innerHTML = text;

                // ajax call to underline text
                $.ajax({
                    type: "POST",
                    url: "/ajax/getuserwords.php",
                    data: { txt: $('#text-container').html() },
                    dataType: "json"
                })
                .done(function(data) {
                    $('#text-container').html(underlineWords(data, doclang));
                })
                .fail(function(xhr, ajaxOptions, thrownError) {
                    console.log("There was an unexpected error trying to underline words in this text")
                }); // end $.ajax    
                // document.getElementById('text-container').innerText = JSON.stringify(data);
            });
            
            reader.readAsText(file);
        }
    });

    /**
     * Show reading line when video's currentTime changes
     */
    $("#video-stream").on("timeupdate", function (e) {
        var video_time = document.getElementById('video-stream').currentTime * 1000;
        var $obj = $("div.text-center", "#text-container");
        var $next_obj = $obj
                        .filter(function() {
                            return $(this).attr("data-start") < video_time;
                        })
                        .last();
        if (
            $next_obj.length > 0 &&
            !$next_obj.hasClass("video-reading-line")
        ) {
            $obj.removeClass("video-reading-line");
            $next_obj.addClass("video-reading-line");
            
            $next_obj[0].scrollIntoView({
                behavior: 'auto',
                block: 'center',
                inline: 'center'
            });
        }
    });

    /**
     * Shows dialog message reminding users to save changes before leaving
     */
    $(window).on("beforeunload", function() {
        if (show_confirmation_dialog) {
            return "To save your progress, please click the Save button before you go. Otherwise, your changes will be lost. Are you sure you want to exit this page?";
        }
    }); // end window.on.beforeunload
});
