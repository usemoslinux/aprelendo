<?php
	require_once('header.php')
	?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

  <div class="container mtb">
    <div class="row">
      <div class="col-xs-12">
      <ol class="breadcrumb">
          <li>
            <a href="/">Home</a>
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
      <p><strong style="background-color:rgba(33,150,243,0.4)">New</strong>: words added to your learning list.</p>
      <p><strong style="background-color:rgba(255,235,59,0.4)">Reviewed</strong>: words already in your learning list, which you read and you didn't mark as forgotten (by pressing the "forgot meaning" button). It only includes words that the system still thinks you need to pratice (ergo, they will appear underlined in future readings).</p>
      <p><strong style="background-color:rgba(76,175,80,0.4)">Learned</strong>: same as reviewed, except that the system thinks you have already reviewed these words enough times. In practice, this means they won't appear underlined next time you read a text containing them.</p>
      <p><strong style="background-color:rgba(244,67,54,0.4)">Forgotten</strong>: words you reviewed and learned in the past, but you marked for learning once again.</p>
      </div>  
    </div>
  </div>
  <script src="js/stats.js"></script>

  <?php require_once('footer.php') ?>