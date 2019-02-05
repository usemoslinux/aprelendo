<?php
require_once('header.php')
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

<div class="container mtb">
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
<script src="js/stats.js"></script>

<?php require_once('footer.php') ?>