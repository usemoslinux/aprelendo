<?php
/**
 * Copyright (C) 2019 Pablo Castagnino
 * 
 * This file is part of aprelendo.
 * 
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once '../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // check if logged in and set $user

use Aprelendo\Includes\Classes\User;

// only premium users are allowed to visit this page
if (!$user->isPremium()) {
    header('Location:texts.php');
    exit;
}

use Aprelendo\Includes\Classes\Reader;

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
        if (!$user->isAllowedToAccessElement('texts', (int)$_GET['id'])) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        $is_shared = isset($_GET['sh']) && $_GET['sh'] != 0 ? true : false;
        $reader = new Reader($pdo, $is_shared, $_GET['id'], $user->getId(), $user->getLangId());
        $result = '';
        $prefs = $reader->getPrefs();

        switch ($prefs->getDisplayMode()) {
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

        $styles['font-family'] = $prefs->getFontFamily();
        $styles['font-size'] = $prefs->getFontSize();
        $styles['text-align'] = $prefs->getTextAlignment();
        $styles['line-height'] = $prefs->getLineHeight();
    } else {
        throw new \Exception('Oops! There was an error trying to fetch that ebook.');
    }
} catch (Exception $e) {
    header('Location:/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Language learning platform designed to boost your reading, listening, speaking and writing skills.">
    <meta name="keywords" content="language, learning, language learning, flashcards, total reading, reading, news, ebooks, books, videos">
    <meta name="author" content="Aprelendo">
    <meta name="google-signin-client_id" content="913422235077-p01j7jbo80c7vpbesb4uuvl10vemfl13.apps.googleusercontent.com" >
    
    <link rel="shortcut icon" type="image/x-icon" href="img/favicons/favicon.ico" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
    <link rel="apple-touch-icon" sizes="57x57" href="img/favicons/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="img/favicons/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="img/favicons/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="img/favicons/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon" sizes="120x120" href="img/favicons/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon" sizes="144x144" href="img/favicons/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="img/favicons/apple-touch-icon-152x152.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicons/apple-touch-icon-180x180.png" />

    <title>Aprelendo: Learn languages with your favorite texts, ebooks and videos</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" integrity="sha512-P5MgMn1jBN01asBgU0z60Qk4QxiXo86+wlFahKrsQf37c9cro517WzVSPPV1tDKzhku2iJ2FVgL67wG03SGnNA==" crossorigin="anonymous" />

    <!-- Extra style sheets -->
    <link rel="stylesheet" type="text/css" href="css/ebooks-min.css">
    <link rel="stylesheet" type="text/css" href="css/styles-min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> 

    <!-- Font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />

    <!-- Epub.js & jszip -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js" integrity="sha384-6M0rZuK8mRhdpnt5f7OV2x+2kGHdPMTeq8E4qSbS5S4Ohq+Mcq1ZmSWQV3FdawvW" crossorigin="anonymous"></script>
    <script defer src="js/epubjs/epub.min.js"></script>

    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>

    <!-- Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js" integrity="sha512-XKa9Hemdy1Ui3KSGgJdgMyYlUg1gM+QhL6cnlyTe2qzMCYm4nAZ1PsVerQzTTXzonUR+dmswHqgJPuwCq1MaAg==" crossorigin="anonymous"></script>
</head>

<body id="readerpage" <?php echo getCSS($class, $styles); ?> >

    <div id="navigation" class="sidebar-closed">
        <h1 id="title">...</h1>
        <image id="cover" width="150px" />
        <h2 id="author">...</h2>
        <div id="toc"></div>
    </div>

    <div id="main">
        <div id="header">
            <span id="opener">
                <span id="book-title" class="book-title d-none"></span>

                <svg height="24px" id="hamburger" style="enable-background:new 0 0 32 32;" version="1.1" viewBox="0 0 32 32"
                    width="32px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <path d="M4,10h24c1.104,0,2-0.896,2-2s-0.896-2-2-2H4C2.896,6,2,6.896,2,8S2.896,10,4,10z M28,14H4c-1.104,0-2,0.896-2,2  s0.896,2,2,2h24c1.104,0,2-0.896,2-2S29.104,14,28,14z M28,22H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h24c1.104,0,2-0.896,2-2  S29.104,22,28,22z" />
                </svg>
            </span>
            <span>
                <button class="basic btn btn-secondary float-right" id="btn-save">Save & Close</button>
                <div class="loading-spinner mx-auto float-right">
                    <div></div>
                    <div></div>
                </div>
            </span>
        </div>

        <a id="prev" href="#prev" class="navlink"></a>
        <div id="viewer" class="scrolled"></div>
        <a id="next" href="#next" class="navlink"></a>

    </div>

    <?php 
        require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window
    ?>

    <script data-id="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>" defer src="js/showebook-min.js"></script>

</body>

</html>