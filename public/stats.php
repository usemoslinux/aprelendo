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
?>

<script defer src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="texts.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="active">Statistics</a>
                </li>
            </ol>
        </div>
    </div>
    <div class="row">
    <div class="col-sm-12 col-lg-9">
            <canvas id="myChart" width="800" height="450"></canvas>
        </div>
        <div class="col-sm-12 col-lg-3">
            <p><strong style="background-color:DodgerBlue">New</strong>: words you've just added to your learning list.</p>
            <p><strong style="background-color:Orange">Reviewed</strong>: words that you reviewed at least once but
                that still need additional reviews.</p>
            <p><strong style="background-color:MediumSeaGreen">Learned</strong>: words that the
                system thinks you have already reviewed enough times.</p>
            <p><strong style="background-color:Tomato">Forgotten</strong>: words you reviewed or learned in the past,
                but that you marked for learning once again.</p>
        </div>
    </div>
</div>
<script defer src="js/stats.js"></script>

<?php require_once 'footer.php'; ?>