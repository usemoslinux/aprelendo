<?php
	$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME) or die("Could not connect to database: " . mysqli_connect_errno($con) . ': ' . mysqli_connect_error($con));
	mysqli_set_charset($con, 'utf8');
 ?>
