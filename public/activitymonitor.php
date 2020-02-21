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

$stats = new Statistics($pdo, $user->getId(), $user->getLangId());
$week_stats = $stats->get(7); // get today's statistics

$streak_days = 0;

for ($i=6; $i >= 0; $i--) { 
    if ($week_stats['created'][$i] + $week_stats['modified'][$i] + $week_stats['learned'][$i] > 10) {
        $streak_days++;    
    }
}

$message_html = '<p class="card-text text-center"><i style="color:Tomato" class="fas fa-fire"></i> ';

if ($streak_days > 0) {
    $message_html .= $streak_days . ' day streak. Keep it up!';
} else {
    $message_html .= "You've been lazy lately.";
}

$message_html .= '</p>';

?>

<!-- Activity monitor -->
<div class="row flex">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Keep motivated - Today's goal</h6>
                
                <div class="progress my-4">
                    <div class="progress-bar bg-success" style="width:170%">14 / 10</div>
                </div> 
                <?php echo $message_html; ?>
                <a href="stats.php" class="btn btn-primary btn-sm float-right">See more stats</a>
            </div>
        </div>
    </div>
</div>