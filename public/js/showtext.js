$(document).ready(function () {
  $selword = null;
  dictionaryURI = "";
  translatorURI = "";
  prevsel = 0; // previous selection index in #selPhrase
  phase = 1; // first phase of the learning cycle

  /**
   * Sets keyboard shortcuts for media player
   * @param {event object} e Used to get keycodes
   */
  $(window).on("keydown", function (e) {
    var $audioplayer = $("#audioplayer");
    if ($audioplayer.length && e.ctrlKey) {
      switch (e.keyCode) {
        case 32: // "spacebar" keyCode
          if ($audioplayer.prop("paused")) {
            $audioplayer.trigger("play");
          } else {
            $audioplayer.trigger("pause");
          }
          break;
      }
    }
  });

  // ajax call to get dictionary & translator URIs
  $.ajax({
    url: "db/getdicuris.php",
    type: "GET",
    dataType: "json"
  }).done(function (data) {
    dictionaryURI = data.LgDict1URI;
    translatorURI = data.LgTranslatorURI;
  });

  /**
   * Sets Add & Delete buttons depending on whether selection exists in database
   */
  function setAddDeleteButtons() {
    var $btnremove = $("#btnremove");
    var $btnadd = $("#btnadd");
    if ($selword.is(".learning, .new, .forgotten, .learned")) {
      if ($btnremove.is(":visible") === false) {
        $btnremove.show();
        $btnadd.text("Forgot meaning");
      }
    } else {
      $btnremove.hide();
      $btnadd.text("Add");
    }
  }

  /**
   * Shows dictionary when user clicks a word
   * All words are enclosed in span.word tags
   */
  $(document).on("click", "span.word", function () {
    var audioplayer = $("#audioplayer");

    if (audioplayer.length) {
      // if there is audio playing
      if (!audioplayer.prop("paused") && audioplayer.prop("currentTime")) {
        audioplayer.trigger("pause"); // pause audio
        playingaudio = true;
      } else {
        playingaudio = false;
      }
    }

    $selword = $(this);

    setAddDeleteButtons();

    // show dictionary
    var url = dictionaryURI.replace("%s", encodeURIComponent($selword.text()));

    $("#dicFrame")
      .get(0)
      .contentWindow.location.replace(url);
    // the previous line loads iframe content without adding it to browser history,
    // as this one does: $('#dicFrame').attr('src', url);

    // build phrase select element in modal window
    $("#selPhrase").empty();
    $("#selPhrase").append(
      $("<option>", {
        value: $selword.text(),
        text: $selword.text()
      })
    );
    phraselength = 0;

    // chose max. 5 words. If .?! detected, then stop (it's the end of the sentence).
    $selword
      .nextAll("span")
      .slice(0, 20)
      .each(function (i, item) {
        if (
          phraselength == 5 ||
          $(item)
          .text()
          .search(/[.?!]/i) > -1
        ) {
          return false;
        } else {
          if ($(item).hasClass("word")) {
            $("#selPhrase").append(
              $("<option>", {
                value: $selword.text() +
                  $selword
                  .nextAll("span")
                  .slice(0, i + 1)
                  .text(),
                text: $selword.text() + "..." + $(item).text()
              })
            );
            phraselength++;
          }
        }
      });
    $("#selPhrase").append(
      $("<option>", {
        value: "translateparagraph",
        text: "Translate whole paragraph"
      })
    );

    prevsel = 0;
  });

  /**
   * Adds selected word or phrase to the database and underlines it in the text
   */
  $("#btnadd").on("click", function () {
    // check if selection is a word or phrase
    var selection = $("#selPhrase option:selected").val();
    var selphrase_sel_index = $("#selPhrase").prop("selectedIndex");
    var selphrase_count = $("#selPhrase option").length;
    var is_phrase =
      selphrase_sel_index > 0 && selphrase_sel_index != selphrase_count - 1;

    // add selection to "words" table
    $.ajax({
        type: "POST",
        url: "db/addword.php",
        data: {
          word: selection,
          isphrase: is_phrase
        }
      })
      .done(function () {
        // if successful, underline word or phrase
        if (is_phrase) { // if it's a phrase
          var firstword = $selword.text();
          var phraseext = selphrase_sel_index + 1;
          var filterphrase = $("span.word").filter(function () {
            return (
              $(this)
              .text()
              .toLowerCase() === firstword.toLowerCase()
            );
          });

          filterphrase.each(function () {
            var lastword = $(this)
              .nextAll("span.word")
              .slice(0, phraseext - 1)
              .last();
            var phrase = $(this)
              .nextUntil(lastword)
              .addBack()
              .next("span.word")
              .addBack();

            if (phrase.text().toLowerCase() === selection.toLowerCase()) {
              if ($(this).is('.new, .learning, .learned, .forgotten')) {
                phrase.wrapAll(
                  "<span class='word reviewing forgotten' data-toggle='modal' data-target='#myModal'></span>"
                );  
              } else {
                phrase.wrapAll(
                  "<span class='word reviewing new' data-toggle='modal' data-target='#myModal'></span>"
                );
              }
              
              phrase.contents().unwrap();
            }
          });
        } else { // if it's a word
          var filterword = $("span.word").filter(function () {
            return (
              $(this)
              .text()
              .toLowerCase() === selection.toLowerCase()
            );
          });

          filterword.each(function() {
            var $word = $(this);
            if ($word.is('.new, .learning, .learned, .forgotten')) {
              $word.html("<span class='word reviewing forgotten' data-toggle='modal' data-target='#myModal'>" +
              selection +
              "</span>");
            } else {
              $word.html("<span class='word reviewing new' data-toggle='modal' data-target='#myModal'>" +
              selection +
              "</span>");
            }
          });

          filterword.contents().unwrap();
        }
        
        // if user is in phase 2 (underlining words) and there was no previous word underlined,
        // (therefore dictation was off), when user adds his first new word, allow dictation
        if (phase==3 && $('audio').length > 0 && $('#alert-msg-phase').text().indexOf('Phase 2') > -1) {
          $('#btn_next_phase').html('Go to phase 3<div class="small">Dictation</div>');
          phase--;
        }

      })
      .fail(function (XMLHttpRequest, textStatus, errorThrown) {
        alert(
          "Oops! There was an error adding this word or phrase to the database."
        );
      });
  });

  /**
   * Remove selected word or phrase from database
   */
  $("#btnremove").on("click", function () {
    $.ajax({
        type: "POST",
        url: "db/removeword.php",
        data: {
          word: $selword.text()
        }
      })
      .done(function () {
        var filter = $("span.word").filter(function () {
          return (
            $(this)
            .text()
            .toLowerCase() === $selword.text().toLowerCase()
          );
        });

        $.ajax({
          url: "db/underlinewords.php",
          type: "POST",
          data: {
            txt: $selword.text()
          }
        }).done(function (result) {
          filter.html(result);
          filter.contents().unwrap();
          // if user is in phase 2 (underlining words) and deleted the only word that was underlined
          // don't allow phase 3 (dictation) & go directly to last phase (save changes)
          if (phase==2 && $('audio').length > 0 && $('.learning, .new, .forgotten').length == 0) {
            $('#btn_next_phase').html('Finished<div class="small">Save changes</div>');
            phase++;
          }
        });
      })
      .fail(function (XMLHttpRequest, textStatus, errorThrown) {
        alert("Oops! There was an error removing the word from the database.");
      });
  });

  /**
   * Triggers next phase of assisted learning
   * Executes when the user presses the big blue button at the end
   */
  $("#btn_next_phase").on("click", function () {
    switch (phase) {
      case 1:
        $("html, body").animate({
            scrollTop: 0
          },
          "slow"
        );
        $('#alert-msg-phase').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Assisted learning - Phase 2:</strong> Look up words/phrases.');
        if ($(".learning, .new, .forgotten").length > 0 && $("audio").length > 0) {
          $(this).html(
            'Go to phase 3<div class="small">Dictation</div>'
          );
          phase++;
        } else {
          $(this).html(
            'Finished<div class="small">Save changes</div>'
          );
          phase += 2;
        }
        break;
      case 2:
        toggleDictation();
        $(this).html(
          'Finished<div class="small">Save changes</div>'
        );
        $('#alert-msg-phase').html('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Assisted learning - Phase 3:</strong> Dictation.');
        phase++;
        break;
      case 3:
        archiveTextAndSaveWords();
        break;
      default:
        break;
    }
  });

  /**
   * Finished studying this text. Archives text & saves new status of words/phrases 
   * Executes when the user presses the big green button at the end
   */
  $('#btn_save').on('click', archiveTextAndSaveWords);

  /**
   * Archives text and updates status of all underlined words & phrases
   */
  function archiveTextAndSaveWords() {
    // build array with underlined words
    var oldwords = [];
    var ids = [];
    var word = "";
    $(".learning").each(function () {
      word = $(this)
        .text()
        .toLowerCase();
      if (jQuery.inArray(word, oldwords) == -1) {
        oldwords.push(word);
      }
    });

    ids.push($("#container").attr("data-textID")); // get text ID

    $.ajax({
      type: "POST",
      url: "db/archivetext.php",
      data: {
        words: oldwords,
        textIDs: JSON.stringify(ids),
        archivetext: true
      }
    }).done(function (data) {
        window.location.replace("texts.php");
      })
      .fail(function (XMLHttpRequest, textStatus, errorThrown) {
        alert("Oops! There was an error updating the database.");
      });
  }

  /**
   * Resumes playing if audio was paused when clicking on a word
   */
  $("#myModal").on("hidden.bs.modal", function () {
    var audioplayer = $("#audioplayer");
    if (typeof(playingaudio) != 'undefined' && playingaudio && audioplayer.length) {
      audioplayer.trigger("play");
    }
  });

  /**
   * Changes playback speed when user moves slider
   */
  $("#pbr").on("input change", function () {
    cpbr = parseFloat($(this).val()).toFixed(1);
    $("#currentpbr").text(cpbr);
    $("#audioplayer").prop("playbackRate", cpbr);
  });

  /**
   * Updates dictionary in modal window when user selects a new word/phrase
   * If user chooses to "translate the whole paragraph", the translator pops up
   */
  $("#selPhrase").on("change", function () {
    var selindex = $("#selPhrase").prop("selectedIndex");
    var trans_whole_p_index = $("#selPhrase option").length - 1;
    var url = "";

    // set Add & Delete buttons depending on whether selection exists in database
    if (selindex == 0 || selindex == trans_whole_p_index) {
      // only for the first word we need to check if it exists in db
      setAddDeleteButtons();
    } else {
      // for the rest, due to the selection method used in Langx, we can be sure
      // they are not in the database
      $("#btnremove").hide();
      $("#btnadd").text("Add");
    }

    // define behaviour when user clicks on a phrase or "translate whole paragraph"
    if (selindex == trans_whole_p_index) {
      // translate whole paragraph
      url = translatorURI.replace(
        "%s",
        encodeURIComponent($selword.parent("p").text())
      );
      var win = window.open(url);
      if (win) {
        win.focus();
      } else {
        alert(
          "Couldn't open translator window. Please allow popups for this website"
        );
      }
      $(this).prop("selectedIndex", prevsel);
    } else {
      // else, select phrase & look it up in dictionary
      phrase = $("#selPhrase option")
        .eq(selindex)
        .val();
      url = dictionaryURI.replace("%s", encodeURIComponent(phrase));
      $("#dicFrame")
        .get(0)
        .contentWindow.location.replace(url);
      prevsel = selindex;
    }
  });

  function toggleDictation() {
    if ($(".dict-answer").length == 0) {
      // toggle dictation on
      //replace all underlined words/phrases with input boxes
      $(".learning, .new, .forgotten").each(function (index, value) {
        var $elem = $(this);
        var length = $elem.text().length;
        $elem
          .hide()
          .after(
            '<div class="input-group dict-input-group"><input type="text" class="dict" ' +
            'style="width:' +
            (length + 1) * 10 +
            'px" maxlength="' +
            length +
            '" data-text="' +
            $elem.text() +
            '">' +
            '<span class="input-group-addon dict-answer hidden"></span></div>'
          );
      });
      $("html, body").animate({
          scrollTop: 0
        },
        "slow"
      ); // go back to the top of the page

      // automatically play audio, from the beginning
      var $audioplayer = $("#audioplayer");
      $audioplayer.prop("currentTime", "0");
      $audioplayer.trigger("play");

      $(":text:first").focus(); // focus first input
    } else {
      // toggle dictation off
      $(".learning, .new, .forgotten").each(function (index, value) {
        $elem = $(this);
        $elem
          .show()
          .nextAll(":lt(1)")
          .remove();
      });
      $("html, body").animate({
          scrollTop: 0
        },
        "slow"
      );
      $("#audioplayer").trigger("pause");
    }
  }

  $("#btndictation").on("click", function () {
    toggleDictation();
  });

  $("body").on("blur", ":text", function (event) {
    $curinput = $(this);
    // when user moves focus out of the input box, check if answer is correct
    // and show a cue to indicate status
    if (
      $curinput.val().toLowerCase() == $curinput.attr("data-text").toLowerCase()
    ) {
      $curinput.css("border-color", "yellowgreen");
      $curinput
        .next("span")
        .not(".hidden")
        .addClass("hidden");
    } else if ($.trim($curinput.val()) != "") {
      $curinput.css("border-color", "tomato");
      $curinput
        .next("span")
        .removeClass("hidden")
        .addClass("dict-wronganswer")
        .text($curinput.attr("data-text"));
    }
  });

  $("body").on("keydown", ".dict", function (e) {
    if (e.which === 13) {
      var index = $(".dict").index(this) + 1;
      $(".dict")
        .eq(index)
        .focus();
    }
  });
});