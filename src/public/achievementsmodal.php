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

use Aprelendo\Achievements;

$achievements = new Achievements($pdo, $user->id, $user->lang_id, $user->time_zone);
$unnanounced_achievements = $achievements->checkUnannounced();
$achievements->saveUnannounced($unnanounced_achievements);

if (!empty($unnanounced_achievements)):
?>

<!-- ACHIEVEMENTS MODAL WINDOW -->
<div id="modal-achievements" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Modal content-->
        <div class="modal-content mb-xs-3 achievements-card">
            <div class="modal-header">
                <h4 class="modal-title fw-bold mx-auto">Achievement Unlocked</h4>
            </div>
            <div class="modal-body">
                <?php foreach($unnanounced_achievements as $achievement): ?>
                <div class="modal-split">
                    <figure class="w-75 mx-auto my-0">
                        <img src="<?php echo $achievement['img_uri'];?>" class="achievement-icon mx-auto d-block"
                            alt="<?php echo $achievement['description'];?>">
                        <figcaption class="text-center mt-3 fs-4 fw-bold">
                            <?php echo $achievement['description'];?>
                        </figcaption>
                    </figure>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<script defer src="/js/achievementsmodal.min.js"></script>

<?php endif; ?>