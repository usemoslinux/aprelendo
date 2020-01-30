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

require_once('../includes/dbinit.php'); // connect to database
use Aprelendo\Includes\Classes\Language;

$lang_full = ucfirst(Language::getNameFromIso($user->getLang()));
?>

<script>
    function init() {
        gapi.load('auth2', function() {
            gapi.auth2.init();
        });
    }
</script>

<div class="d-flex h-100 flex-column">
    <nav class="navbar navbar-expand-md navbar-light">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="index.php">Aprelendo</a>

            <!-- Toggler Button -->
            <button class="navbar-toggler" type="button" aria-label="toggler button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <ul class="navbar-nav ml-auto">
                    <li id="language-dropdown" class="nav-item dropdown">
                        <a href="javascript:;" id="language-menu" class="nav-link dropdown-toggle" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img id="img-language-flag" src="/img/flags/<?php echo $user->getLang() . '.svg';?>"
                                alt="<?php echo $lang_full; ?> flag">
                            <span id="learning-lang-span">
                                <?php echo $lang_full; ?>
                            </span>
                            <b class="caret"></b>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="language-menu">
                            <a class="dropdown-item" href="<?php echo 'languages.php?chg=' . $user->getLangId(); ?>">
                                <?php echo $lang_full; ?> settings</a>
                            <a class="dropdown-item" href="languages.php">Change current language</a>
                        </div>
                    </li>

                    <li id="user-dropdown" class="nav-item dropdown">
                        <a id="user-menu" href="javascript:;" class="nav-link dropdown-toggle" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-circle"></i>
                            <?php echo ucfirst($user->getName()); ?>
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

                            <?php if (!$user->isPremium()): ?>
                            <div class="dropdown-header text-primary" >
                                Premium
                            </div>
                            <a href="gopremium.php" class="dropdown-item">Upgrade account</a>
                            <div class="dropdown-divider"></div>
                            <?php endif; ?>

                            <a href="logout.php" onclick="signOut();" class="dropdown-item">Logout</a>
                            <script>
                                function signOut() {
                                    var auth2 = gapi.auth2.getAuthInstance();
                                    auth2.signOut().then(function () {
                                        auth2.disconnect();
                                        
                                        console.log('User signed out.');
                                    });
                                }
                            </script>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    