<?php
    session_start();

    // save preferences to database
    $fontfamily = isset($_POST['fontfamily']) ? $_POST['fontfamily'] : "Helvetica";
    $fontsize = isset($_POST['fontsize']) ? $_POST['fontsize'] : '12pt';
    $lineheight = isset($_POST['lineheight']) ? $_POST['lineheight'] : '1.5';
    $alignment = isset($_POST['alignment']) ? $_POST['alignment'] : 'left';
    $mode = isset($_POST['mode']) ? $_POST['mode'] : 'light';
    $userid = '1'; // TODO: implement different preferences for each user

    require_once('dbinit.php');

    $result = mysqli_query($con, "REPLACE INTO preferences (prefUserId, prefFontFamily,
        prefFontSize, prefLineHeight, prefAlignment, prefMode)
        VALUES ('$userid', '$fontfamily', '$fontsize', '$lineheight', '$alignment', '$mode')")
        or die(mysqli_error($con));

    // save preferences to session
    $_SESSION['fontfamily'] = $fontfamily;
    $_SESSION['fontsize'] = $fontsize;
    $_SESSION['lineheight'] = $lineheight;
    $_SESSION['alignment'] = $alignment;
    $_SESSION['mode'] = $mode;
 ?>
