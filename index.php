<?php require_once('header.php') ?>

	<!-- *****************************************************************************************************************
	 TABS
	 ***************************************************************************************************************** -->

	 <div class="container mtb">
	 	<div class="row">
	 		<div class="col-lg-8">
				<!-- Tabs  -->
				<ul class="nav nav-tabs">
						<li class="nav active"><a href="#library" data-toggle="tab">Library</a></li>
						<li class="nav"><a href="#wordlist" data-toggle="tab">Word list</a></li>
						<li class="nav"><a href="#statistics" data-toggle="tab">Statistics</a></li>
						<li class="nav"><a href="#flashcards" data-toggle="tab">Flashcards</a></li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div class="tab-pane fade in active" id="library"><?php require_once('listtexts.php') ?></div>
					<div class="tab-pane fade" id="wordlist"><?php require_once('show_words.php') ?></div>
					<div class="tab-pane fade" id="statistics"><?php require_once('show_stats.php') ?></div>
					<div class="tab-pane fade" id="flashcards"><?php require_once('show_flashcards.php') ?></div>
				</div>

			</div><! --/col-lg-8 -->

	 		<div class="col-lg-4">
		 		<h4>Our Address</h4>
		 		<div class="hline"></div>
		 			<p>
		 				Some Ave, 987,<br/>
		 				23890, New York,<br/>
		 				United States.<br/>
		 			</p>
		 			<p>
		 				Email: hello@solidtheme.com<br/>
		 				Tel: +34 8493-4893
		 			</p>
		 			<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
	 		</div>
	 	</div><! --/row -->
	 </div><! --/container -->

<?php require_once('footer.php') ?>
