<?php 
require_once('../../includes/dbinit.php'); // connect to database
require_once(APP_ROOT . 'includes/checklogin.php'); // loads User class & checks if user is logged in

use Aprelendo\Includes\Classes\RSSFeeds;

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

try {
    $rssfeeds = new RSSFeeds($con, $user_id, $learning_lang_id);

    $RSS1notempty = !empty($rssfeeds->feed1->url);
    $RSS2notempty = !empty($rssfeeds->feed2->url);
    $RSS3notempty = !empty($rssfeeds->feed3->url);

    if ($RSS1notempty || $RSS2notempty || $RSS3notempty) {
        $html = '<div id="accordion" class="accordion">';

        if ($RSS1notempty) {
            $html .= printRSSFeed($rssfeeds->feed1, 1);
        }
        if ($RSS2notempty) {
            $html .= printRSSFeed($rssfeeds->feed2, 2);
        }
        if ($RSS3notempty) {
            $html .= printRSSFeed($rssfeeds->feed3, 3);
        }
        echo $html. '</div>';
    } else {
        throw new Exception ('There are no RSS feeds to show. Please, add some in the <a href="languages.php">languages</a> section. You can add up to 3 feeds per language.');
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
}


function printRSSFeed($feed, $groupindex) {
    if(isset($feed->title)) {
        $accordion_id = 'accordion-' . $groupindex;
        $heading_id = 'heading-' . $groupindex;
        $item_id = 'item-' . $groupindex;
        $label_id = 'btn-' . $groupindex;

        $html = "<div id='$accordion_id' class='subaccordion'>
                    <div class='card'>
                        <div class='card-header' id='$heading_id'>
                            <button id='$label_id' class='btn btn-link collapsed' data-toggle='collapse' data-target='#$item_id' aria-expanded='false' aria-controls='$item_id'>
                                <i class='fas fa-chevron-right'></i>
                                $feed->title</a>
                            </button>
                        </div>
                        <div id='$item_id' class='collapse' aria-labelledby='$label_id' data-parent='#accordion'>
                            <div class='card-body'>";

        if (isset($feed->articles)) {
            $itemindex = 1;
            foreach ($feed->articles as $article) {
                $art_title = $article['title'];
                $art_date = $article['date'];
                $art_author = $article['author'];
                $art_src = $article['src'];
                $art_content = $article['content'];
                
                $heading_id     = 'heading-' . $groupindex . '-' . $itemindex;
                $item_id        = 'item-' . $groupindex . '-' . $itemindex;
                $label_id       = 'btn-' . $groupindex . '-' . $itemindex;

                $html .= "<div class='card'>
                            <div class='card-header' id='$heading_id'>
                                <button id='$label_id' class='btn btn-link collapsed entry-info' data-toggle='collapse' data-target='#$item_id' data-pubdate='$art_date' data-author='$art_author' data-src='$art_src' aria-expanded='false' aria-controls='$item_id'>
                                    <i class='fas fa-chevron-right'></i>
                                    $art_title</a>
                                </button>
                            </div>
                            <div id='$item_id' class='collapse' aria-labelledby='$label_id' data-parent='#$accordion_id'>
                                <div class='card-body'>";

                $html .= '<div class="entry-text">' . strip_tags($art_content, '<p>') . '</div>';
                
                $html .= "<hr>
                          <div>
                            <button type='button' class='btn btn-secondary btn-edit'>Edit</button>
                            <button type='button' class='btn btn-secondary btn-readnow'>Add & Read now</button>
                            <button type='button' class='btn btn-secondary btn-readlater'>Add & Read later</button>
                            <span class='message'></span>
                          </div></div></div></div>";

                $itemindex++;
            }
        }
        $html .= '</div></div></div></div>';
    } else {
        throw new Exception ("Oops! There was an error trying to fetch this feed: $feed->url \nIt is probably due to a malformed RSS feed.");
    }
    return $html;
}

?>