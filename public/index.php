<?php require_once('header.php') ?>

	<!-- *****************************************************************************************************************
	 TABS
	 ***************************************************************************************************************** -->

	 <div class="container mtb">
	 	<div class="row">
	 		<div class="col-lg-12">
				<!-- Tabs  -->
				<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#mytexts" aria-controls="mytexts" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-book"></span> My texts</a></li>
						<li role="presentation" class="nav"><a href="#rss" aria-controls="rss" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-fire"></span> RSS</a></li>
				</ul>

				<!-- Tab panes: content inside tabs -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade in active" id="mytexts"><?php require_once(PRIVATE_PATH . 'listtexts.php') ?></div>
					<div role="tabpanel" class="tab-pane fade" id="rss">chau</div>
				</div>
			</div>
	 	</div>
	 </div>

<?php require_once('footer.php') ?>
