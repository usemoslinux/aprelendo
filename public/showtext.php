<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LangX</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</head>
<body>

  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-0 col-md-3"></div>
      <div id="textdiv" class="col-sm-12 col-md-6">
        <?php
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $time_start = microtime(true);
        require_once('db/dbinit.php'); // connect to database
        require_once('functions.php');

        $id = mysqli_real_escape_string($con, $_GET['id']);

        $result = mysqli_query($con, "SELECT text, textTitle FROM texts WHERE textID='$id'") or die(mysqli_error($con));

        $row = mysqli_fetch_assoc($result);

        echo '<h1>'.$row['textTitle'].'</h1>'; // display title

        $text = $row['text']; // display text

        echo '<div id="reader-estimated-time">' .
              estimated_reading_time($text) . ' minutes</div>';

        echo '<hr>';

        $text = colorizeWords($text);
        $text = addlinks($text);
        //$text = "";
        echo addParagraphs($text); // convert /n to HTML <p>

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        echo '<b>Total Execution Time:</b> '.$execution_time.' Secs';
        ?>
        <p></p>
        <button type="button" id="btnfinished" class="btn btn-lg btn-success btn-block">Finished reading</button>
        <p></p>
      </div>
      <div class="col-sm-0 col-md-3"></div>
    </div>
  </div>


  <!-- Modal window -->
  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button id="btnremove" type="button" data-dismiss="modal" class="btn btn-danger">Forget</button>
          <button id="btnadd" type="button" class="btn btn-primary btn-success pull-right" data-dismiss="modal">Add</button>
        </div>
        <div class="modal-body" id="definitions">
          <iframe id="dicFrame" style="width:100%;" frameborder="0"></iframe>
        </div>
      </div>
    </div>
  </div>

</body>
</html>


<script>

$(document).ready(function() {

  selword = null;

  $(document).on("click", "a", function(){
    // show dictionary
    url = 'https://fr.m.wiktionary.org/wiki/' + this.text;
    //url = 'http://www.wordreference.com/fres/' + this.text;
    //url = 'https://glosbe.com/fr/es/' + this.text;

    $('#dicFrame').attr('height', $(window).height()-150);
    $('#dicFrame').attr('src', url);
    selword = $(this);
  });

  $('#btnadd').on("click", function() {
    // add word to "words" table
    $.ajax({
      type: "POST",
      url: "db/addword.php",
      data: { word: selword.text() },
      success: function(){ // if successful, underline word
        var filter = $('a').filter(function() { return $(this).text().toLowerCase() === selword.text().toLowerCase() });
        if (selword.parent().hasClass('word')) {
          filter.parent().replaceWith(selword);
        }
        filter.wrap("<span class='word new'></span>");
        // var element = selword;
        // var word = element.text().toLowerCase();
        // $('a').each(function(){
        //   var linkword = selword.text().toLowerCase();
        //   if (word == linkword) {
        //     // remove old underlining if it already exists
        //     if (selword.parent().hasClass('word')) {
        //       selword.parent().replaceWith(selword);
        //     }
        //     // add 'new' underlining
        //     selword.wrap("<span class='word new'></span>");
        //   }
        // });
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Oops! There was an error adding the word to the database.");
      }
    });
  });

  $('#btnremove').on('click', function(){
    $.ajax({
      type: "POST",
      url: "db/removeword.php",
      data: { word: selword.text() },
      success: function(){
        var filter = $('.word').filter(function() { return $(this).text().toLowerCase() === selword.text().toLowerCase() });
        //var filter = '.learning:contains(' + selword.text() + ')';

        if (selword.parent().hasClass('word learning')) {
          filter.removeClass('word learning');
        } else if (selword.parent().hasClass('word new')) {
          filter.removeClass('word new');
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Oops! There was an error removing the word from the database.");
      }

    });
  });

  $('#btnfinished').on("click", function() {
    // build array with underlined words
    var oldwords = [];
    var word = "";
    $('.learning').each(function(){
      word = $(this).text().toLowerCase();
      if (jQuery.inArray(word, oldwords) == -1) {
        oldwords.push(word);
      }

    })

    // alert(JSON.stringify(res));

    $.ajax({
      type: "POST",
      url: "finishedreading.php",
      data: { words: oldwords },
      success: function(data) {
        //alert(data);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Oops! There was an error updating the database.");
      }
    });
  });

});

</script>
