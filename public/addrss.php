<?php
	require_once('header.php')
	?>

  <div class="container mtb">
    <div class="row">
      <div class="col-xs-12">
        <ol class="breadcrumb">
          <li>
            <a href="texts.php">Home</a>
          </li>
          <li>
            <a class="active">Add RSS article</a>
          </li>
        </ol>
        <?php require_once('listrss.php'); ?>
      </div>
    </div>
  </div>

  <?php require_once('footer.php') ?>