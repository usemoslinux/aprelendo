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
require_once APP_ROOT . 'Includes/checklogin.php'; // loads User class & check if user is logged in

use Aprelendo\WordStats;
use Aprelendo\WordDailyGoal;

$stats = new WordStats($pdo, $user->id, $user->lang_id);

// get today's statistics
$nr_of_words_reviewed_today = $stats->getReviewedToday();
$per_of_words_learned_today = round($nr_of_words_reviewed_today * 100 / 10);
$today_is_streak = ($nr_of_words_reviewed_today >= 10);
$msg_progress_bar = "$nr_of_words_reviewed_today / 10";

// get streak days
$daily_goal = new WordDailyGoal($pdo, $user->id, $user->lang_id, $user->time_zone, $today_is_streak);
$daily_goal_streak_days = $daily_goal->days_streak;

$motivational_msg_no_streak = ["Every day is a new opportunity to learn.",
                                "The more you study, the more progress you'll make.",
                                "Small steps lead to big achievements.",
                                "Learning is the key to unlocking new opportunities.",
                                "Consistency is the key to success.",
                                "Don't give up on your goals, keep pushing forward.",
                                "Learning a language takes time, but it's worth the effort.",
                                "Every effort you make will bring you closer to your goal.",
                                "One day at a time, you'll get there.",
                                "The journey of a thousand miles begins with a single step."
                            ];

$motivational_msg_ongoing_streak = ["You've taken a new step towards mastering a new language.",
                                     "Keep up the great work and you'll see results.",
                                     "Learning a language is a journey, enjoy the process.",
                                     "Your dedication to studying is inspiring.",
                                     "Learning a new language is a powerful tool for personal growth.",
                                     "You're making progress every day, keep it up.",
                                     "With every study session, you're one step closer to fluency.",
                                     "Your efforts today will pay off in the future.",
                                     "Learning is a lifelong journey, and you're on the right track.",
                                     "Keep going, and soon you'll be able to speak with confidence."
                                ];

$message_html = '<span class="font-italic text-muted">';

if ($daily_goal_streak_days > 0) {
    $message_html .= $daily_goal_streak_days . ' day streak. ' . $motivational_msg_ongoing_streak[rand(0, 9)];
} else {
    $message_html .= $motivational_msg_no_streak[rand(0, 9)];
}

$message_html .= '</span>';

?>

<!-- Activity monitor -->
<div id="activity-monitor" class="row flex">
    <div class="col-sm-12">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title">Today's goal: Practice 10 words</h6>
                <div class="progress my-2" style="height: 10px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                    style="min-width: 34px; width: <?php echo strval($per_of_words_learned_today) . '%'; ?>">
                    <?php echo $msg_progress_bar; ?></div>
                </div>
                <?php echo $message_html; ?>
                <a href="/stats" class="font-italic float-end"><span class="bi bi-graph-up"></span> More stats</a>
            </div>
        </div>
    </div>
</div>
