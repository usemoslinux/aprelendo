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
  $(".list-group-item").on("click", function () {
    $(".fas", this)
      .toggleClass("fas fa-chevron-right")
      .toggleClass("fas fa-chevron-down");
  });

  function htmlEscape(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
  }

  function addTextToLibrary($entry_info, $entry_text, add_mode) {
    var art_title = $.trim($entry_info.text());
    var art_author = $entry_info.attr("data-author");
    var art_url = $entry_info.attr("data-src");
    var art_pubdate = $entry_info.attr("data-pubdate");
    var art_content = "";

    $entry_text.children("p").each(function () {
      art_content += $(this).text() + "\n";
    });
    art_content = $.trim(art_content);

    if (add_mode == 'addaudio') {
      var form = $('<form id="form_add_audio" action="../addtext.php" method="post"></form>')
        .append('<input type="hidden" name="art_title" value="' + htmlEscape(art_title) + '">')
        .append('<input type="hidden" name="art_author" value="' + htmlEscape(art_author) + '">')
        .append('<input type="hidden" name="art_url" value="' + htmlEscape(art_url) + '">')
        .append('<input type="hidden" name="art_pubdate" value="' + htmlEscape(art_pubdate) + '">')
        .append('<input type="hidden" name="art_content" value="' + htmlEscape(art_content) + '">')
        .append('<input type="hidden" name="art_is_shared" value="true">');
      $('body').append(form);
      form.submit();
    } else {
      $.ajax({
          type: "POST",
          url: "db/addtext.php",
          dataType: "JSON",
          data: {
            title: art_title,
            author: art_author,
            url: art_url,
            pubdate: art_pubdate,
            text: art_content,
            mode: 'rss'
          }
        }).done(function (data) {
          switch (add_mode) {
            case "readlater":
              $entry_text
                .find("button")
                .remove()
                .end()
                .find("span.message")
                .addClass("text-success")
                .text("Text was successfully added to your library")
                .show()
                .fadeOut(2000);
              break;
            case "readnow":
              location.replace("../showtext.php?id=" + data.insert_id + "&sh=1");
              break;
            default:
              break;
          }
        })
        .fail(function () {
          $entry_text
            .find("span.message")
            .addClass("text-failure")
            .text("There was an error trying to add this text to your library!")
            .show()
            .fadeOut(2000);
        });
    }
  }

  $(".btn-readlater").on("click", function (e) {
    e.preventDefault();
    e.stopPropagation();
    addTextToLibrary(
      $(this)
      .closest(".entry-text")
      .parent()
      .prev(".entry-info"),
      $(this).closest(".entry-text"),
      "readlater"
    );
  });

  $(".btn-readnow").on("click", function (e) {
    e.preventDefault();
    e.stopPropagation();
    addTextToLibrary(
      $(this)
      .closest(".entry-text")
      .parent()
      .prev(".entry-info"),
      $(this).closest(".entry-text"),
      "readnow"
    );
  });

  $(".btn-addsound").on("click", function (e) {
    e.preventDefault();
    e.stopPropagation();
    addTextToLibrary(
      $(this)
      .closest(".entry-text")
      .parent()
      .prev(".entry-info"),
      $(this).closest(".entry-text"),
      "addaudio"
    );
  });

});