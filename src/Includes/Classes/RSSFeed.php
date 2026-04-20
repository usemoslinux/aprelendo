<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class RSSFeed
{
    public $title    = '';
    public $url      = '';
    public $articles = [];
    private const MAX_NR_OF_ARTICLES = 10;

    /**
     * Constructor
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        if (!empty($url)) {
            $this->url = $url;
            $this->fetchXMLFeed($url);
        }
    } 

    /**
     * Get RSS feed elements and initialize class variables
     *
     * @param string $url Url of the feed to parse
     * @throws UserException If there's a problem fetching or parsing the feed
     * @return void
     */
    public function fetchXMLFeed(string $url): void
    {
        $feed_contents = Curl::getUrlContents($url);

        if (!$feed_contents) {
            throw new UserException('Error fetching the feed: ' . $url);
        }

        $xml_feed = simplexml_load_string($feed_contents, 'SimpleXMLElement', LIBXML_NOERROR |  LIBXML_ERR_NONE);

        if (!$xml_feed) {
            throw new UserException('Error parsing the feed: ' . $url);
        }

        if (isset($xml_feed->entry)) {
            $feed_type = 'atom';
            $entries = $xml_feed->entry;
        } elseif (isset($xml_feed->channel)) {
            $feed_type = 'rss';
            $entries = $xml_feed->channel->item;
        } else {
            throw new UserException('Unknown feed format for: ' . $url);
        }

        $this->title = ($feed_type === 'atom') ? $xml_feed->title : $xml_feed->channel->title;
        $itemIndex = 1;

        foreach ($entries as $article) {
            $articleDate = ($feed_type === 'atom') ? $article->updated : $article->pubDate;

            $this->articles[$itemIndex] = [
                'title' => $article->title,
                'date' => date("d/m/Y - H:i", strtotime($articleDate)),
                'author' => $article->author,
                'src' => ($feed_type === 'atom') ? ($article->link?->attributes()?->href ?? '') : $article->link,
                'content' => ($feed_type === 'atom') ? $article->content : $article->description,
            ];

            if ($itemIndex >= self::MAX_NR_OF_ARTICLES) {
                break;
            }

            $itemIndex++;
        }
    } 
}
