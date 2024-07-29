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

require_once '../Includes/dbinit.php';  // connect to database

$curpage = basename($_SERVER['PHP_SELF'], '.php'); // returns the current file Name
$show_pages = ['showtext', 'showvideo', 'showebook', 'showofflinevideo'];

// these are the same pages that use simpleheader.php instead of header.php
$no_login_required_pages = [
    'index', 'register', 'login', 'accountactivation', 'aboutus', 'privacy', 'attributions', 'extensions', 'contact',
    'totalreading', 'compatibledics', 'error', 'forgotpassword', 'donate'
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

    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="/img/favicons/site.webmanifest">
    <link rel="shortcut icon" href="/img/favicons/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="/img/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

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

    <?php if($curpage=='login' || $use_google_login): ?>
    <!-- Google API -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    
    <?php endif; ?>
</head>

<?php
// show wallpaper on every page, except those in $show_pages array
if (!$this_is_show_page) {
    echo $curpage == 'donate' ? '<body class="blue-gradient-wallpaper">' : '<body class="pattern-wallpaper">';
}

?>
