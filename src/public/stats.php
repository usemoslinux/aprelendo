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

use Aprelendo\Achievements;
use Aprelendo\WordStats;
use Aprelendo\WordDailyGoal;

// Get daily recall streak statistics
$stats = new WordStats($pdo, $user->id, $user->lang_id);
$words_recalled_today = $stats->getRecalledToday();
$today_is_streak = ($words_recalled_today >= 10);

// get streak days
$daily_goal = new WordDailyGoal(
    $pdo,
    $user->id,
    $user->lang_id,
    $user->time_zone,
    $today_is_streak
);
$daily_goal_streak_days = $daily_goal->days_streak;

// Get achievements
$achievements = new Achievements($pdo, $user->id, $user->lang_id, $user->time_zone);
$badges = $achievements->checkSaved();
$total_nr_of_badges = count($badges);
$achievements_html = '';

// Create achievements html
if ($total_nr_of_badges > 0) {
    $nr_of_lines = $total_nr_of_badges / 4;
    $nr_of_lines_floor = floor($nr_of_lines);
    $nr_of_lines_ceil = ceil($nr_of_lines);
    $nr_of_lines_fraction = $nr_of_lines - $nr_of_lines_floor;
    $cur_badge_index = 0;

    $achievements_html = '<div class="row"><div class="col mb-4"><div class="card h-100 achievements-card">'
        . '<div class="card-header text-center fw-bolder"><div>Achievements</div></div><div class="card-body small">';

    for ($row = 0; $row < $nr_of_lines_ceil; $row++) {
        $achievements_html .= '<div class="row">';

        $cols_in_row = ($row == $nr_of_lines_floor) ? ($nr_of_lines_fraction * 4) : 4;
        for ($col = 0; $col < $cols_in_row; $col++) {
            $achievements_html .= '<div class="col-6 col-md-3">
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

        $achievements_html .= '</div>';
    }

    $achievements_html .= '</div></div></div></div>';
}
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
                        <span class="active">Statistics</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Reviews Heatmap -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card h-100">
                <div class="card-header text-center fw-bolder">
                    <div>Reviews Heatmap</div>
                </div>
                <div class="card-body small">
                    <div id="heatmap"></div>
                    <div class="text-center mt-2">Each square is a day; darker means more words reviewed.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart and Table Row -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header text-center fw-bolder">
                    <div>Word List Chart</div>
                </div>
                <div class="card-body small">
                    <canvas id="total-stats-canvas" class="mx-auto"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header text-center fw-bolder">
                    <div>Word List Summary</div>
                </div>
                <div class="card-body small">
                    <table aria-describedby="word-list-heading" class="table shadow-none">
                        <thead>
                            <tr>
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
                                <td id="learned-count">0</td>
                                <td id="learned-percentage">0</td>
                            </tr>
                            <tr>
                                <td><strong class="word-description reviewed">Learning</strong></td>
                                <td>Words that still need additional reviews</td>
                                <td id="learning-count">0</td>
                                <td id="learning-percentage">0</td>
                            </tr>
                            <tr>
                                <td><strong class="word-description new">New</strong></td>
                                <td>Words you've just added to your learning list or that were once marked as
                                    forgotten, were reviewed once and still need additional reviews</td>
                                <td id="new-count">0</td>
                                <td id="new-percentage">0</td>
                            </tr>
                            <tr>
                                <td><strong class="word-description forgotten">Forgotten</strong></td>
                                <td>Words that you marked as forgotten and were not yet reviewed again</td>
                                <td id="forgotten-count">0</td>
                                <td id="forgotten-percentage">0</td>
                            </tr>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td></td>
                                <td id="total-count">0</td>
                                <td id="total-percentage"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Goal Cards Row -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header text-center fw-bolder">
                    <div>Current Daily Goal Streak</div>
                </div>
                <div class="card-body small">
                    <figure class="px-5 px-sm-0 mt-2">
                        <img src="/img/gamification/daily-goal-streak.png" class="mx-auto d-block m-2"
                            alt="Daily goal streak" title="Daily goal streak days">
                        <figcaption class="w-100 text-center fw-bold">
                            <span style="font-size:2rem"><?php echo number_format($daily_goal_streak_days); ?></span>
                        </figcaption>
                    </figure>
                    <p>Behold the count of successive days you conquered the challenge of
                        recalling <?php echo $daily_goal->daily_goal; ?> words daily. This metric not only tracks your
                        consistency but also serves as a powerful motivator, encouraging you to make language learning
                        a daily habit.
                    </p>
                    <p>
                        Building a streak helps establish a routine, which is one of the cornerstones of successful
                        learning. Each day you meet your goal, you're reinforcing neural pathways that make recalling
                        vocabulary faster and more intuitive over time. Even a single streak day is a victory, as it
                        represents progress toward mastery.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header text-center fw-bolder">
                    <div>Words Recalled Today</div>
                </div>
                <div class="card-body small">
                    <figure class="px-5 px-sm-0 mt-2">
                        <img src="/img/gamification/words-today.png" class="mx-auto d-block m-2"
                            alt="Words practiced today" title="Words practiced today">
                        <figcaption class="w-100 text-center fw-bold">
                            <span style="font-size:2rem"><?php echo number_format($words_recalled_today); ?></span>
                        </figcaption>
                    </figure>
                    <p>This is the total number of words in your learning list that you recalled today.</p>
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
                        } ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gems and Reading Streak Cards Row -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header text-center fw-bolder">
                    <div>Reading Streak</div>
                </div>
                <div class="card-body small">
                    <figure class="px-5 px-sm-0 mt-2">
                        <img src="/img/gamification/streak.png"
                            style="<?php echo $today_is_reading_streak ? '' : 'filter: grayscale(1);'; ?>"
                            class="mx-auto d-block m-2" alt="Streak" title="Reading streak days">
                        <figcaption class="w-100 text-center fw-bold">
                            <span style="font-size:2rem"><?php echo number_format($streak_days); ?></span>
                        </figcaption>
                    </figure>
                    <p>
                        Your reading streak captures the number of consecutive days you've engaged with texts, videos,
                        or ebooks in your chosen language. This metric emphasizes your immersion in authentic content,
                        which is vital for developing language intuition and context-based understanding.
                    </p>
                    <p>
                        Unlike vocabulary drills or isolated exercises, reading streaks highlight your exposure to
                        real-world usage. Each day spent reading reinforces grammar, sentence structure, and word usage
                        in a natural setting. Whether it's a short article or a chapter from an ebook, maintaining a
                        reading streak ensures steady progress in comprehension and fluency.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header text-center fw-bolder">
                    <div>Gems Earned</div>
                </div>
                <div class="card-body small">
                    <figure class="px-5 px-sm-0 mt-2">
                        <img src="/img/gamification/gems.png" class="mx-auto d-block m-2" alt="Gems"
                            title="Gems earned">
                        <figcaption class="w-100 text-center fw-bold">
                            <span style="font-size:2rem"><?php echo number_format($nr_of_gems); ?></span>
                        </figcaption>
                    </figure>
                    <p>The total gems you've accumulated are a reflection of your commitment to learning and your
                        engagement with the platform. Gems are earned through activities such as uploading new texts,
                        videos, or books, and by expanding your vocabulary with new words and phrases. These rewards
                        serve as a tangible representation of your efforts and progress, encouraging you to stay
                        motivated and consistent in your studies.
                    </p>
                    <p>However, learning is a dynamic process, and gems can also be lost when words or phrases
                        previously marked as "learned" are forgotten. This ensures the system accurately mirrors your
                        retention and understanding, keeping you focused on mastering vocabulary rather than
                        accumulating points alone.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Achievements -->
    <?php echo $achievements_html; ?>
</div>

<!-- ChartJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"
    integrity="sha512-ZwR1/gSZM3ai6vCdI+LVF1zSq/5HznD3ZSTk7kajkaj4D292NLuduDCO1c/NT8Id+jE58KYLKT7hXnbtryGmMg=="
    crossorigin="anonymous" referrerpolicy="no-referrer">
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js"
    integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script defer src="/js/stats.min.js"></script>
<script defer src="/js/reviewheatmap.min.js"></script>

<?php require_once 'footer.php'; ?>
