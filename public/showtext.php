<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel='shortcut icon' type='image/x-icon' href='/images/favicon.ico' />
  <title>LangX</title>

  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <!-- Custom styles for this template -->
  <link href="css/styles.css" rel="stylesheet">

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>

<body id="readerpage"
<?php
switch ($_COOKIE['mode']) {
  case 'light':
  echo "class='lightmode'";
  break;
  case 'sepia':
  echo "class='sepiamode'";
  break;
  case 'dark':
  echo "class='darkmode'";
  break;
  default:
  break;
}
echo " style='font-family:{$_COOKIE['fontfamily']};font-size:{$_COOKIE['fontsize']};text-align:{$_COOKIE['alignment']};'";
?>
>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-0 col-md-3"></div>
    <?php
    ini_set('max_execution_time', 300); //300 seconds = 5 minutes
    $time_start = microtime(true);
    require_once('db/dbinit.php'); // connect to database
    require_once('functions.php');

    $id = mysqli_real_escape_string($con, $_GET['id']);

    $result = mysqli_query($con, "SELECT text, textTitle, textAuthor, textSourceURI, textAudioURI FROM texts WHERE textID='$id'") or die(mysqli_error($con));

    $row = mysqli_fetch_assoc($result);

    echo "<div id='container' class='col-sm-12 col-md-6' data-textID='$id'>";

    if (!empty($row)) {

      $sourceuri = $row['textSourceURI']; // display source, if available
      if (!empty($sourceuri)) {
        echo '<a class="source" href="' . $sourceuri . '">' . getHost($sourceuri) . '</a>'; 
      }
      
      echo '<h1>'.$row['textTitle'].'</h1>'; // display title

      $author = $row['textAuthor']; // display author, if available
      if (!empty($author)) {
        echo '<div class="author">' . $author . '</div>';
      }
      
      $text = $row['text']; // display text

      echo '<div id="reader-estimated-time" class="meta-data">' .
      estimatedReadingTime($text) . ' minutes</div>'; // show estimated reading time

      $textAudioURI = $row['textAudioURI']; // if there an associated audio file, show audio player
      if (!empty($textAudioURI)) {
        echo '<hr>';

        echo "<audio controls id='audioplayer'>";

        if (strcasecmp(pathinfo($textAudioURI, PATHINFO_EXTENSION), 'mp3') == 0) {
          echo "<source src='$textAudioURI' type='audio/mpeg'>";
        } else {
          echo "<source src='$textAudioURI' type='audio/ogg'>";
        }

        echo "Your browser does not support the audio element.
        </audio>";
        echo '<form>
        <div class="form-group flex-pbr-form">
        <label for="pbr">Playback rate: <span id="currentpbr">1.0</span></label>
        <input id="pbr" type="range" class="flex-pbr" value="1" min="0.5" max="2" step="0.1">
        </div>
        </form>';
      }

      echo '<hr>';

      $assistedlearning = $_COOKIE['assistedlearning'];
      if ($assistedlearning) {
        echo '<div id="alert-msg-phase" class="alert alert-info alert-dismissible show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Assisted learning - Phase 1:</strong> Reading & listening.</div>';   
      }
      
      echo '<div id="text" style="line-height:' . $_COOKIE['lineheight'] . ';">';
      
      $actlangid = $_COOKIE['actlangid'];
      $show_freq_list = isset($_COOKIE['showfreqlist']) ? $_COOKIE['showfreqlist'] : false;

      $text = colorizeWords($text, $con, $show_freq_list);
      $text = addLinks($text);
      echo addParagraphs($text); // convert /n to HTML <p>
      echo '</div>';

      $time_end = microtime(true);
      $execution_time = ($time_end - $time_start);
      echo '<b>Total Execution Time:</b> '.$execution_time.' Secs';
    } else {
      header('Location: /');
    }

    echo '<p></p>';

    if ($assistedlearning) {
      echo '<button type="button" id="btn_next_phase" class="btn btn-lg btn-primary btn-block">Go to phase 2
      <div class="small">Look up words/phrases</div></button>';
    } else {
      echo '<button type="button" id="btn_save" class="btn btn-lg btn-success btn-block">Finish & Save</button>';

      // if there is audio available & at least 1 learning word in current document
      $learningwords = strpos($text, "<span class='word learning'") || strpos($text, "<span class='new learning'");
      if (!empty($textAudioURI && $learningwords === TRUE)) {
        echo '<button type="button" id="btndictation" class="btn btn-lg btn-info btn-block">Toggle dictation on/off</button>';
      }
    }

    echo '<p></p></div>';
    ?>

    <div class="col-sm-0 col-md-3"></div>
  </div>
</div>


<!-- Modal window -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button id="btnremove" type="button" data-dismiss="modal" class="btn btn-danger">Delete</button>
        <button id="btnadd" type="button" class="btn btn-primary btn-success pull-right addbtn" data-dismiss="modal">Add</button>
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

<script type="text/javascript" src="js/showtext.js"></script>

</body>
</html>
