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

class RSSFeed
{
    private $title    = '';
    private $url      = '';
    private $xmlfeed  = '';
    private $articles = [];

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
    * @return void
    */
    public function fetchXMLFeed(string $url): void
    {
        $this->xmlfeed = Curl::getUrlContents($url);
        
        if ($this->xmlfeed) {
            $this->xmlfeed = simplexml_load_string($this->xmlfeed);

            if ($this->xmlfeed) {
                $isatom = isset($this->xmlfeed->entry);
                $isrss = isset($this->xmlfeed->channel);
                
                if ($isatom || $isrss) {
                    // ATOM: feed>title; RSS: rss>channel>title
                    $this->title = $isatom
                        ? $this->xmlfeed->title
                        : $this->xmlfeed->channel->title;
                    // ATOM: feed>entry; RSS: rss>channel>item
                    $entry = $isatom
                        ? $this->xmlfeed->entry
                        : $this->xmlfeed->channel->item;
                    
                    if (isset($this->title) && isset($entry)) {
                        $itemindex = 1;
                        foreach ($entry as $article) {
                            // ATOM: feed>entry>updated; RSS: rss>channel>item>pubDate
                            $artdate = $isatom ? $article->updated : $article->pubDate;
                            $this->articles[$itemindex]['title'] = $article->title;
                            $this->articles[$itemindex]['date'] = date("d/m/Y - H:i", strtotime($artdate));
                            // ATOM: feed>entry>author; RSS: rss>channel>item>author
                            $this->articles[$itemindex]['author'] = $article->author;
                            // ATOM: feed>entry>link>href attr; RSS: rss>channel>item>link
                            $this->articles[$itemindex]['src'] = $isatom
                                ? $article->link->attributes()->href
                                : $article->link;
                            // ATOM: feed>entry>content; rss>channel>item>description
                            $this->articles[$itemindex]['content'] = $isatom
                                ? $article->pdotent
                                : $article->description;
                            
                            if ($itemindex >= 5) {
                                break;
                            } else {
                                $itemindex++;
                            }
                        }
                    }
                }
            } else {
                throw new \Exception('Oops! There was a problem trying to get this feed: ' . $url);
            }
        } else {
            throw new \Exception('Oops! There was a problem trying to get this feed: ' . $url);
        }
    } // end fetchXMLFeed()

    /**
     * Get the value of title
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the value of url
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get the value of xmlfeed
     * @return string
     */
    public function getXmlfeed(): string
    {
        return $this->xmlfeed;
    }

    /**
     * Get the value of articles
     * @return string
     */
    public function getArticles(): array
    {
        return $this->articles;
    }
}
