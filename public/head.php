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
    require_once APP_ROOT . 'includes/checklogin.php'; // check if user is logged in and set $user object
    $google_id = $user->google_id;
    $use_google_login = !empty($google_id);
}

// check if user is allowed to view this page
$this_is_show_page = in_array($curpage, $show_pages);

if ($this_is_show_page) {
    $doclang = $user->lang;

    $table = isset($_GET['sh']) && $_GET['sh'] != 0 ? 'shared_texts' : 'texts';
    
    if ($curpage != 'showofflinevideo' &&
        (!isset($_GET['id']) || empty($_GET['id']) || !$user->isAllowedToAccessElement($table, (int)$_GET['id']))) {
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
    <meta name="description" content="Language learning platform designed to boost your reading,
    listening, speaking and writing skills.">
    <meta name="keywords" content="language, learning, language learning, flashcards, total reading,
    reading, ebooks, books, videos">
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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom styles for this template -->
    <link href="/css/styles.min.css" rel="stylesheet">
    
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0"
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- JQuery JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
        integrity="sha384-NXgwF8Kv9SSAr+jemKKcbvQsz+teULH/a5UNJvZc6kP47hZgl62M1vGnw6gHQhb1"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"
        integrity="sha512-VK2zcvntEufaimc+efOYi622VN5ZacdnufnmX7zIhCPmjhKnOi9ZDMtg1/ug5l183f19gG1/cBstPO4D8N/Img=="
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
