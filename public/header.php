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
use Aprelendo\Includes\Classes\Gems;

if (isset($_GET['lang']) && !empty($_GET['lang'])) {
    try {
        $lang = new Language($pdo, $user->getId());
        $lang->loadRecordByName($_GET['lang']);
        $user->setActiveLang($lang->getId());
    } catch (\Throwable $th) {
        header("Location: index.php");
        exit;
    }
}

$lang_full = ucfirst(Language::getNameFromIso($user->getLang()));

$gems = new Gems($pdo, $user->getId(), $user->getLangId(), $user->getTimeZone());
$nr_of_gems  = (int)$gems->getGems();
$streak_days = (int)$gems->getDaysStreak();

?>

<script>
    function init() {
        gapi.load('auth2', function() {
            gapi.auth2.init();
        });
    }
</script>

<div class="d-flex h-100 flex-column">
    <header>
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container mtb">
                <!-- Brand -->
                <a class="navbar-brand" href="/index"></a>

                <!-- Toggler Button -->
                <button class="navbar-toggler" type="button" aria-label="toggler button" data-bs-toggle="collapse"
                    data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar links -->
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav ms-auto">
                        <li id="streak-days" class="nav-item gamification py-2 pe-md-2">
                            <img src="/img/gamification/streak.svg" class="me-3 me-md-1" alt="Streak"
                            title="Reading streak days"> <?php echo $streak_days; ?>
                        </li>
                        <li id="gems" class="nav-item gamification py-2 pe-md-2">
                            <img src="/img/gamification/gems.svg" class="me-3 me-md-1 ms-md-2" alt="Gems"
                            title="Gems earned"> <?php echo $nr_of_gems; ?>
                        </li>
                        <li id="language-dropdown" class="nav-item dropdown">
                            <a href="javascript:;" id="language-menu" class="nav-link dropdown-toggle" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img id="img-language-flag" class="me-3 me-md-0"
                                    src="/img/flags/<?php echo $user->getLang() . '.svg';?>"
                                    alt="<?php echo $lang_full; ?> flag">
                                <span id="learning-lang-span">
                                    <?php echo $lang_full; ?>
                                </span>
                                <strong class="caret"></strong>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="language-menu">
                                <a class="dropdown-item" href="<?php echo 'languages?chg=' . $user->getLangId(); ?>">
                                    <?php echo $lang_full; ?> settings</a>
                                <a class="dropdown-item" href="/languages">Change current language</a>
                            </div>
                        </li>

                        <li id="user-dropdown" class="nav-item dropdown">
                            <a id="user-menu" href="javascript:;" class="nav-link dropdown-toggle" role="button"
                              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="fas fa-user-circle me-3 me-md-1"></span>
                                <?php echo ucfirst($user->getName()); ?>
                                <strong class="caret"></strong>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-menu">
                                <div class="dropdown-header">
                                    Sections
                                </div>
                                <a href="/texts" class="dropdown-item">My texts</a>
                                <a href="/sharedtexts" class="dropdown-item">Shared texts</a>
                                <a href="/sources" class="dropdown-item">Popular sources</a>
                                <a href="/words" class="dropdown-item">Word list</a>
                                <a href="/study" class="dropdown-item">Study</a>
                                <a href="/stats" class="dropdown-item">Statistics</a>
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-header">
                                    Settings
                                </div>
                                <a href="/userprofile" class="dropdown-item">My profile</a>
                                <a href="/preferences" class="dropdown-item">Preferences</a>
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-header" >
                                    Support us
                                </div>
                                <a href="/donate.php" class="dropdown-item">Donate</a>
                                <div class="dropdown-divider"></div>
                                <a href="/logout.php" onclick="signOut();" class="dropdown-item">Logout</a>
                                <script>
                                    function signOut() {
                                        google.accounts.id.disableAutoSelect();
                                    }
                                </script>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
