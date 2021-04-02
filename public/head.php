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

require_once '../includes/dbinit.php';  // connect to database

$curpage = basename($_SERVER['PHP_SELF']); // returns the current file Name
$show_pages = array('showtext.php', 'showvideo.php', 'showebook.php');

// these are the same pages that use simpleheader.php instead of header.php
$no_login_required_pages = array('index.php', 'register.php', 'login.php', 'accountactivation.php', 
                                 'aboutus.php', 'privacy.php', 'attributions.php', 'extensions.php', 'support.php', 
                                 'totalreading.php', 'compatibledics.php', 'error.php', 'forgotpassword.php', 
                                 'gopremium.php');

$use_google_login = false;

// check if login is required to access page
if (!in_array($curpage, $no_login_required_pages)) {
    require_once APP_ROOT . 'includes/checklogin.php'; // check if user is logged in and set $user object
    $google_id = $user->getGoogleId();
    $use_google_login = isset($google_id) && !empty($google_id);
}                                 

// check if user is allowed to view this page
$this_is_show_page = in_array($curpage, $show_pages);

if ($this_is_show_page) {
    $doclang = $user->getLang();

    $table = isset($_GET['sh']) && $_GET['sh'] != 0 ? 'shared_texts' : 'texts';
    if (!$user->isAllowedToAccessElement($table, (int)$_GET['id'])) {
        header("HTTP/1.1 401 Unauthorized");
        exit;
    }

    $is_shared = $table == 'shared_texts' ? true : false;
} else {
    $doclang = 'en';
}

?>

<!DOCTYPE html>
<html lang=<?php echo "\"$doclang\""; ?> >

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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> 
    
    <!-- Custom styles for this template -->
    <link href="css/styles-min.css" rel="stylesheet">
    
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />

    <!-- JQuery JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js" integrity="sha512-wV7Yj1alIZDqZFCUQJy85VN+qvEIly93fIQAN7iqDFCPEucLCeNFz4r35FCo9s6WrpdDQPi80xbljXB8Bjtvcg==" crossorigin="anonymous"></script>

    <?php if($curpage=='login.php' || $use_google_login): ?>
    <!-- Google API -->
    <script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
    
    <?php endif; ?>
</head>

<?php
// show wallpaper on every page, except those in $show_pages array
if (!$this_is_show_page) {
    echo $curpage == 'gopremium.php' ? '<body class="blue-gradient-wallpaper">' : '<body class="pattern-wallpaper">';
}

?>
