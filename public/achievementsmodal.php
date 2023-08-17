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

use Aprelendo\Includes\Classes\Achievements;

$achievements = new Achievements($pdo, $user->getId(), $user->getLangId(), $user->getTimeZone());

$unnanounced_achievements = $achievements->checkUnannounced();
$achievements->saveUnannounced($unnanounced_achievements);

if(isset($unnanounced_achievements) && !empty($unnanounced_achievements)):
?>

<!-- ACHIEVEMENTS MODAL WINDOW -->
<div id="modal-achievements" class="modal fade" data-keyboard="true" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Modal content-->
        <div class="modal-content mb-xs-3">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title mx-auto">Achievement unlocked</h5>
            </div>
            <div class="modal-body">
                <?php foreach($unnanounced_achievements as $achievement): ?>
                <div class="modal-split">
                    <figure class="w-75 mx-auto my-0">
                        <img src="<?php echo $achievement['img_uri'];?>" class="mx-auto d-block"
                            alt="<?php echo $achievement['description'];?>">
                        <figcaption class="text-center fw-bold"><?php echo $achievement['description'];?></figcaption>
                    </figure>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<script defer src="js/achievementsmodal.min.js"></script>

<?php endif; ?>
