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
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & check if user is logged in

use Aprelendo\Includes\Classes\Statistics;

/**
 * GAMIFICATION IDEAS
 * 
 * 1. Show weekly streaks
 * 2. Compare users stats with other user stats:
 *    - How many words where studied in the last 7 days
 *    - How many texts where added in the last 7 days
 *    
 * 3. Badges
 *    - Most learned words last week
 *    - Most new words last week
 *    - Most texts studied last week
 *    - Most texts added last week
 *    - Most active user uploading shared texts
 *    - Your first song
 *    - Your first letter
 *    - Your first conversation
 *    - Your first Youtube video
 *    - Your first ebook
 *    - Using "free" mode
 *    - Custom dictionary (for any/current language?)
 *    - Custom translator (for any/current language?)
 *    - Lazy lazy

 */

$stats = new Statistics($pdo, $user->getId(), $user->getLangId());
$week_stats = $stats->get(7); // get weekly statistics

$streak_days = 0;

for ($i=6; $i >= 0; $i--) { 
    if ($week_stats['created'][$i] + $week_stats['modified'][$i] + $week_stats['learned'][$i] < 10) {
        break;    
    }
    $streak_days++;
}

$message_html = '<span class="font-italic text-muted">';

if ($streak_days > 0) {
    $message_html .= $streak_days . ' day streak. Keep it up!';
} else {
    $message_html .= "You've been lazy lately.";
}

$message_html .= '</span>';

$today_stats = $stats->get(1); // get today's statistics
$nr_of_words_learned_today = $today_stats['created'][0] + $today_stats['modified'][0] + $today_stats['learned'][0];
$per_of_words_leardned_today = round($nr_of_words_learned_today * 100 / 10);
$msg_progress_bar = "$nr_of_words_learned_today / 10";


?>

<!-- Activity monitor -->
<div class="row flex">
    <div class="col-sm-12">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title">Today's goal: Practice 10 words</h6>
                <div class="progress my-2" style="height: 10px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="min-width: 34px; width: <?php echo strval($per_of_words_leardned_today) . '%'; ?>"><?php echo $msg_progress_bar; ?></div>
                </div> 
                <?php echo $message_html; ?>
                <a href="stats.php" class="font-italic float-right"><i class="fas fa-chart-line"></i> More stats</a>
            </div>
        </div>
    </div>
</div>