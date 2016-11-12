<?php
	$con = mysqli_connect("localhost", "root", "glorfindel", "langx") or die("Could not connect to database: " . mysqli_connect_errno($con) . ': ' . mysqli_connect_error($con));
	mysqli_set_charset($con, 'utf8');
 ?>
