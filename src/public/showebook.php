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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

use Aprelendo\Reader;
use Aprelendo\Texts;
use Aprelendo\TextsUtilities;
use Aprelendo\AudioPlayerForEbooks;
use Aprelendo\Videos;
use Aprelendo\UserException;

$color_mode = '';
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
                $color_mode = 'lightmode';
                break;
            case 'sepia':
                $color_mode = 'sepiamode';
                break;
            case 'dark':
                $color_mode = 'darkmode';
                break;
            default:
                break;
        }

        $font_family = $prefs->font_family;
        $font_size = $prefs->font_size;
        $text_align = $prefs->text_alignment;
        $reader_css = "font-family:$font_family;font-size:$font_size;text-align:$text_align;";
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
$audio_uri = TextsUtilities::isGoogleDriveLink($text->audio_uri)
    ? TextsUtilities::getGoogleDriveAudioUri($text->audio_uri)
    : $text->audio_uri;
$audio_source_is_YT = Videos::isYTVideo($audio_uri);
?>

<!DOCTYPE html>
<html lang=<?php echo "\"$doclang\""; ?>>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Aprelendo is a free, open-source language learning platform designed to boost your reading, listening, speaking and writing skills without hidden costs.">
    <meta name="keywords"
        content="language, learning, language learning, flashcards, total reading, reading, ebooks, books, videos">
    <meta name="author" content="Aprelendo">

    <link rel="icon" type="image/png" href="/img/favicons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/img/favicons/favicon.svg">
    <link rel="shortcut icon" href="/img/favicons/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-title" content="Aprelendo">
    <link rel="manifest" href="/img/favicons/site.webmanifest">

    <title>Aprelendo: Free Language Learning Platform</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css"
        integrity="sha512-fw7f+TcMjTb7bpbLJZlP8g2Y4XcCyFZW8uy8HsRZsH/SwbMw0plKHFHr99DN3l04VsYNwvzicUX/6qurvIxbxw=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Extra style sheets -->
    <link rel="stylesheet" type="text/css" href="/css/ebooks.min.css">
    <link rel="stylesheet" type="text/css" href="/css/styles.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css"
        integrity="sha512-t7Few9xlddEmgd3oKZQahkNI4dS6l80+eGEzFQiqtyVYdvcSG2D3Iub77R20BdotfRPA9caaRkg1tyaJiPmO0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Epub.js & jszip -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"
        integrity="sha384-+mbV2IY1Zk/X1p/nWllGySJSUN8uMs+gUAN10Or95UBH0fpj6GfKgPmgC5EXieXG"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>
    <script defer src="/js/epubjs/epub.min.js"></script>

    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>

    <!-- Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/js/bootstrap.bundle.min.js"
        integrity="sha512-Tc0i+vRogmX4NN7tuLbQfBxa8JkfUSAxSFVzmU31nVdHyiHElPPy2cWfFacmCJKw0VqovrzKhdd2TSTMdAxp2g=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>

    <?php if ($_SERVER['HTTP_HOST'] === 'www.aprelendo.com'): ?>
        <!-- Matomo Analytics -->
        <script src="/js/matomo.min.js" async defer></script>
    <?php endif; ?>
</head>

<body id="readerpage" <?php echo ' class="dvh-100 dvw-100 ' . $color_mode . '"' ?>>
    <div class="offcanvas offcanvas-start <?php echo $color_mode; ?>"
        data-bs-scroll="true" tabindex="-1" id="navigation" aria-labelledby="navigation-title">
        <div class="offcanvas-header">
            <button id="close-offcanvas" type="button"
                class="btn-close <?php echo $color_mode == 'darkmode' ? 'btn-close-white' : ''; ?>"
                data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="d-flex flex-row align-items-center">
                <img id="cover" width="100" alt="Ebook Cover" src="/img/other/generic-ebook-cover.webp">
                <div class="d-flex flex-column flex-fill ms-2">
                    <div class="m-2" id="title">Untitled</div>
                    <div class="m-2" id="author">Unknown</div>
                    <div class="m-2" id="publisher">Unknown</div>
                    <div class="m-2" id="pubdate">Not available</div>
                </div>
            </div>
            <div id="toc"></div>
        </div>
        <?php
        if (!empty($audio_uri)) {
            if ($audio_source_is_YT) {
                echo '<div class="video-player">' .
                    '<div data-ytid="' . Videos::extractYTId($audio_uri) . '" id="videoplayer"></div>' .
                    '</div>';
            } else {
                $audio_player = new AudioPlayerForEbooks($audio_uri);
                echo $audio_player->show();
            }
        }
        ?>
    </div>

    <div id="main" class="offset-lg-2 col-lg-8 h-100">
        <div class="d-flex flex-column h-100">
            <div id="header" class="mt-2 mx-3">
                <span class="d-flex flex-row-reverse">
                    <button id="btn-close-ebook" type="button" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                        data-bs-title="Close &amp; Save reading position"
                        aria-label="Close" class="btn-close">
                    </button>
                    <span class="me-2" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip"
                        data-bs-placement="bottom" data-bs-title="Reader settings">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#reader-settings-modal"
                            class="btn btn-sm btn-secondary">
                            <span class="bi bi-gear-fill"></span>
                        </button>
                    </span>

                    <div class="loading-spinner-container me-2">
                        <div class="spinner-wrapper">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>

                    <span id="book-title-chapter" class="fw-bold pe-2 me-auto my-auto text-truncate"></span>
                    <span id="book-title" class="fw-bold ms-2 me-2 my-auto text-nowrap text-truncate"></span>

                    <button id="opener" class="btn btn-link" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#navigation"
                        aria-controls="navigation">
                        <svg id="hamburger"
                            xmlns="http://www.w3.org/2000/svg"
                            width="28" height="28"
                            viewBox="0 0 32 32">
                            <path d="M4,10h24c1.104,0,2-0.896,2-2s-0.896-2-2-2H4C2.896,6,2,6.896,2,8S2.896,10,4,
                            10z M28,14H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h24c1.104,0,2-0.896,2-2S29.104,
                            14,28,14z M28,22H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h24c1.104,0,2-0.896,2-2
                            S29.104,22,28,22z" />
                        </svg>
                    </button>

                </span>
            </div>
            <div id="text-container" class="d-flex flex-column m-2 p-3" style="<?php echo $reader_css; ?>">
                <div id="text" class="flex-grow-1" data-idText="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
                </div>
                <div class="navlink">
                    <a id="next" class="btn btn-outline-success d-none" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-placement="top" href="#next"></a>
                </div>
            </div>
        </div>
    </div>

    <?php
    require_once PUBLIC_PATH . 'showdicactionmenu.php'; // load dictionary modal window
    require_once PUBLIC_PATH . 'showreadersettingsmodal.php'; // load preferences modal window
    if (!empty($user->hf_token)) {
        require_once PUBLIC_PATH . 'showaibotmodal.php'; // load Lingobot modal window
    }
    ?>

    <script defer src="/js/showtext.min.js"></script>
    <script defer src="/js/underlinewords.min.js"></script>
    <script defer src="/js/wordselection.min.js"></script>
    <script defer src="/js/actionbtns.min.js"></script>
    <script defer src="/js/showebook.min.js"></script>
    <?php if ($audio_source_is_YT): ?>
        <script src="/js/ytvideoplayer.min.js"></script>
    <?php else: ?>
        <script defer src="/js/audioplayer.min.js"></script>
    <?php endif; ?>
    <script defer src="/js/dictionaries.min.js"></script>
    <script defer src="/js/helpers.min.js"></script>
    <script defer src="/js/tooltips.min.js"></script>
</body>

</html>