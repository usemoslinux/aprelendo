<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LangX</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</head>
<body>

  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-0 col-md-3"></div>
        <?php
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $time_start = microtime(true);
        require_once('db/dbinit.php'); // connect to database
        require_once('functions.php');

        $id = mysqli_real_escape_string($con, $_GET['id']);

        $result = mysqli_query($con, "SELECT text, textTitle, textAudioURI FROM texts WHERE textID='$id'") or die(mysqli_error($con));

        $row = mysqli_fetch_assoc($result);

        echo "<div id='textdiv' class='textcontainer col-sm-12 col-md-6' data-textID='$id'>";

        if (!empty($row)) {
          echo '<h1>'.$row['textTitle'].'</h1>'; // display title

          $text = $row['text']; // display text

          echo '<div id="reader-estimated-time">' .
          estimated_reading_time($text) . ' minutes</div>'; // show estimated reading time

          $textAudioURI = $row['textAudioURI']; // if there is an audio file show audio player
          if (!empty($textAudioURI)) {
            echo '<hr>';

            echo "<audio controls id='audioplayer'>
                  <source src='$textAudioURI' type='audio/mpeg'>
                  Your browser does not support the audio element.
                  </audio>";
            echo '<form>
                  <div class="form-group flex-pbr-form">
                  <label for="pbr">Playback rate: <span id="currentpbr">1.0</span></label>
                  <input id="pbr" type="range" class="flex-pbr" value="1" min="0.5" max="2" step="0.1">
                  </div>
                  </form>';
          }

          echo '<hr>';

          $text = colorizeWords($text);
          $text = addlinks($text);
          //$text = "";
          echo addParagraphs($text); // convert /n to HTML <p>

          $time_end = microtime(true);
          $execution_time = ($time_end - $time_start);
          echo '<b>Total Execution Time:</b> '.$execution_time.' Secs';
        } else {
          header('Location: /');
        }
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

  $(window).on('keydown', function(e) {
    switch (e.keyCode) {
      case 80: // "p" keyCode
        $('#audioplayer')[0].play();
        break;
      case 83: // "s" keyCode
        $('#audioplayer')[0].pause();
        break;
    }
  });

  $(document).on('click', 'a', function(){

    var audioplayer = $('#audioplayer');

    if (audioplayer.length) { // if there is an audioplayer
      if (!audioplayer.prop('paused') && audioplayer.prop('currentTime')) {
        audioplayer.trigger('pause'); // pause audio
        playingaudio = true;
      } else {
        playingaudio = false;
      }
    }

    // show dictionary
    url = 'https://fr.m.wiktionary.org/wiki/' + this.text;
    //url = 'http://www.wordreference.com/fres/' + this.text;
    //url = 'https://glosbe.com/fr/es/' + this.text;

    $('#dicFrame').get(0).contentWindow.location.replace(url);
    // the previous line loads iframe content without adding it to browser history,
    // as this one does: $('#dicFrame').attr('src', url);
    selword = $(this);
  });

  $('#btnadd').on("click", function() {
    // add word to "words" table
    $.ajax({
      type: "POST",
      url: "db/addword.php",
      data: { word: selword.text() },
      success: function(){ // if successful, underline word
        var filter = $('a').filter(function() { return $(this).text().toLowerCase() === selword.text().toLowerCase(); });
        if (selword.parent().hasClass('word')) {
          filter.parent().replaceWith(selword);
        }
        filter.wrap("<span class='word new'></span>");
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
        var filter = $('.word').filter(function() { return $(this).text().toLowerCase() === selword.text().toLowerCase(); });

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

  $('#myModal').on('hidden.bs.modal', function () {
    var audioplayer = $('#audioplayer');
    if (playingaudio && audioplayer.length) { // if there is an audioplayer
        audioplayer.trigger("play");
    }

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

    });

    //alert(JSON.stringify(oldwords));

    $.ajax({
      type: "POST",
      url: "db/finishedreading.php",
      data: { words: oldwords, textID: $('#textdiv').attr('data-textID') },
      success: function(data) {
        //alert(data);
        window.location.replace("/");
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert("Oops! There was an error updating the database.");
      }
    });
  });

  // audio playback rate slider
  $('#pbr').on('input change', function() {
    cpbr = parseFloat($(this).val()).toFixed(1);
    $('#currentpbr').text(cpbr);
    $('#audioplayer').prop('playbackRate', cpbr);
  });


});

</script>
