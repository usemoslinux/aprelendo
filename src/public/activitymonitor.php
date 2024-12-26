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
require_once APP_ROOT . 'Includes/checklogin.php'; // loads User class & check if user is logged in

use Aprelendo\WordStats;
use Aprelendo\WordDailyGoal;

$stats = new WordStats($pdo, $user->id, $user->lang_id);

// get today's statistics
$words_recalled_today = $stats->getRecalledToday();
$percentage_words_recalled_today = round($words_recalled_today * 100 / 10);
$today_is_streak = ($words_recalled_today >= 10);
$msg_progress_bar = "$words_recalled_today / 10";

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

$message_html = '<span class="text-muted">';

if ($daily_goal_streak_days > 0) {
    $message_html .= '<span class="badge text-bg-warning me-1 bg-opacity-50"><i class="bi bi-fire text-danger me-1">'
        . '</i><strong>' . $daily_goal_streak_days . ' day streak!</strong></span>'
        . '<em>' . $motivational_msg_ongoing_streak[rand(0, 9)] . '</em>';
} else {
    $message_html .= '<em>' . $motivational_msg_no_streak[rand(0, 9)] . '</em>';
}

$message_html .= '</span>';

?>

<!-- Activity monitor -->
<div id="activity-monitor" class="row flex">
    <div class="col-sm-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="">Daily Goal: Recall 10 words</div>
            </div>
            <div class="card-body">
                <div class="progress mb-2" style="height: 13px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                        style="width: <?php echo strval($percentage_words_recalled_today) . '%'; ?>">
                        <?php echo $msg_progress_bar; ?></div>
                </div>
                <div class="d-flex d-inline-block small">
                    <?php echo $message_html; ?>
                    <div class="mx-2 ms-auto">
                        <a href="/stats" title="My statistics" class="d-flex">
                            <span class="bi bi-graph-up"></span><span class="d-none ms-1 d-md-block"> More stats</span>
                        </a>
                    </div>
                    <div>
                        <a href="javascript:;" title="Help" data-bs-toggle="collapse"
                            data-bs-target="#help-word-recall-streak" class="d-flex collapsed" aria-expanded="false">
                            <span class="bi bi-question-circle"></span><span class="d-none ms-1 d-md-block"> Help</span>
                        </a>
                    </div>
                </div>
                <div id="help-word-recall-streak" class="small collapse">
                    <hr>
                    <p>
                        The metric displayed on this card shows the number of words you've successfully recalled today,
                        helping you monitor your daily progress and maintain your "recall streak".
                    </p>
                    <p>
                        A <u>recall streak</u> tracks the number of consecutive days you've met your target for recalled
                        words during daily practice. It includes all words from your vocabulary list that you've
                        reviewed, but excludes any marked as "forgotten."
                    </p>
                    <p>
                        This streak resets if you miss a day of practice, regardless of how many words you recalled on
                        the previous day.
                    </p>
                    <p>
                        Additionally, it's important to differentiate between the recall streak and the <u>reading
                        streak</u>, which measures how many consecutive days you've engaged with your chosen texts
                        at least once. This metric reflects your overall engagement with learning materials and is
                        displayed in the blue header next to the flame icon.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>