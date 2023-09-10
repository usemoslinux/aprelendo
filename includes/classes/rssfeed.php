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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aprelendo\Includes\Classes;

use Aprelendo\Includes\Classes\Curl;
use Aprelendo\Includes\Classes\UserException;

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
    } // end __construct()

    /**
     * Get RSS feed elements and initialize class variables
     *
     * @param string $url Url of the feed to parse
     * @throws UserException If there's a problem fetching or parsing the feed
     * @return void
     */
    public function fetchXMLFeed(string $url): void
    {
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => FICTIONAL_USER_AGENT,
            CURLOPT_FOLLOWLOCATION => true,
        ];

        $feed_contents = Curl::getUrlContents($url, $options);

        if (!$feed_contents) {
            throw new UserException('Error fetching the feed: ' . $url);
        }

        $xml_feed = simplexml_load_string($feed_contents);

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
                'src' => ($feed_type === 'atom') ? $article->link->attributes()->href : $article->link,
                'content' => ($feed_type === 'atom') ? $article->pdotent : $article->description,
            ];

            if ($itemIndex >= self::MAX_NR_OF_ARTICLES) {
                break;
            }

            $itemIndex++;
        }
    } // end fetchXMLFeed()
}
