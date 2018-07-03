<?php
    require_once('dbinit.php'); // connect to database
    require_once('checklogin.php'); // check if user is logged in and set $user_id & $learning_lang_id
    require_once('../classes/reader.php');

    $text = new Reader($con, $user->id, $user->learning_lang_id);
    $result = $text->colorizeWords($_POST['txt']);
    echo $text->addLinks($result);
 ?>
