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

require_once '../Includes/dbinit.php';  // connect to database

$curpage = basename($_SERVER['PHP_SELF'], '.php'); // returns the current file Name
$show_pages = ['showtext', 'showvideo', 'showebook', 'showofflinevideo'];

// these are the same pages that use simpleheader.php instead of header.php
$no_login_required_pages = [
    'index', 'register', 'login', 'accountactivation', 'aboutus', 'privacy', 'attributions', 'extensions', 'contact',
    'totalreading', 'exampledics', 'error', 'forgotpassword', 'donate'
];

$use_google_login = false;

// check if login is required to access page
if (!in_array($curpage, $no_login_required_pages)) {
    require_once APP_ROOT . 'Includes/checklogin.php'; // check if user is logged in and set $user object
    $google_id = $user->google_id;
    $use_google_login = !empty($google_id);
}

$this_is_show_page = in_array($curpage, $show_pages);
$doclang = $this_is_show_page ? $user->lang : 'en';

?>

<!DOCTYPE html>
<html lang=<?php echo "\"$doclang\""; ?> >

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Language learning platform designed to boost your reading,
    listening, speaking and writing skills.">
    <meta name="keywords" content="language, learning, language learning, flashcards, total reading,
    reading, ebooks, books, videos">
    <meta name="author" content="Aprelendo">
    <meta name="robots" content="index, follow">

    <link rel="icon" type="image/png" href="/img/favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/img/favicons/favicon.svg" />
    <link rel="shortcut icon" href="/img/favicons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Aprelendo" />
    <link rel="manifest" href="/img/favicons/site.webmanifest" />

    <title>Aprelendo: Learn languages with your favorite texts, ebooks and videos</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"
        integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    
    <!-- Custom styles for this template -->
    <link href="/css/styles.min.css" rel="stylesheet">
    
    <!-- Bootstrap icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"
        integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- JQuery JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"
        integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>

    <!-- Cal-Heatmap scripts and style sheets for stats page only -->
    <?php if($curpage=='stats'): ?>
    <script src="https://d3js.org/d3.v7.min.js" 
        integrity="sha512-vc58qvvBdrDR4etbxMdlTt4GBQk1qjvyORR2nrsPsFPyrs+/u5c3+1Ct6upOgdZoIl7eq6k3a1UPDSNAQi/32A=="
        crossorigin="anonymous">
    </script>
    <script src="https://unpkg.com/cal-heatmap/dist/cal-heatmap.min.js"
        integrity="sha512-c30EKKfFAjoXgk+P/C4DF+B0uRWiWL1ZX21nS3FH4SsexSjOBbwM4danZUAnDmVKzdDpbSKrkR4vWO3JFstzcQ=="
        crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://unpkg.com/cal-heatmap/dist/cal-heatmap.css"
        integrity="sha512-Z9sQ/pYnUJ7hSzK16+NScA+tcAxFXoZ+vQZ5FQ3FgvNCcpI+fKXepuGc2OvamHHOU00tEBOi2CVbS9xRDpslRw=="
        crossorigin="anonymous">
    <script src="https://unpkg.com/@popperjs/core@2"
        integrity="sha512-TPh2Oxlg1zp+kz3nFA0C5vVC6leG/6mm1z9+mA81MI5eaUVqasPLO8Cuk4gMF4gUfP5etR73rgU/8PNMsSesoQ=="
        crossorigin="anonymous">
    </script>
    <script src="https://unpkg.com/cal-heatmap/dist/plugins/Tooltip.min.js"
        integrity="sha512-udm+VHvcN//WFrhJG4C/ittWSSaOT+8uN2cZR3IOfMFZhHFYYvGJ8icwWjNKUY2MrMa50iX2Rn40epm6ncBXcA=="
        crossorigin="anonymous">
    </script>
    <script src="https://unpkg.com/cal-heatmap/dist/plugins/CalendarLabel.min.js"
        integrity="sha512-OAfBG8mdMW+HCq1M5znKxyZP2Y/20Dns6Tn2SKxCyWD6bX/Wa0vaYg0e4heK++W5UTWpdr9p6Lwu6rUl3J6vrg=="
        crossorigin="anonymous">
    </script>
    <?php endif; ?>

    <!-- Google API -->
    <?php if($curpage=='login' || $use_google_login): ?>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <?php endif; ?>

    <!-- Matomo Analytics -->
    <?php if ($_SERVER['HTTP_HOST'] === 'www.aprelendo.com'): ?>
        <script src="/js/matomo.min.js" async defer></script>
    <?php endif; ?>
</head>

<?php
// show wallpaper on every page, except those in $show_pages array
if (!$this_is_show_page) {
    echo $curpage == 'donate' ? '<body class="blue-gradient-wallpaper">' : '<body class="pattern-wallpaper">';
}

?>
