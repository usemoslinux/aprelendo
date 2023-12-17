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

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

use Aprelendo\Achievements;
use Aprelendo\WordStats;
use Aprelendo\WordDailyGoal;
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.2.1/chart.umd.js"
    integrity="sha512-vCUbejtS+HcWYtDHRF2T5B0BKwVG/CLeuew5uT2AiX4SJ2Wff52+kfgONvtdATqkqQMC9Ye5K+Td0OTaz+P7cw=="
    crossorigin="anonymous" referrerpolicy="no-referrer">
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js"
    integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/texts">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Statistics</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <main>
        <section>
            <div class="row">
                <div class="col-12 my-4">
                    <div class="row">
                        <h4 id="word-list-heading" class="text-center pt-2">Your word list</h4>
                        <div class="col-lg-7 pt-4">
                            <canvas id="total-stats-canvas" style="max-height:295px"></canvas>
                        </div>
                        <div class="col-lg-5 pt-4">
                            <table class="table text-end small" aria-describedby="word-list-heading">
                                <thead>
                                    <tr class="table-secondary">
                                        <th>Status</th>
                                        <th>Description</th>
                                        <th>#</th>
                                        <th>%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong class="word-description learned">Learned</strong></td>
                                        <td>Words that have been reviewed enough times</td>
                                        <td id="learned-count"></td>
                                        <td id="learned-percentage"></td>
                                    </tr>
                                    <tr>
                                        <td><strong class="word-description reviewed">Learning</strong></td>
                                        <td>Words that still need additional reviews</td>
                                        <td id="learning-count"></td>
                                        <td id="learning-percentage"></td>
                                    </tr>
                                    <tr>
                                        <td><strong class="word-description new">New</strong></td>
                                        <td>Words you've just added to your learning list or that were once marked as
                                            forgotten, were reviewed once and still need additional reviews</td>
                                        <td id="new-count"></td>
                                        <td id="new-percentage"></td>
                                    </tr>
                                    <tr>
                                        <td><strong class="word-description forgotten">Forgotten</strong></td>
                                        <td>Words that you marked as forgotten and were not yet reviewed again</td>
                                        <td id="forgotten-count"></td>
                                        <td id="forgotten-percentage"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td></td>
                                        <td id="total-count"></td>
                                        <td id="total-percentage"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <hr>
        <?php
            // get today's statistics
            $stats = new WordStats($pdo, $user->id, $user->lang_id);
            $nr_of_words_reviewed_today = $stats->getReviewedToday();
            $today_is_streak = ($nr_of_words_reviewed_today >= 10);

            // get streak days
            $daily_goal = new WordDailyGoal($pdo, $user->id,
                $user->lang_id, $user->time_zone, $today_is_streak);
            $daily_goal_streak_days = $daily_goal->days_streak;
        ?>
        <section>
            <dl class="row">
                <dt class="col-12">
                    <h4 class="text-center py-4">Daily goal</h4>
                </dt>
                <dt class="col-md-2">
                    <figure class="px-5 px-sm-0 mt-2">
                        <img src="/img/gamification/daily_goal_streak.png" class="mx-auto d-block m-2"
                            alt="Daily goal streak" title="Daily goal streak days">
                        <figcaption class="w-100 text-center fw-bold">
                            <span style="font-size:2rem"><?php echo $daily_goal_streak_days; ?></span>
                        </figcaption>
                    </figure>
                </dt>
                <dd class="col-md-10">
                    <p>Behold the count of successive days you conquered the challenge of
                        reviewing <?php echo $daily_goal->daily_goal; ?> words daily.</p>
                    <p>Streaks possess formidable power in repetition. With each accomplished action, they bestow
                        rewards, increasing the odds of your unwavering commitment. In due time, this fresh routine
                        shall metamorphose into an indomitable habit.</p>
                    <p>Streaks also allow you to view your progress. You know what they say, if you can envisage
                        your goal, then you’re halfway to achieving it.</p>
                </dd>
                <dt class="col-md-2">
                    <figure class="px-5 px-sm-0 mt-2">
                        <img src="/img/gamification/words_today.png" class="mx-auto d-block m-2"
                            alt="Words practiced today" title="Words practiced today">
                        <figcaption class="w-100 text-center fw-bold">
                            <span style="font-size:2rem"><?php echo $nr_of_words_reviewed_today; ?></span>
                    </figure>
                </dt>
                <dd class="col-md-10">
                    <p>This is the total number of words in your learning list that you reviewed today.</p>
                    <p>It includes all the words in your learning list (except those that you marked
                        as forgotten) that you reviewed either during a study or while reading an article, ebook or
                        video transcript.
                    </p>
                    <p><?php if ($today_is_streak) {
                        echo "Bravo! Your consistent commitment to reviewing words each day has propelled you"
                            . " to new linguistic heights. You're on a remarkable path of progress!";
                    } else {
                        echo "Stay positive and keep pushing forward! While today may not have been the day you"
                            . " reached your goal, every step you take brings you closer to achieving it. Keep going!";
                    }?></p>
                </dd>
            </dl>
        </section>
        <hr>
        <section>
            <dl class="row">
                <dt class="col-12">
                    <h4 class="text-center py-4">Gems & study streak</h4>
                </dt>
                <dt class="col-md-2">
                    <figure class="px-5 px-sm-0 mt-2">
                        <img src="/img/gamification/streak.png"
                            style="<?php echo $today_is_reading_streak ? '' : 'filter: grayscale(1);'; ?>"
                            class="mx-auto d-block m-2" alt="Streak" title="Reading streak days">
                        <figcaption class="w-100 text-center fw-bold">
                            <span style="font-size:2rem"><?php echo $streak_days; ?></span>
                        </figcaption>
                    </figure>
                </dt>
                <dd class="col-md-10">
                    <p>This is the number of consecutive days you studied a text, video or ebook in the currently
                        selected language.</p>
                    <p>Streaks are a robust repetition tool. They reward you each time you complete an action,
                        meaning you’re more likely to keep doing it. After a while, this new routine will become
                        a new habit.</p>
                    <p>Streaks not only provide a window into your progress but also serve as a powerful motivator.
                        As the saying goes, by visualizing your goal, you have already embarked on a transformative
                        journey towards its attainment.</p>
                </dd>
                <dt class="col-md-2">
                    <figure class="px-5 px-sm-0 mt-2">
                        <img src="/img/gamification/gems.png" class="mx-auto d-block m-2" alt="Gems"
                            title="Gems earned">
                        <figcaption class="w-100 text-center fw-bold">
                            <span style="font-size:2rem"><?php echo $nr_of_gems; ?></span>
                    </figure>
                </dt>
                <dd class="col-md-10">
                    <p>This is the total number of gems you have won by studying the currently selected language.</p>
                    <p>You can win gems by uploading new texts, videos or books. You also earn them by adding new
                        words and phrases to your vocabulary list. Practice makes you worthy of more precious stones.
                        However, you can lose them by forgetting words or phrases that were marked as learned.</p>
                    <p>Earning gems shouldn't be your ultimate goal, but it can help keep you engaged and motivated
                        to learn.</p>
                </dd>
            </dl>
        </section>
        <?php
        $achievements = new Achievements($pdo, $user->id, $user->lang_id, $user->time_zone);
        $badges = $achievements->checkSaved();
        $total_nr_of_badges = count($badges);
        
        if ($total_nr_of_badges > 0) {
            // header
            $html = '<hr>
                        <section>
                        <div class="row">
                            <div class="col-12"><h4 class="text-center py-4">Achievements</h4></div>
                        </div>';

            // badges
            $nr_of_lines = $total_nr_of_badges / 4;
            $nr_of_lines_floor = floor($nr_of_lines);
            $nr_of_lines_ceil = ceil($nr_of_lines);
            $nr_of_lines_fraction = $nr_of_lines - $nr_of_lines_floor;
            $cur_badge_index = 0;

            for ($row=0; $row < $nr_of_lines_ceil; $row++) {
                $html .= '<div class="row">';

                $cols_in_row = ($row == $nr_of_lines_floor) ? ($nr_of_lines_fraction*4) : 4;
                for ($col=0; $col < $cols_in_row; $col++) {
                    $html .= '<div class="col-6 col-md-3">
                                <figure class="w-100 mt-2">
                                    <img src="'
                                        . $badges[$cur_badge_index]['img_uri']
                                        . '" class="mx-auto d-block gamification-img gamification-badge" alt="'
                                        . $badges[$cur_badge_index]['description']
                                        . '">
                                    <figcaption class="text-center fw-bold pt-2">'
                                        . $badges[$cur_badge_index]['description']
                                        . '</figcaption>
                                </figure>
                            </div>';
                    $cur_badge_index++;
                }
                            
                $html .= '</div>';
            }

            echo $html . '</section>';
        }
        ?>
    </main>
</div>
<script defer src="/js/stats.min.js"></script>

<?php require_once 'footer.php'; ?>
