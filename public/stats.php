<?php
	require_once('header.php')
	?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

  <div class="container mtb">
    <div class="row">
      <div class="col-xs-12">
      <ol class="breadcrumb">
          <li>
            <a href="texts.php">Home</a>
          </li>
          <li>
            <a class="active">Statistics</a>
          </li>
        </ol>
        </div>
      <div class="col-xs-12 col-md-9">
      <canvas id="myChart" width="800" height="450"></canvas>
      </div>  
      <div class="col-xs-12 col-md-3">
      <p><strong style="background-color:DodgerBlue">New</strong>: words added to your learning list.</p>
      <p><strong style="background-color:Orange">Reviewed</strong>: words already in your learning list, which you read and you didn't mark as forgotten (by pressing the "forgot meaning" button). It only includes words that the system still thinks you need to pratice (ergo, they will appear underlined in future readings).</p>
      <p><strong style="background-color:MediumSeaGreen">Learned</strong>: same as reviewed, except that the system thinks you have already reviewed these words enough times. In practice, this means they won't appear underlined next time you read a text containing them.</p>
      <p><strong style="background-color:Tomato">Forgotten</strong>: words you reviewed and learned in the past, but you marked for learning once again.</p>
      </div>  
    </div>
  </div>
  <script src="js/stats.js"></script>

  <?php require_once('footer.php') ?>