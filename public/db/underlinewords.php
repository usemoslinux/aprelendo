<?php
    require_once('dbinit.php'); // connect to database
    require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in
    require_once(PUBLIC_PATH . '/classes/reader.php'); // loads Reader class

    $text = new Reader($con, $user->id, $user->learning_lang_id);
    $result = $text->colorizeWords($_POST['txt']);
    echo $text->addLinks($result);
 ?>
