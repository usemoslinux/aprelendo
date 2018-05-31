<?php
    // save preferences to database
    $fontfamily = isset($_POST['fontfamily']) ? $_POST['fontfamily'] : "Helvetica";
    $fontsize = isset($_POST['fontsize']) ? $_POST['fontsize'] : '12pt';
    $lineheight = isset($_POST['lineheight']) ? $_POST['lineheight'] : '1.5';
    $alignment = isset($_POST['alignment']) ? $_POST['alignment'] : 'left';
    $mode = isset($_POST['mode']) ? $_POST['mode'] : 'light';
    $assistedlearning = isset($_POST['assistedlearning']) ? $_POST['assistedlearning'] : true;
    $userid = '1'; // TODO: implement different preferences for each user
    $actlangid = $_COOKIE['actlangid'];

    require_once('dbinit.php');
    
    $result = mysqli_query($con, "REPLACE INTO preferences (prefUserId, prefFontFamily,
        prefFontSize, prefLineHeight, prefAlignment, prefMode, prefAssistedLearning, prefActLangId)
        VALUES ('$userid', '$fontfamily', '$fontsize', '$lineheight', '$alignment', '$mode', '$assistedlearning', '$actlangid')") or die(mysqli_error($con));

    if ($result) {
      $expire = time()+81400;
      setcookie('fontfamily', $fontfamily, $expire, "/", false, 0);
      setcookie('fontsize', $fontsize, $expire, "/", false, 0);
      setcookie('lineheight', $lineheight, $expire, "/", false, 0);
      setcookie('alignment', $alignment, $expire, "/", false, 0);
      setcookie('mode', $mode, $expire, "/", false, 0);
      setcookie('assistedlearning', $assistedlearning, $expire, "/", false, 0);
    }
 ?>