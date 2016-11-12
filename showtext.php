<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Show text</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </head>
  <body>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-0 col-md-3"></div>
        <div class="col-sm-12 col-md-6">
          <?php
            ini_set('max_execution_time', 300); //300 seconds = 5 minutes
            $time_start = microtime(true);
            include 'connect.php'; // connect to database

            $result = mysqli_query($con, 'SELECT text, textTitle FROM texts') or die(mysqli_error($con));

            $row = mysqli_fetch_assoc($result);

            // display title
            echo '<h1>'.$row['textTitle'].'</h1>';

            // display text
            $text = $row['text'];

            $text = colorizeWords($text);
            $text = addlinks($text);
            echo addParagraphs($text);

            function addParagraphs($text) // Add paragraph elements to text
            {
                $lf = chr(10);
                $text = preg_replace('/\n/', '</p><p>', $text);
                $text = '<p>'.$text.'</p>';

                return $text;
            }

            function addlinks($text)
            {
                $text = preg_replace('/\w+(?![^<]*>)/iu',
              "<a href='' data-toggle='modal' data-target='#myModal'>$0</a>", $text);

                return $text;
            }

            function colorizeWords($text)
            {
                include 'connect.php'; // connect to database

              $result = mysqli_query($con, 'SELECT word, wordStatus, wordTranslation FROM words') or die(mysqli_error($con));
                while ($row = mysqli_fetch_assoc($result)) {
                    $word = $row['word'];
                    $lvlno = $row['wordStatus'];
                    $tr = $row['wordTranslation'];

                    // preg_replace + regex is used to replace complete words
                    // this method also respects the original case
                    $text = preg_replace("/\b".$word."\b(?![^<]*>)/ui",
                    "<span class='word lvl{$lvlno}' data-toggle='tooltip' title='".$tr."'>$0</span>", "$text");
                }

                return $text;
            }

            function colorizeWords2($text)
            {
              include 'connect.php'; // connect to database
              $result = mysqli_query($con, 'SELECT word, wordStatus, wordTranslation FROM words') or die(mysqli_error($con));
              while ($row = $result->fetch_assoc()) {
                $array[] = $row;
              }


              $claves = preg_split("/[\s,’']+/iu", $text, -1, PREG_SPLIT_NO_EMPTY);
              foreach ($claves as $value) {
                 foreach ($array as $line) {

                  if (strcasecmp($line["word"], $value) == 0) {
                    $text = preg_replace("/\b".$value."\b(?![^<]*>)/ui",
                         "<span class='word lvl" . $line["wordStatus"] . "' data-toggle='tooltip' title='" . $line["wordTranslation"] . "'>$0</span>", "$text");
                    break;
                  }
                }
              }
              return $text;
            }

            $time_end = microtime(true);
            $execution_time = ($time_end - $time_start);
            echo '<b>Total Execution Time:</b> '.$execution_time.' Secs';
           ?>
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
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add word</h4>
          </div>
          <div class="modal-body">
            <?php include 'addword.php'; ?>
          </div>
        </div>
      </div>
    </div>


    <script>
      // when clicking on a word, show its definition as a tooltip
      $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
      });

      $(document).on("click", "a", function(){

        // if word already exists in db, load data accordingly into form
        $.ajax({
          method: "POST",
          url: "checkword.php",
          data: { word: "nouvelle" },
          success: function(data) {
            //alert(jQuery.parseJSON(data));
            var obj = jQuery.parseJSON(data);
            $('#word').val(obj.word);
            $('#translation').val(obj.wordTranslation);
            //$('#image').val('');
            $('#tags').val(obj.tags);
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("Oops! There was an error processing your request. Please try again later.");
          }
        });


        //$('#word').val( $(this).text() ); // if word is not already on the db
      });
    </script>

  </body>
</html>
