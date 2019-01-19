<div class="row flex">
<div class="col-xs-12">
<?php 
require_once('../includes/dbinit.php'); // connect to database

use Aprelendo\Includes\Classes\RSSFeeds;

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

try {
    $rssfeeds = new RSSFeeds($con, $user_id, $learning_lang_id);

    $RSS1notempty = !empty($rssfeeds->feed1->url);
    $RSS2notempty = !empty($rssfeeds->feed2->url);
    $RSS3notempty = !empty($rssfeeds->feed3->url);

    if ($RSS1notempty || $RSS2notempty || $RSS3notempty) {
        $html = '<div class="list-group list-group-root well">';
        
        if ($RSS1notempty) {
            $html .= printRSSFeed($rssfeeds->feed1, 1);
        }
        if ($RSS2notempty) {
            $html .= printRSSFeed($rssfeeds->feed2, 2);
        }
        if ($RSS3notempty) {
            $html .= printRSSFeed($rssfeeds->feed3, 3);
        }
        echo $html . '</div>';
    } else {
        throw new Exception ('There are no RSS feeds to show. Please, add some in the <a href="languages.php">languages</a> section. You can add up to 3 feeds per language.');
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
}


function printRSSFeed($feed, $groupindex) {
    if(isset($feed->title)) {
        $result = "<a href='#item-$groupindex' class='list-group-item' data-toggle='collapse'>
        <i class='fas fa-chevron-right'></i> $feed->title</a>" .
        "<div class='list-group collapse' id='item-$groupindex'>";
        
        if (isset($feed->articles)) {
            $itemindex = 1;
            foreach ($feed->articles as $article) {
                $art_title = $article['title'];
                $art_date = $article['date'];
                $art_author = $article['author'];
                $art_src = $article['src'];
                $art_content = $article['content'];
                
                $result .= "<a href='#item-$groupindex-$itemindex' class='list-group-item entry-info' data-toggle='collapse'" . 
                " data-pubdate='$art_date'" .
                " data-author='$art_author'" .
                " data-src='$art_src' >" .
                "<i class='fas fa-chevron-right'></i> $art_title</a>";
                
                $result .= "<div class='list-group collapse' id='item-$groupindex-$itemindex'>
                <div class='list-group-item entry-text'>" . strip_tags($art_content, '<p>') .
                "<div><button type='button' class='btn btn-default btn-addsound'>Edit</button>
                <button type='button' class='btn btn-default btn-readnow'>Add & Read now</button>
                <button type='button' class='btn btn-default btn-readlater'>Add & Read later</button>
                <span class='message'></span></div></div></div>";
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