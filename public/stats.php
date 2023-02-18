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

require_once '../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

use Aprelendo\Includes\Classes\Achievements;

?>

<script defer src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"
    integrity="sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxU9JEHakS7UJZNeml5ALk+8IKlU6jDgMabC3vkumRokgJA=="
    crossorigin="anonymous" referrerpolicy="no-referrer">
</script>

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
                <div class="col-12">
                    <h4 class="text-center py-2">Your progress this week</h4>
                </div>
                <div class="col-12">
                    <canvas id="myChart" width="100%" height="400px"></canvas>
                </div>
                <div class="col-12">
                    <p><strong class="word-description new">New</strong>: words you've just added to your
                        learning list.</p>
                    <p><strong class="word-description reviewed">Reviewed</strong>: words that you reviewed at least
                        once but that still need additional reviews.</p>
                    <p><strong class="word-description learned">Learned</strong>: words that the
                        system thinks you have already reviewed enough times.</p>
                    <p><strong class="word-description forgotten">Forgotten</strong>: words you reviewed or learned
                        in the past, but that you marked for learning once again.</p>
                    <p><small>Note: considering each text individually, if a word appeared more than once it will
                        only count as one.</small></p>
                </div>
            </div>
        </section>
        <hr>
        <section>
            <dl class="row">
                <dt class="col-12">
                    <h4 class="text-center py-4">Gems & study streak</h4>
                </dt>
                <dt class="col-md-2">
                    <figure class="px-5 px-sm-0 mt-2">
                        <img src="/img/gamification/streak.svg" class="mx-auto d-block m-2 gamification-img"
                            alt="Streak" title="Reading streak days">
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
                    <p>Streaks also allow you to view your progress. You know what they say, if you can envisage
                        your goal, then you’re halfway to achieving it.</p>
                </dd>
                <dt class="col-md-2">
                    <figure class="px-5 px-sm-0 mt-2">
                        <img src="/img/gamification/gems.svg" class="mx-auto d-block m-2 gamification-img"
                            alt="Gems" title="Gems earned">
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
        $achievements = new Achievements($pdo, $user->getId(), $user->getLangId(), $user->getTimeZone());
        $badges = $achievements->checkAll();
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
<script defer src="js/stats-min.js"></script>

<?php require_once 'footer.php'; ?>
