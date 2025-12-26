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
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

use Aprelendo\Language;
use Aprelendo\SupportedLanguages;
use Aprelendo\HallOfFame;

$hall_of_fame = new HallOfFame($pdo, $user->lang, 'all_time');

// get leaderboard data
$top_gems_earned = $hall_of_fame->getTopGemsEarned();
$top_longest_streaks = $hall_of_fame->getTopStreaks();
$top_words_learned = $hall_of_fame->getTopWordsLearned();
$top_achievements = $hall_of_fame->getTopAchievements();

// get community stats
$active_learners = $hall_of_fame->getActiveLearners();
$total_words_learned = $hall_of_fame->getTotalWordsLearned();
$total_gems_earned = $hall_of_fame->getTotalGemsEarned();
$avg_streak_days = $hall_of_fame->GetAvgStreakDays();

$full_lang_name = ucfirst(SupportedLanguages::get($user->lang, 'name')) ?? $user->lang;

?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/texts">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Hall of Fame <?php echo "($full_lang_name)"; ?></span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">
        <!-- Top Gem Earners -->
        <div class="col-lg-6 col-md-12">
            <div class="card leaderboard-card h-100">
                <div class="card-header fw-bolder">
                        <i class="bi bi-gem stat-icon text-warning"></i>
                        Top Gem Earners
                </div>
                <div class="card-body p-1">
                    <div class="list-group list-group-flush" id="gemLeaderboard">
                        <?php echo $hall_of_fame->print_leaderboard($top_gems_earned, 'gems'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Longest Streaks -->
        <div class="col-lg-6 col-md-12">
            <div class="card leaderboard-card h-100">
                <div class="card-header fw-bolder">
                        <i class="bi bi-fire stat-icon text-danger"></i>
                        Longest Streaks
                </div>
                <div class="card-body p-1">
                    <div class="list-group list-group-flush" id="streakLeaderboard">
                        <?php echo $hall_of_fame->print_leaderboard($top_longest_streaks, 'days'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Most Words in List -->
        <div class="col-lg-6 col-md-12">
            <div class="card leaderboard-card h-100">
                <div class="card-header fw-bolder">
                        <i class="bi bi-book stat-icon text-success"></i>
                        Most Words Learned
                </div>
                <div class="card-body p-1">
                    <div class="list-group list-group-flush" id="wordsLeaderboard">
                        <?php echo $hall_of_fame->print_leaderboard($top_words_learned, 'words'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Achievement Hunters -->
        <div class="col-lg-6 col-md-12">
            <div class="card leaderboard-card h-100">
                <div class="card-header fw-bolder">
                        <i class="bi bi-award stat-icon text-info"></i>
                        Achievement Hunters
                </div>
                <div class="card-body p-1">
                    <div class="list-group list-group-flush" id="achievementsLeaderboard">
                        <?php echo $hall_of_fame->print_leaderboard($top_achievements, 'achievements'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="text-center mb-4">Community Statistics</h3>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                    <?php echo $hall_of_fame->print_community_stat($active_learners); ?>
                    <p class="text-muted mb-0">Active Learners</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-book-half text-success" style="font-size: 3rem;"></i>
                    <?php echo $hall_of_fame->print_community_stat($total_words_learned); ?>
                    <p class="text-muted mb-0">Total Words Learned</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-gem text-warning" style="font-size: 3rem;"></i>
                    <?php echo $hall_of_fame->print_community_stat($total_gems_earned); ?>
                    <p class="text-muted mb-0">Total Gems Earned</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-fire text-danger" style="font-size: 3rem;"></i>
                    <?php echo $hall_of_fame->print_community_stat($avg_streak_days); ?>
                    <p class="text-muted mb-0">Avg. Streak Days</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>