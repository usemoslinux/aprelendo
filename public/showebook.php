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

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

use Aprelendo\Reader;
use Aprelendo\Texts;
use Aprelendo\TextsUtilities;
use Aprelendo\UserException;

function getCSS($styles)
{
    // styles for P
    $style_str = "#viewer p {";
    foreach ($styles as $style => $value) {
        $style_str .= "$style: $value !important; ";
    }
    $style_str .= "margin: 0 0 1em 0 !important; padding: 0 !important; text-indent:1.5em !important;} ";
    
    // styles for headers
    $style_str .= "#viewer h1, #viewer h2, #viewer h3, #viewer h4, #viewer h5, #viewer h6 {";

    foreach ($styles as $style => $value) {
        if ($style !== "text-align") {
            $style_str .= "$style: $value !important; ";
        }
    }
    $style_str .= "text-align:center !important;
        margin: 0 0 1em 0 !important;
        padding: 2.5em 0 !important;
        text-indent:0 !important;}";
    
    return $style_str;
}

$class = '';
$styles = [];
$doclang = $user->lang;

try {
    if (!empty($_GET['id'])) {
        // check if user has access to view this text
        if (!$user->isAllowedToAccessElement('texts', (int)$_GET['id'])) {
            http_response_code(403);
            exit;
        }

        $is_shared = isset($_GET['sh']) && $_GET['sh'] != 0 ? true : false;
        $reader = new Reader($pdo, $user->id, $user->lang_id, $_GET['id'], $is_shared);
        $result = '';

        // get user preferences & load classes and CSS for ebook
        $prefs = $reader->prefs;

        switch ($prefs->display_mode) {
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

        $styles['font-family'] = $prefs->font_family;
        $styles['font-size'] = $prefs->font_size;
        $styles['text-align'] = $prefs->text_alignment;
        $styles['line-height'] = $prefs->line_height;
    } else {
        throw new UserException('Oops! There was an unexpected error trying to fetch that ebook.');
    }
} catch (Exception $e) {
    header('Location:/login');
    exit;
}

// get audio uri, if any
$text = new Texts($pdo, $user->id, $user->lang_id);
$text->loadRecord($_GET['id']);
$audio_uri = TextsUtilities::getAudioUriForEmbbeding($text->audio_uri);

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

    <link rel="shortcut icon" type="image/x-icon" href="/img/favicons/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="57x57" href="/img/favicons/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/img/favicons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/favicons/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/img/favicons/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/img/favicons/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/img/favicons/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/img/favicons/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon-180x180.png">

    <title>Aprelendo: Learn languages with your favorite texts, ebooks and videos</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM"
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Extra style sheets -->
    <link rel="stylesheet" type="text/css" href="/css/ebooks.min.css">
    <link rel="stylesheet" type="text/css" href="/css/styles.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css"
        integrity="sha512-D1liES3uvDpPrgk7vXR/hR/sukGn7EtDWEyvpdLsyalQYq6v6YUsTUJmku7B4rcuQ21rf0UTksw2i/2Pdjbd3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        
    <!-- Epub.js & jszip -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"
        integrity="sha384-+mbV2IY1Zk/X1p/nWllGySJSUN8uMs+gUAN10Or95UBH0fpj6GfKgPmgC5EXieXG"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>
    <script defer src="/js/epubjs/epub.min.js"></script>

    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
        integrity="sha384-NXgwF8Kv9SSAr+jemKKcbvQsz+teULH/a5UNJvZc6kP47hZgl62M1vGnw6gHQhb1"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>

    <!-- Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"
        integrity="sha512-VK2zcvntEufaimc+efOYi622VN5ZacdnufnmX7zIhCPmjhKnOi9ZDMtg1/ug5l183f19gG1/cBstPO4D8N/Img=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>

    <style id="userstyles">
        <?php echo getCSS($styles); ?>
    </style>
</head>

<body id="readerpage" <?php echo ' class="'. $class . '"' ?>>

    <div class="offcanvas offcanvas-start <?php echo $class == 'darkmode' ? 'text-bg-dark' : ''; ?>"
        data-bs-scroll="true" tabindex="-1" id="navigation" aria-labelledby="navigation-title">
        <div class="offcanvas-header">
            <h1 id="title">...</h1>
            <button id="close-offcanvas" type="button"
                class="btn-close <?php echo $class == 'darkmode' ? 'btn-close-white' : ''; ?>"
                data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <image id="cover" width="150px">
            <h2 id="author">...</h2>
            <div id="toc"></div>
        </div>
        <?php if(!empty($audio_uri)): ?>
            <audio id="audioplayer" controls>
                <source id="audioplayer" src="<?php echo $audio_uri; ?>" />
                <p>Your browser doesn't support the audio HTML tag.</p>
            </audio>
        <?php endif; ?>
    </div>

    <div id="main">
        <div id="header">
            <span class="d-flex flex-row-reverse">
                <button id="btn-close-ebook" type="button" data-bs-toggle="tooltip"
                    data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                    data-bs-title="Close &amp; Save reading position"
                    aria-label="Close" class="btn-close">
                </button>
                <span data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                    data-bs-placement="bottom" data-bs-title="Reader settings">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#reader-settings-modal"
                        class="btn btn-sm btn-secondary me-2">
                        <span class="bi bi-gear-fill"></span>
                    </button>
                </span>

                <div class="loading-spinner me-2">
                    <div class="ldio-nhngmna4s2b">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>

                <span id="book-title-chapter" class="fw-bold pe-2 me-auto my-auto text-truncate"></span>
                <span id="book-title" class="fw-bold ms-2 me-2 my-auto text-nowrap text-truncate"></span>

                <button id="opener" class="btn btn-link" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#navigation" aria-controls="navigation">
                    <svg height="28px" id="hamburger" style="enable-background:new 0 0 32 32;" version="1.1"
                        viewBox="0 0 32 32" width="28px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink">
                        <path
                            d="M4,10h24c1.104,0,2-0.896,2-2s-0.896-2-2-2H4C2.896,6,2,6.896,2,8S2.896,10,4,
                            10z M28,14H4c-1.104,0-2,0.896-2,2  s0.896,2,2,2h24c1.104,0,2-0.896,2-2S29.104,
                            14,28,14z M28,22H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h24c1.104,0,2-0.896,
                            2-2  S29.104,22,28,22z">
                    </svg>
                </button>
            </span>
        </div>

        <div class="navlink">
            <a id="prev" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                data-bs-placement="bottom" href="#prev"></a>
        </div>
        <div id="viewer" class="py-0 px-5 scrolled"
            data-idText="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
        </div>
        <div class="navlink">
            <a id="next" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                data-bs-placement="top" href="#next"></a>
        </div>
    </div>

    <?php
        require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window
        require_once PUBLIC_PATH . 'showreadersettingsmodal.php'; // load preferences modal window
    ?>

    <script defer src="/js/underlinewords.min.js"></script>
    <script defer src="/js/showtext.min.js"></script>
    <script defer src="/js/showebook.js"></script>
    <script defer src="/js/dictionary.min.js"></script>
    <script defer src="/js/tooltips.js"></script>
</body>

</html>
