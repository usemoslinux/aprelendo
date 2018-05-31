$(document).ready(function () {
  $(".list-group-item").on("click", function () {
    $(".glyphicon", this)
      .toggleClass("glyphicon-chevron-right")
      .toggleClass("glyphicon-chevron-down");
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
        .append('<input type="hidden" name="art_content" value="' + htmlEscape(art_content) + '">');
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
              location.replace("../showtext.php?id=" + data.insert_id);
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