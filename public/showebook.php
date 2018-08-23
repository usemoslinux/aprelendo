<?php
require_once('db/dbinit.php');  // connect to database
require_once(PUBLIC_PATH . '/db/checklogin.php'); // check if user is logged in and set $user object
require_once(PUBLIC_PATH . '/classes/reader.php'); // load Reader class

function getCSS($class, $styles) {
    $class_str = "class='$class'";
    $style_str = " style=\"";
    foreach ($styles as $style => $value) {
        $style_str .= "$style: $value; "; 
    }
    $style_str .= "\"";
    
    return $class_str . ' ' . $style_str; 
}

$class = '';
$styles = [];

try {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        // check if user has access to view this text
        if (!$user->isAllowedToAccessElement('texts', $_GET['id'])) {
            throw new Exception ('User is not authorized to access this file.');
        }

        $is_shared = isset($_GET['sh']) && $_GET['sh'] != 0 ? true : false;
        $reader = new Reader($con, $is_shared, $_GET['id'], $user->id, $user->learning_lang_id);
        $result = '';
        
        switch ($reader->display_mode) {
            case 'light':
                $class = 'lightmode';
                break;
            case 'sepia':
                $class = 'sepiamode';
                break;
            case 'dark':
                $class = 'darkmode';
                break;
            default:
                break;
        }

        $styles['font-family'] = $reader->font_family;
        $styles['font-size'] = $reader->font_size;
        $styles['text-align'] = $reader->text_align;        
    } else {
        throw new Exception ('Oops! There was an error trying to fetch that ebook.');
    }
} catch (Exception $e) {
    header('Location:/login.php');
}

?>

    <!DOCTYPE html>
    <html <?php echo getCSS($class, []); ?> >

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel='shortcut icon' type='image/x-icon' href='images/favicon.ico' />
        <title>LangX</title>

        <!-- Epub.js & jszip -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/epubjs/dist/epub.min.js"></script>

        <!-- JQuery -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
            crossorigin="anonymous">
        <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
crossorigin="anonymous"></script> -->

        <!-- Extra style sheets -->
        <link rel="stylesheet" type="text/css" href="css/ebooks.css">
        <link rel="stylesheet" type="text/css" href="css/styles.css">
    </head>

    <body id="readerpage" <?php echo getCSS($class, $styles); ?> >
        <div></div>
        <div id="opener">
            <span id="book-title" class="book-title"></span>
            <svg height="24px" id="hamburger" style="enable-background:new 0 0 32 32;" version="1.1" viewBox="0 0 32 32" width="32px"
                xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <path d="M4,10h24c1.104,0,2-0.896,2-2s-0.896-2-2-2H4C2.896,6,2,6.896,2,8S2.896,10,4,10z M28,14H4c-1.104,0-2,0.896-2,2  s0.896,2,2,2h24c1.104,0,2-0.896,2-2S29.104,14,28,14z M28,22H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h24c1.104,0,2-0.896,2-2  S29.104,22,28,22z"
                />
            </svg>
        </div>
        <div id="viewer" class="spreads"></div>
        <a id="prev" href="#prev" class="arrow">‹</a>
        <a id="next" href="#next" class="arrow">›</a>

        <div id="navigation" class="closed">
            <div id="closer">
            <svg viewPort="0 0 15 15" version="1.1"
                xmlns="http://www.w3.org/2000/svg">
                <line x1="1" y1="11" 
                    x2="11" y2="1" 
                    stroke="black" 
                    stroke-width="2"/>
                <line x1="1" y1="1" 
                    x2="11" y2="11" 
                    stroke="black" 
                    stroke-width="2"/>
            </svg>
            </div>
            <h1 id="title">...</h1>
            <image id="cover" width="150px" />
            <h2 id="author">...</h2>
            <div id="toc"></div>
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
        <script data-id="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>" src="js/showebook.js"></script>

    </body>

    </html>