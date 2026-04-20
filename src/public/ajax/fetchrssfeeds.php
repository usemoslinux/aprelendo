<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_GET)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\RSSFeed;
use Aprelendo\Language;
use Aprelendo\InternalException;
use Aprelendo\UserException;

$user_id = $user->id; // get current user's ID
$lang_id = $user->lang_id; // get current language's ID

try {
    if (!isset($_GET['feed_index'])) {
        throw new UserException('Empty Feed Index.');
    }

    $feed_index = $_GET['feed_index'];

    $lang = new Language($pdo, $user_id);
    $lang->loadRecordById($lang_id);

    $feed_uris = [ $lang->rss_feed1_uri,
                   $lang->rss_feed2_uri,
                   $lang->rss_feed3_uri ] ;

    if ($feed_uris) {
        $feed = new RSSFeed($feed_uris[$feed_index]);
        $payload = printRSSFeed($feed, $feed_index+1);
        $response = ['success' => true, 'payload' => $payload];
    }

    echo json_encode($response);
    exit;
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
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
    $feed_title = escapeHtml((string)$feed->title);
    $feed_articles = $feed->articles;

    if (empty($feed_title)) {
        throw new UserException("Malformed RSS feed");
    }

    // Initialize variables for accordion
    $accordion_id = 'accordion-' . $groupindex;
    $heading_id = 'heading-' . $groupindex;
    $item_id = 'item-' . $groupindex;
    $label_id = 'btn-' . $groupindex;

    // Initialize HTML string for accordion group
    $html = "<h2 class='accordion-header' id='$heading_id'>"
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
            $text_title = escapeHtml((string)$article['title']);
            $art_date = escapeHtml((string)$article['date']);
            $text_author = escapeHtml((string)$article['author']);
            $art_src = escapeHtml(sanitizeExternalUrl((string)$article['src']));
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

            $html .= '<div class="entry-text">' . renderEntryText($text_content) . '</div>';
            
            $html .= "<hr>
                        <div>
                        <button type='button' class='btn btn-secondary btn-edit'>Add & Edit</button>
                        </div></div></div></div>";

            $itemindex++;
        }
    }
    $html .= '</div></div></div>';
    
    return $html;
}

/**
 * Escapes text before rendering it into HTML.
 *
 * @param string $value
 * @return string
 */
function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Returns a URL only when it uses an allowed external scheme.
 *
 * @param string $url
 * @return string
 */
function sanitizeExternalUrl(string $url): string
{
    $url = trim($url);

    if ($url === '') {
        return '';
    }

    $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));

    if (!in_array($scheme, ['http', 'https'], true)) {
        return '';
    }

    return $url;
}

/**
 * Converts RSS HTML content into safe paragraph markup.
 *
 * @param mixed $text_content
 * @return string
 */
function renderEntryText($text_content): string
{
    $text_content = (string)$text_content;
    $text_content = html_entity_decode($text_content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text_content = preg_replace('/<(?:br\s*\/?|\/p|\/div|\/li|\/blockquote|\/h[1-6])>/i', "\n", $text_content);
    $plain_text = strip_tags((string)$text_content);
    $plain_text = preg_replace("/\r\n|\r/u", "\n", $plain_text);
    $plain_text = preg_replace("/\n{3,}/u", "\n\n", $plain_text);
    $paragraphs = preg_split("/\n\s*\n/u", trim($plain_text)) ?: [];
    $html = '';

    foreach ($paragraphs as $paragraph) {
        $paragraph = trim((string)preg_replace('/[ \t]+/u', ' ', $paragraph));

        if ($paragraph === '') {
            continue;
        }

        $html .= '<p>' . escapeHtml($paragraph) . '</p>';
    }

    return $html;
}
