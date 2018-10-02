<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel='shortcut icon' type='image/x-icon' href='images/logo.svg' />
    <title>Aprelendo</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
        crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="css/styles.css" rel="stylesheet">
    
    <!-- JQuery & Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
    
</head>

<body id="readerpage"
<?php
require_once('db/dbinit.php');  // connect to database
require_once(PUBLIC_PATH . '/db/checklogin.php'); // check if user is logged in and set $user object
require_once(PUBLIC_PATH . '/classes/reader.php'); // load Reader class

try {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        // check if user has access to view this text
        $table = isset($_GET['sh']) && $_GET['sh'] != 0 ? 'sharedtexts' : 'texts';
        if (!$user->isAllowedToAccessElement($table, $_GET['id'])) {
            throw new Exception ('User is not authorized to access this file.');
        }

        $is_shared = $table == 'sharedtexts' ? true : false;
        $reader = new Reader($con, $is_shared, $_GET['id'], $user->id, $user->learning_lang_id);
        
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
        throw new Exception ('>Oops! There was an error trying to fetch that text.');
    }
} catch (Exception $e) {
    header('Location:/login.php');
    exit;
}
?>
>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-6 col-md-offset-3">
                <?php
                    echo $reader->showText();
                    if ($is_shared) {
                        echo '<input type="hidden" id="is_shared">';
                    }
                ?>
                </div>
                
            </div>
        </div>

        <!-- Modal window -->
        <div id="myModal" class="modal fade" data-keyboard="true" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button id="btnremove" type="button" data-dismiss="modal" class="btn btn-danger">Delete</button>
                        <button id="btnadd" type="button" class="btn btn-primary btn-success pull-right add-btn" data-dismiss="modal">Add</button>
                        <button id="btncancel" type="button" data-dismiss="modal" class="btn btn-static pull-right cancel-btn">Cancel</button>
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

        <script type="text/javascript" src="js/showtext.js"></script>

</body>

</html>