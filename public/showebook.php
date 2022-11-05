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

use Aprelendo\Includes\Classes\Reader;

function getCSS($class, $styles)
{
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
$doclang = $user->getLang();

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
    header('Location:/login');
    exit;
}
?>

<!DOCTYPE html>
<html lang=<?php echo "\"$doclang\""; ?>>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Language learning platform designed to boost your reading, listening, speaking and writing skills.">
    <meta name="keywords"
        content="language, learning, language learning, flashcards, total reading, reading, ebooks, books, videos">
    <meta name="author" content="Aprelendo">

    <link rel="shortcut icon" type="image/x-icon" href="/img/favicons/favicon.ico" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
    <link rel="apple-touch-icon" sizes="57x57" href="/img/favicons/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="/img/favicons/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="/img/favicons/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="/img/favicons/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon" sizes="120x120" href="/img/favicons/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon" sizes="144x144" href="/img/favicons/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="/img/favicons/apple-touch-icon-152x152.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon-180x180.png" />

    <title>Aprelendo: Learn languages with your favorite texts, ebooks and videos</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.2/css/bootstrap.min.css"
        integrity="sha512-CpIKUSyh9QX2+zSdfGP+eWLx23C8Dj9/XmHjZY2uDtfkdLGo0uY12jgcnkX9vXOgYajEKb/jiw67EYm+kBf+6g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Extra style sheets -->
    <link rel="stylesheet" type="text/css" href="/css/ebooks-min.css">
    <link rel="stylesheet" type="text/css" href="/css/styles-min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Epub.js & jszip -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"
        integrity="sha384-6M0rZuK8mRhdpnt5f7OV2x+2kGHdPMTeq8E4qSbS5S4Ohq+Mcq1ZmSWQV3FdawvW" crossorigin="anonymous">
    </script>
    <script defer src="js/epubjs/epub.min.js"></script>

    <!-- JQuery -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous">
    </script>

    <!-- Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.2/js/bootstrap.min.js"
        integrity="sha512-5BqtYqlWfJemW5+v+TZUs22uigI8tXeVah5S/1Z6qBLVO7gakAOtkOzUtgq6dsIo5c0NJdmGPs0H9I+2OHUHVQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>
</head>

<body id="readerpage" <?php echo getCSS($class, $styles); ?>>

    <div class="offcanvas offcanvas-start <?php echo $class == 'darkmode' ? 'text-bg-dark' : ''; ?>"
        data-bs-scroll="true" tabindex="-1" id="navigation" aria-labelledby="navigation-title">
        <div class="offcanvas-header">
            <h1 id="title">...</h1>
            <button id="close-offcanvas" type="button"
                class="btn-close text-reset <?php echo $class == 'darkmode' ? 'btn-close-white' : ''; ?>"
                data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <image id="cover" width="150px" />
            <h2 id="author">...</h2>
            <div id="toc"></div>
        </div>
    </div>

    <div id="main">
        <div id="header">
            <span class="d-flex flex-row-reverse">
                <button class="basic btn btn-link me-n2" title="Close" id="btn-close-ebook">
                    <span class="fas fa-times"></span></button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#reader-settings-modal"
                    class="basic btn btn-sm btn-secondary me-2" title="Reader settings">
                    <span class="fas fa-cog"></span>
                </button>

                <div class="loading-spinner me-2">
                    <div class="ldio-nhngmna4s2b">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>

                <span id="book-title" class="fw-bold ms-2 me-auto my-auto"></span>

                <button id="opener" class="btn btn-link" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#navigation" aria-controls="navigation">
                    <svg height="24px" id="hamburger" style="enable-background:new 0 0 32 32;" version="1.1"
                        viewBox="0 0 32 32" width="32px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink">
                        <path
                            d="M4,10h24c1.104,0,2-0.896,2-2s-0.896-2-2-2H4C2.896,6,2,6.896,2,8S2.896,10,4,
                            10z M28,14H4c-1.104,0-2,0.896-2,2  s0.896,2,2,2h24c1.104,0,2-0.896,2-2S29.104,
                            14,28,14z M28,22H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h24c1.104,0,2-0.896,
                            2-2  S29.104,22,28,22z" />
                    </svg>
                </button>
            </span>
        </div>

        <a id="prev" href="#prev" class="navlink"></a>
        <div id="viewer" class="py-0 px-5 scrolled"></div>
        <a id="next" href="#next" class="navlink"></a>

    </div>

    <?php
        require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window
        require_once PUBLIC_PATH . 'showreadersettingsmodal.php'; // load preferences modal window
    ?>

    <script defer src="js/underlinewords-min.js"></script>
    <script defer src="js/showtext-min.js"></script>
    <script data-id="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>" defer src="js/showebook-min.js"></script>

</body>

</html>
