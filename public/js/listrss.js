$(document).ready(function() {
  $(".list-group-item").on("click", function() {
    $(".glyphicon", this)
      .toggleClass("glyphicon-chevron-right")
      .toggleClass("glyphicon-chevron-down");
  });

  function addTextToLibrary($entry_info, $entry_text, readlater) {
    var art_title = $.trim($entry_info.text());
    var art_author = $entry_info.attr("data-author");
    var art_link = $entry_info.attr("data-src");
    var art_pubdate = $entry_info.attr("data-pubdate");
    var art_content = "";

    $entry_text.children("p").each(function() {
      art_content += $(this).text() + "\n";
    });
    art_content = $.trim(art_content);

    $.ajax({
      type: "POST",
      url: "db/addtext.php",
      dataType: "JSON",
      data: {
        title: art_title,
        author: art_author,
        link: art_link,
        pubdate: art_pubdate,
        content: art_content
      }
    }).done(function(data) {
        switch (readlater) {
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
          case "addaudio":
            location.replace("../addtext.php?id=" + data.insert_id);
            break;
          default:
            break;
        }
      })
      .fail(function() {
        $entry_text
          .find("span.message")
          .addClass("text-failure")
          .text("There was an error trying to add this text to your library!")
          .show()
          .fadeOut(2000);
      });
  }

  $(".btn-readlater").on("click", function(e) {
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

  $(".btn-readnow").on("click", function(e) {
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

  $(".btn-addsound").on("click", function(e) {
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
