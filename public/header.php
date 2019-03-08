<?php
/**
 * Copyright (C) 2018 Pablo Castagnino
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

require_once('../includes/dbinit.php'); // connect to database
require_once(APP_ROOT . 'includes/checklogin.php'); // check if logged in and set $user

use Aprelendo\Includes\Classes\Language;

$learning_lang_full = ucfirst(Language::getLanguageName($user->learning_lang));

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel='shortcut icon' type='image/x-icon' href='img/logo.svg' />

    <title>Aprelendo</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    
    <!-- Font awesome icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css " integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ"
        crossorigin="anonymous">

    <!-- JQuery JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    
    <!-- Bootstrap Tour -->
    <?php 
    $file_name = 'js/tour/' . basename($_SERVER['PHP_SELF'], ".php") . '.js';
    if (file_exists(PUBLIC_PATH . $file_name)) {
        // echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tour/0.11.0/css/bootstrap-tour-standalone.css">
        // <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tour/0.11.0/js/bootstrap-tour-standalone.min.js"></script>';
    }
    ?>
    
    <!-- Custom styles for this template -->
    <link href="css/styles.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-md navbar-light">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="index.php">Aprelendo</a>

            <!-- Toggler/collapsibe Button -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <ul class="navbar-nav ml-auto">
                    <li id="language-dropdown" class="nav-item dropdown">
                        <a href="javascript:;" id="language-menu" class="nav-link dropdown-toggle" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img id="img-language-flag" src="/img/flags/<?php echo $user->learning_lang . '.svg';?>"
                                alt="<?php echo $learning_lang_full; ?> flag">
                            <span id="learning-lang-span">&nbsp;
                                <?php echo $learning_lang_full; ?></span>
                            <b class="caret"></b>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="language-menu">
                            <a class="dropdown-item" href="<?php echo 'languages.php?chg=' . $user->learning_lang_id; ?>">
                                <?php echo $learning_lang_full; ?> settings</a>
                            <a class="dropdown-item" href="languages.php">Change current language</a>
                        </div>
                    </li>

                    <li id="user-dropdown" class="nav-item dropdown">
                        <a id="user-menu" href="javascript:;" class="nav-link dropdown-toggle" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-circle"></i>
                            <?php echo ucfirst($user->name); ?>
                            <b class="caret"></b>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-menu">
                            <div class="dropdown-header">
                                Sections
                            </div>
                            <a href="texts.php" class="dropdown-item">My texts</a>
                            <a href="sharedtexts.php" class="dropdown-item">Shared texts</a>
                            <a href="sources.php" class="dropdown-item">Popular sources</a>
                            <a href="words.php" class="dropdown-item">Word list</a>
                            <a href="stats.php" class="dropdown-item">Statistics</a>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-header">
                                Settings
                            </div>
                            <a href="userprofile.php" class="dropdown-item">My profile</a>
                            <a href="preferences.php" class="dropdown-item">Preferences</a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    