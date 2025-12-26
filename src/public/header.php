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
use Aprelendo\Language;
use Aprelendo\Gems;

if (!empty($_GET['lang'])) {
    try {
        $lang = new Language($pdo, $user->id);
        $lang->loadRecordByName($_GET['lang']);
        $user->setActiveLang($lang->id);
    } catch (\Throwable $th) {
        header("Location:/");
        exit;
    }
}

$lang_full = ucfirst(Language::getNameFromIso($user->lang));

$gems = new Gems($pdo, $user->id, $user->lang_id, $user->time_zone);
$nr_of_gems  = (int)$gems->gems;
$streak_days = (int)$gems->days_streak;
$today_is_reading_streak = $gems->today_is_streak;

?>

<div class="d-flex h-100 flex-column">
    <header>
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container mtb">
                <!-- Brand -->
                <a class="navbar-brand" href="/"></a>

                <!-- Toggler Button -->
                <button class="navbar-toggler" type="button" aria-label="toggler button" data-bs-toggle="collapse"
                    data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar links -->
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav ms-auto mt-3 mt-md-auto pe-3">
                        <li id="streak-days" class="nav-item my-2 mx-auto me-md-3 gamification">
                            <span class="d-block py-2">
                                <img src="/img/gamification/streak.webp"
                                style="<?php echo $today_is_reading_streak ? '' : 'filter: grayscale(1);'; ?>"
                                class="me-1 me-md-1 header-gamification-icon" alt="Streak"
                                title="Reading streak days"> <?php echo number_format($streak_days); ?>
                            </span>
                        </li>
                        <li id="gems" class="nav-item my-2 mx-auto me-md-3 gamification">
                            <span class="d-block py-2">
                                <img src="/img/gamification/gems.webp" class="me-1 me-md-1 ms-md-2 header-gamification-icon"
                                alt="Gems" title="Gems earned"> <?php echo number_format($nr_of_gems); ?>
                            </span>
                        </li>
                        <li id="language-dropdown" class="nav-item dropdown my-2 me-md-2">
                            <a href="javascript:;" id="language-menu" class="nav-link dropdown-toggle" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="me-1 me-md-0 language-flag"
                                    src="/img/flags/<?php echo $user->lang . '.svg';?>"
                                    alt="<?php echo $lang_full; ?> flag">
                                <span id="learning-lang-span">
                                    <?php echo $lang_full; ?>
                                </span>
                                <strong class="caret"></strong>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="language-menu">
                                <a class="dropdown-item" href="<?php echo 'languages?chg=' . $user->lang_id; ?>">
                                    <?php echo $lang_full; ?> settings</a>
                                <a class="dropdown-item" href="/languages">Change current language</a>
                            </div>
                        </li>

                        <li id="user-dropdown" class="nav-item dropdown my-2">
                            <a id="user-menu" href="javascript:;" class="nav-link dropdown-toggle" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="bi bi-person-circle me-1 me-md-1"></span>
                                <?php echo ucfirst($user->name); ?>
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
                                <a href="/studylauncher" class="dropdown-item">Study</a>
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
                                <?php if (!IS_SELF_HOSTED): ?>
                                    <script>
                                        function signOut() {
                                            if (typeof google !== 'undefined') {
                                                google.accounts.id.disableAutoSelect();
                                            }
                                        }
                                    </script>
                                <?php endif; ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
