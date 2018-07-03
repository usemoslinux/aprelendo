<div class="row flex">
<div class="col-xs-12">
<?php 
require_once('db/dbinit.php'); // connect to database
require_once('classes/rssfeeds.php'); 

$rssfeed = new RSSFeed;

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

$result = mysqli_query($con, "SELECT LgRSSFeed1URI, LgRSSFeed2URI, LgRSSFeed3URI FROM languages WHERE LgUserId='$user_id' AND LgID='$learning_lang_id'") or die(mysqli_error($con));

if (mysqli_num_rows($result) > 0) { // if there are any feeds, show them
    $row = mysqli_fetch_array($result);
    
    $lgrssfeed1URI = $row['LgRSSFeed1URI'];
    $lgrssfeed2URI = $row['LgRSSFeed2URI'];
    $lgrssfeed3URI = $row['LgRSSFeed3URI'];
    
    $RSS1notempty = !empty($lgrssfeed1URI);
    $RSS2notempty = !empty($lgrssfeed2URI);
    $RSS3notempty = !empty($lgrssfeed3URI);
    
    if ($RSS1notempty || $RSS2notempty || $RSS3notempty) {
        echo '<div class="list-group list-group-root well">';
        
        if ($RSS1notempty) {
            echo $rssfeed->getAndPrintElements($lgrssfeed1URI, 1);
        }
        if ($RSS2notempty) {
            echo $rssfeed->getAndPrintElements($lgrssfeed2URI, 2); 
        }
        if ($RSS3notempty) {
            echo $rssfeed->getAndPrintElements($lgrssfeed3URI, 3); 
        }
        echo '</div>';
    }
}
?>
</div>
</div>

<script type="text/javascript" src="js/listrss.js"></script>