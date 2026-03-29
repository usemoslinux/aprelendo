<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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

<script defer src="/js/achievementsmodal.js"></script>

<?php endif; ?>