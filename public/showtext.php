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
                estimatedReadingTime($text) . ' minutes</div>'; // show estimated reading time

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

                $text = colorizeWords($text, $con);
                $text = addLinks($text);
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
                <select class="modal-selPhrase" name="selPhrase" id="selPhrase">
                    <option value="translateparagraph">Translate whole paragraph</option>
                </select>
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

    $(document).on('click', 'span.word', function(){

        var audioplayer = $('#audioplayer');

        if (audioplayer.length) { // if there is audio playing
            if (!audioplayer.prop('paused') && audioplayer.prop('currentTime')) {
                audioplayer.trigger('pause'); // pause audio
                playingaudio = true;
            } else {
                playingaudio = false;
            }
        }

        // show dictionary
        var url = 'https://fr.m.wiktionary.org/wiki/' + $(this).text();
        //url = 'http://www.wordreference.com/fres/' + this.text;
        //url = 'https://glosbe.com/fr/es/' + this.text;

        $('#dicFrame').get(0).contentWindow.location.replace(url);
        // the previous line loads iframe content without adding it to browser history,
        // as this one does: $('#dicFrame').attr('src', url);
        selword = $(this);

        // build phrase select element in modal window
        $('#selPhrase').empty();
        $('#selPhrase').append($('<option>', {
            value: selword.text(),
            text: selword.text()
        }));
        phraselength = 0;
        selword.nextAll('span').slice(0,20).each(function(i, item){
            if (phraselength == 5 || $(item).text().search('.') > 0) {
                return false;
            } else {
                if ($(item).hasClass('word')) {
                    $('#selPhrase').append($('<option>', {
                        value: selword.text() + selword.nextAll('span').slice(0,i+1).text(),
                        text: selword.text() + '...' + $(item).text()
                    }));
                    phraselength++;
                }
            }
        });
        $('#selPhrase').append($('<option>', {
            value: 'translateparagraph',
            text: 'Translate whole paragraph'
        }));

    });

    // https://jsfiddle.net/kLcczz15/90/
    $('#btnadd').on('click', function() {
        // check if selection is a word or phrase
        var selection = $('#selPhrase option:selected').val();
        var selphrase_sel_index = $('#selPhrase').prop('selectedIndex');
        var selphrase_count = $('#selPhrase option').length;
        var is_phrase = selphrase_sel_index > 0 && selphrase_sel_index != selphrase_count-1;

        // add selection to "words" table
        $.ajax({
          type: 'POST',
          url: 'db/addword.php',
          data: { word: selection, isphrase: is_phrase },
          success: function(){ // if successful, underline word or phrase
            if (is_phrase) {
                var firstword = selword.text();
                var phraseext = selphrase_sel_index + 1;
                var filterphrase = $('span.word').filter(function() { return $(this).text().toLowerCase() === firstword.toLowerCase(); });

                filterphrase.each(function() {
                    var lastword = $(this).nextAll('span.word').slice(0,phraseext-1).last();
                    var phrase = $(this).nextUntil(lastword).addBack().next('span.word').addBack();

                    if(phrase.text().toLowerCase() === selection.toLowerCase()) {
                        phrase.wrapAll("<span class='word new' data-toggle='modal' data-target='#myModal'></span>");
                        phrase.contents().unwrap();
                    }
                });
            } else {
                var filterword = $('span.word').filter(function() { return $(this).text().toLowerCase() === selection.toLowerCase(); });

                filterword.html("<span class='word new' data-toggle='modal' data-target='#myModal'>" + selection + "</span>");
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Oops! There was an error adding this word or phrase to the database.");
          }
        });

    });

    $('#btnremove').on('click', function(){
        $.ajax({
            type: 'POST',
            url: 'db/removeword.php',
            data: { word: selword.text() },
            success: function(){
                var filter = $('span.word').filter(function() {
                    return $(this).text().toLowerCase() === selword.text().toLowerCase();
                });

                alert(selword.text());
                $.ajax({
                    url: 'underlinewords.php',
                    type: 'POST',
                    data: {txt: selword.text()}
                })
                .done(function(result) {
                    //alert('success: html result: ' + result + "\nword: " + selword.text());
                    filter.html(result);
                    filter.contents().unwrap();
                });
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Oops! There was an error removing the word from the database.");
            }
        });


    });

    $('#myModal').on('hidden.bs.modal', function () {
        // if user was playing audio, resume playback
        var audioplayer = $('#audioplayer');
        if (playingaudio && audioplayer.length) {
            audioplayer.trigger("play");
        }

    });

    $('#btnfinished').on('click', function() {
        // build array with underlined words
        var oldwords = [];
        var word = "";
        $('.learning').each(function(){
            word = $(this).text().toLowerCase();
            if (jQuery.inArray(word, oldwords) == -1) {
                oldwords.push(word);
            }

        });

        $.ajax({
            type: 'POST',
            url: 'db/finishedreading.php',
            data: { words: oldwords, textID: $('#textdiv').attr('data-textID') },
            success: function(data) {
                window.location.replace('/');
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

    $('#selPhrase').on('change', function(e) {
        var selindex = $('#selPhrase').prop('selectedIndex');
        var url = '';

        if (selindex == $('#selPhrase option').length-1) { // translate whole paragraph
            url = 'https://translate.google.com/#fr/es/' + selword.parent('p').text() ;
            var win = window.open(url);
            if (win) {
                win.focus();
            } else {
                alert("Couldn't open translator window. Please allow popups for this website");
            }
        } else { // else, select phrase & look it up in dictionary
            phrase = $('#selPhrase option').eq(selindex).val();
            url = 'https://fr.m.wiktionary.org/wiki/' + phrase;
            $('#dicFrame').get(0).contentWindow.location.replace(url);
        }
    });


});

</script>
