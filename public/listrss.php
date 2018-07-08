<div class="row flex">
<div class="col-xs-12">
<?php 
require_once('db/dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/classes/rssfeeds.php'); // load RSSFeed & RSSFeeds classes

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

try {
    $rssfeeds = new RSSFeeds($con, $user_id, $learning_lang_id);

    $RSS1notempty = !empty($rssfeeds->feed1);
    $RSS2notempty = !empty($rssfeeds->feed2);
    $RSS3notempty = !empty($rssfeeds->feed3);

    if ($RSS1notempty || $RSS2notempty || $RSS3notempty) {
        echo '<div class="list-group list-group-root well">';
        
        if ($RSS1notempty) {
            echo printRSSFeed($rssfeeds->feed1, 1);
        }
        if ($RSS2notempty) {
            echo printRSSFeed($rssfeeds->feed2, 2);
        }
        if ($RSS3notempty) {
            echo printRSSFeed($rssfeeds->feed3, 3);
        }
        echo '</div>';
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}


function printRSSFeed($feed, $groupindex) {
    if(isset($feed->title)) {
        $result = "<a href='#item-$groupindex' class='list-group-item' data-toggle='collapse'>
        <i class='glyphicon glyphicon-chevron-right'></i>$feed->title</a>" .
        "<div class='list-group collapse' id='item-$groupindex'>";
        
        if (isset($entry)) {
            $itemindex = 1;
            foreach ($entry as $article) {
                $artdate = $feed->articles[$itemindex]['artdate'];
                $artauthor = $feed->articles[$itemindex]['artauthor'];
                $artsrc = $feed->articles[$itemindex]['artsrc'];
                $content = $feed->articles[$itemindex]['content'];
                
                $result .= "<a href='#item-$groupindex-$itemindex' class='list-group-item entry-info' data-toggle='collapse'" . 
                " data-pubdate='$artdate'" .
                " data-author='$artauthor'" .
                " data-src='$artsrc' >" .
                "<i class='glyphicon glyphicon-chevron-right'></i>$article->title</a>";
                
                $result .= "<div class='list-group collapse' id='item-$groupindex-$itemindex'>
                <div class='list-group-item entry-text'>" . strip_tags($content, '<p>') .
                "<button type='button' class='btn btn-default btn-addsound'>Add sound file</button>
                <button type='button' class='btn btn-default btn-readnow'>Read now</button>
                <button type='button' class='btn btn-default btn-readlater'>Read later</button>
                <span class='message'></span></div></div>";
                $itemindex++;
            }
        }
        $result .= '</div>';
    } else {
        throw new Exception ("Oops! There was an error trying to fetch this feed: $feed->url \nIt is probably due to a malformed RSS feed.");
    }
    return $result;
}

?>
</div>
</div>

<script type="text/javascript" src="js/listrss.js"></script>