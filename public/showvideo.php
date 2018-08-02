<!DOCTYPE html>
<html id="html-video" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel='shortcut icon' type='image/x-icon' href='images/favicon.ico' />
    <title>LangX</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
        crossorigin="anonymous">
    <!-- Custom styles for this template -->
    <link href="css/styles.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
</head>

<body id="readerpage"
<?php
require_once('db/dbinit.php');  // connect to database
require_once(PUBLIC_PATH . '/db/checklogin.php'); // check if user is logged in and set $user object
require_once(PUBLIC_PATH . '/classes/reader.php'); // load Reader class
require_once(PUBLIC_PATH . '/classes/videos.php'); // load Reader class

try {
    $id_is_set = isset($_GET['id']) && !empty($_GET['id']);
    if ($id_is_set) {
        $reader = new Reader($con, $_GET['id'], $user->id, $user->learning_lang_id);
        
        $video = new Videos($con, $user->id, $user->learning_lang_id);
        $video_row = $video->getById($_GET['id']);
        $yt_id = $video->extractYTId($video_row['textSourceURI']);

        switch ($reader->display_mode) {
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
        $font_family = $reader->font_family;
        $font_size = $reader->font_size;
        $text_align = $reader->text_align;
        
        echo " style='font-family:$font_family;font-size:$font_size;text-align:$text_align;>'";
    } else {
        throw new Exception ('>Oops! There was an error trying to fetch that video.');
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
>

        <div class="container-fluid">
            <div class="row">
                <?php
                    if (isset($reader)) {
                        echo $reader->showVideo($yt_id);
                    }
                ?>
            </div>
        </div>

        <!-- Modal window -->
        <div id="myModal" class="modal fade" data-keyboard="true" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button id="btnremove" type="button" data-dismiss="modal" class="btn btn-danger">Delete</button>
                        <button id="btncancel" type="button" data-dismiss="modal" class="btn btn-default">Cancel</button>
                        <button id="btnadd" type="button" class="btn btn-primary btn-success pull-right addbtn" data-dismiss="modal">Add</button>
                        <select class="modal-selPhrase" name="selPhrase" id="selPhrase">
                            <option value="translate_sentence">Translate sentence</option>
                        </select>
                    </div>
                    <div class="modal-body" id="definitions">
                        <iframe id="dicFrame" style="width:100%;" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="js/showvideo.js"></script>

</body>

<script src="js/ytvideo.js"></script>

</html>