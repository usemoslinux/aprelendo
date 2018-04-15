<?php
 	session_start();
	require_once('header.php')
	?>

	 <div class="container mtb">
	 	<div class="row">
	 		<div class="col-lg-12">
				<?php $showarchivedtexts = true; require_once('listtexts.php'); ?>
			</div>
	 	</div>
	 </div>

<?php require_once('footer.php') ?>
