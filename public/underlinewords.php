<?php
    require_once('db/dbinit.php'); // connect to database
    require_once('functions.php');
    $result = colorizeWords($_POST['txt'], $con);
    echo addLinks($result);
 ?>
