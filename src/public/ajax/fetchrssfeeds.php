<?php
/**
 * Copyright (C) 2019 Pablo Castagnino
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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

use Aprelendo\RSSFeeds;
use Aprelendo\InternalException;
use Aprelendo\UserException;

$user_id = $user->id; // get current user's ID
$lang_id = $user->lang_id; // get current language's ID

try {
    $rssfeeds = new RSSFeeds($pdo, $user_id, $lang_id);
    $feeds = $rssfeeds->get();

    $html = '<div id="accordion" class="accordion">';

    if ($feeds) {
        $html = '<div id="accordion" class="accordion">';

        for ($i=0; $i < count($feeds); $i++) {
            $html .= printRSSFeed($feeds[$i], $i+1);
        }

        echo $html . '</div>';
    } else {
        // If all feeds are empty, throw exception with message
        throw new UserException('<p>There are no RSS feeds available for display in this section.</p><p>To '
            . 'get started, head over to the <a class="alert-link" href="/languages.php">languages</a> section and add '
            . 'up to 3 feeds per language to start enjoying <a href="https://en.wikipedia.org/wiki/RSS"'
            . 'target="_blank" rel="noopener noreferrer" class="alert-link">RSS content</a>.</p>');
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}

/**
 * Prints an RSS feed as an accordion
 *
 * @param RSSFeed $feed RSS feed to print
 * @param int $groupindex Index of feed
 * @return string HTML string representing the feed as an accordion
 */
function printRSSFeed($feed, $groupindex): string
{
    // Get feed's title and articles
    $feed_title = $feed->title;
    $feed_articles = $feed->articles;

    if (!empty($feed_title)) {
        // Initialize variables for accordion
        $accordion_id = 'accordion-' . $groupindex;
        $heading_id = 'heading-' . $groupindex;
        $item_id = 'item-' . $groupindex;
        $label_id = 'btn-' . $groupindex;

        // Initialize HTML string for accordion group
        $html = "<div class='accordion-item'>"
            . "<h2 class='accordion-header' id='$heading_id'>"
            . "<button id='$label_id' class='accordion-button collapsed' data-bs-toggle='collapse' "
            . "data-bs-target='#$item_id' aria-expanded='false' aria-controls='$item_id'>"
            . "$feed_title</a>"
            . "</button>"
            . "</h2>"
            . "<div id='$item_id' class='collapse' aria-labelledby='$label_id' data-bs-parent='#accordion'>"
            . "<div class='accordion-body'>"
            . "<div id='$accordion_id' class='accordion'>";

        if (!empty($feed_articles)) {
            $itemindex = 1; // initialize counter for accordion items
            foreach ($feed_articles as $article) {
                // Get article data
                $text_title = $article['title'];
                $art_date = $article['date'];
                $text_author = $article['author'];
                $art_src = $article['src'];
                $text_content = $article['content'];
                
                // Initialize variables for accordion item
                $heading_id     = 'heading-' . $groupindex . '-' . $itemindex;
                $item_id        = 'item-' . $groupindex . '-' . $itemindex;
                $label_id       = 'btn-' . $groupindex . '-' . $itemindex;

                // Add accordion item to HTML string
                $html .= "<div class='accordion-item'>"
                    . "<h2 class='accordion-header' id='$heading_id'>"
                    . "<button id='$label_id' class='accordion-button collapsed entry-info' data-bs-toggle='collapse' "
                    . "data-bs-target='#$item_id' data-pubdate='$art_date' data-author='$text_author' "
                    . "data-src='$art_src' aria-expanded='false' aria-controls='$item_id'>"
                    . "$text_title"
                    . "</button>"
                    . "</h2>"
                    . "<div id='$item_id' class='accordion-collapse collapse' aria-labelledby='$label_id' "
                    . "data-bs-parent='#$accordion_id'>"
                    . "<div class='accordion-body'>";

                $html .= '<div class="entry-text">' . strip_tags($text_content, '<p>') . '</div>';
                
                $html .= "<hr>
                            <div>
                            <button type='button' class='btn btn-secondary btn-edit' data-type='edit'>Edit</button>
                            <button type='button' class='btn btn-secondary btn-readnow' data-type='readnow'>
                            Read now
                            </button>
                            <button type='button' class='btn btn-secondary btn-readlater' data-type='readlater'>
                            Read later
                            </button>
                            <span class='message'></span>
                            </div></div></div></div>";

                $itemindex++;
            }
        }
        $html .= '</div></div></div></div>';
    } else {
        throw new UserException("Oops! There was an error trying to fetch this feed:"
            . $feed->url
            . "\nIt is probably due to a malformed RSS feed.");
    }
    return $html;
}
