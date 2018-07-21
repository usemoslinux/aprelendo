<?php 
require_once('dbinit.php');  // connect to database
require_once(PUBLIC_PATH . '/db/checklogin.php'); // check if user is logged in and set $user object
require_once(PUBLIC_PATH . '/classes/videos.php'); // load Reader class

try {
    if (isset($_POST['video_id']) && !empty($_POST['video_id'])) {
        $video_id = $_POST['video_id'];
        $video = new Videos($con, $user->id, $user->learning_lang_id);
        echo $video->fetchVideo($user->learning_lang, $video_id);
    } else {
        throw new Exception ('There was a problem retrieving that URL. Please check it is not empty or malformed');
    }
    
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

function xml2array($xmlObject)
{
    $out = array ();

    foreach ( (array) $xmlObject as $index => $node ) {
        $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;
    }
        
    return $out;
}

?>