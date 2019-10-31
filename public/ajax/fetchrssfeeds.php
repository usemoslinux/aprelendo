<?php 
/**
 * Copyright (C) 2018 Pablo Castagnino
 * 
 * This file is part of aprelendo.
 * 
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once '../../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & checks if user is logged in

use Aprelendo\Includes\Classes\RSSFeeds;

$user_id = $user->getId();
$lang_id = $user->getLangId();

try {
    $rssfeeds = new RSSFeeds($pdo, $user_id, $lang_id);

    $RSS1notempty = !empty($rssfeeds->getFeed1()->getUrl());
    $RSS2notempty = !empty($rssfeeds->getFeed2()->getUrl());
    $RSS3notempty = !empty($rssfeeds->getFeed3()->getUrl());

    if ($RSS1notempty || $RSS2notempty || $RSS3notempty) {
        $html = '<div id="accordion" class="accordion">';

        if ($RSS1notempty) {
            $html .= printRSSFeed($rssfeeds->getFeed1(), 1);
        }
        if ($RSS2notempty) {
            $html .= printRSSFeed($rssfeeds->getFeed2(), 2);
        }
        if ($RSS3notempty) {
            $html .= printRSSFeed($rssfeeds->getFeed3(), 3);
        }
        echo $html. '</div>';
    } else {
        throw new \Exception('There are no RSS feeds to show. Please, add some in the <a href="languages.php">languages</a> section. You can add up to 3 feeds per language.');
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
}


function printRSSFeed($feed, $groupindex) {
    $feed_title = $feed->getTitle();
    $feed_articles = $feed->getArticles();

    if(isset($feed_title) && !empty($feed_title)) {
        $accordion_id = 'accordion-' . $groupindex;
        $heading_id = 'heading-' . $groupindex;
        $item_id = 'item-' . $groupindex;
        $label_id = 'btn-' . $groupindex;

        $html = "<div id='$accordion_id' class='subaccordion'>
                    <div class='card'>
                        <div class='card-header' id='$heading_id'>
                            <button id='$label_id' class='btn btn-link collapsed' data-toggle='collapse' data-target='#$item_id' aria-expanded='false' aria-controls='$item_id'>
                                <i class='fas fa-chevron-right'></i>
                                $feed_title</a>
                            </button>
                        </div>
                        <div id='$item_id' class='collapse' aria-labelledby='$label_id' data-parent='#accordion'>
                            <div class='card-body'>";

        if (isset($feed_articles) && !empty($feed_articles)) {
            $itemindex = 1;
            foreach ($feed_articles as $article) {
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
        throw new \Exception("Oops! There was an error trying to fetch this feed:" .
            $feed->getUrl() . "\nIt is probably due to a malformed RSS feed.");
    }
    return $html;
}

?>