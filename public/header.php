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
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Font awesome icons -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css " integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
        <!-- Bootstraptour CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-tour@0.12.0/build/css/bootstrap-tour.min.css">
        <!-- Custom styles for this template -->
        <link href="css/styles.css" rel="stylesheet">

        <!-- JQuery JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <!-- Bootstraptour JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-tour@0.12.0/build/js/bootstrap-tour.min.js"></script>

    </head>

    <body>
        <!-- Fixed navbar -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">Aprelendo</a>
                </div>
                <div class="navbar-collapse collapse navbar-right">
                    <ul class="nav navbar-nav">
                        <li id="language-dropdown" class="dropdown">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                                <img id="img-language-flag" src="/img/flags/<?php echo $user->learning_lang . '.svg';?>" alt="<?php echo $learning_lang_full; ?> flag">
                                <span id="learning-lang-span">&nbsp;<?php echo $learning_lang_full; ?></span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo 'languages.php?chg=' . $user->learning_lang_id; ?>"><?php echo $learning_lang_full; ?> settings</a></li>
                                <li><a href="languages.php">Change current language</a></li>
                            </ul>
                        </li>
                        <li id="user-dropdown" class="dropdown">
                            <a id="user-menu" href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                                <?php echo ucfirst($user->name); ?>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-header">
                                    Sections
                                </li>
                                <li>
                                    <a href="texts.php">My texts</a>
                                </li>
                                <li>
                                    <a href="sharedtexts.php">Shared texts</a>
                                </li>
                                <li>
                                    <a href="sources.php">Popular sources</a>
                                </li>
                                <li>
                                    <a href="words.php">Word list</a>
                                </li>
                                <li>
                                    <a href="stats.php">Statistics</a>
                                </li>
                                <li class="divider"></li>
                                <li class="dropdown-header">
                                    Settings
                                </li>
                                <li>
                                    <a href="userprofile.php">My profile</a>
                                </li>
                                <li>
                                    <a href="preferences.php">Preferences</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="logout.php">Logout</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!--/.nav-collapse -->
            </div>
        </div>