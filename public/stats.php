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

use Aprelendo\Includes\Classes\Statistics;

?>

<script defer src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="texts.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="active">Statistics</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <main>
        <section>
            <div class="row">
                <div class="col-lg-12">
                    <h6 class="text-center py-2">Your progress this week</h6>
                </div>
                <div class="col-sm-12 col-lg-9">
                    <canvas id="myChart" width="800" height="450"></canvas>
                </div>
                <div class="col-sm-12 col-lg-3">
                    <p><strong class="word-description new">New</strong>: words you've just added to your learning list.</p>
                    <p><strong class="word-description reviewed">Reviewed</strong>: words that you reviewed at least once but
                        that still need additional reviews.</p>
                    <p><strong class="word-description learned">Learned</strong>: words that the
                        system thinks you have already reviewed enough times.</p>
                    <p><strong class="word-description forgotten">Forgotten</strong>: words you reviewed or learned in the past,
                        but that you marked for learning once again.</p>
                    <p><small>This chart shows only "unique" words or phrases, i.e., words or phrases that appear more than once in a each  document will only count as one.</small></p>
                </div>
            </div>
        </section>
        <section>
            <div class="row">
                <div class="col">
                    <h6 class="text-center pt-4 pb-2">Badges earned this week</h6>
                    <?php 
                    
                    $stats = new Statistics($pdo, $user->getId(), $user->getLangId());
                    $weekly_text_stats = $stats->getTextStats(7); // get weekly text statistics
                    echo var_dump($weekly_text_stats);
                    
                    ?>
                    <div class="d-flex flex-row justify-content-center flex-wrap">
                        <figure class="w-25 px-4 mt-2"> 
                            <img src="img/badges/1st-ebook.svg" class="mx-auto d-block" alt="...">
                            <figcaption class="text-center small font-weight-bold">You uploaded your <br>1st ebook</figcaption>
                        </figure>
                        <figure class="w-25 px-4 mt-2">
                            <img src="img/badges/1st-video.svg" class="mx-auto d-block" alt="...">
                            <figcaption class="text-center small font-weight-bold">You uploaded your <br>1st video</figcaption>
                        </figure>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
<script defer src="js/stats-min.js"></script>

<?php require_once 'footer.php'; ?>