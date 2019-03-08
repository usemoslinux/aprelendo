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

namespace Aprelendo\Includes\Classes;

use Exception;

class RSSFeed
{
    public $title;
    public $url;
    public $xmlfeed;
    public $articles = array();

    /**
     * Constructor
     *
     * @param string $url
     */
    public function __construct($url) {
        if (!empty($url)) {
            $this->url = $url;
            $this->fetchXMLFeed($url);
        }
    }

    /**
    * Get RSS feed elements and initialize class variables
    * 
    * @param string $url Url of the feed to parse
    * @return string
    */
    public function fetchXMLFeed($url) {
        $this->xmlfeed = file_get_contents($url);
        
        if ($this->xmlfeed) {
            $this->xmlfeed = simplexml_load_string($this->xmlfeed);
            $isatom = isset($this->xmlfeed->entry);
            $isrss = isset($this->xmlfeed->channel);
            
            if ($isatom || $isrss) {
                $this->title = $isatom ? $this->xmlfeed->title: $this->xmlfeed->channel->title; // ATOM: feed>title; RSS: rss>channel>title
                $entry = $isatom ? $this->xmlfeed->entry: $this->xmlfeed->channel->item; // ATOM: feed>entry; RSS: rss>channel>item
                
                if(isset($this->title)) {
                    if (isset($entry)) {
                        $itemindex = 1;
                        foreach ($entry as $article) {
                            $artdate = $isatom ? $article->updated : $article->pubDate; // ATOM: feed>entry>updated; RSS: rss>channel>item>pubDate
                            $this->articles[$itemindex]['title'] = $article->title;
                            $this->articles[$itemindex]['date'] = date("d/m/Y - H:i", strtotime($artdate));
                            $this->articles[$itemindex]['author'] = $article->author; // ATOM: feed>entry>author; RSS: rss>channel>item>author
                            $this->articles[$itemindex]['src'] = $isatom ? $article->link->attributes()->href : $article->link;  // ATOM: feed>entry>link>href attr; RSS: rss>channel>item>link
                            $this->articles[$itemindex]['content'] = $isatom ? $article->content : $article->description; // ATOM: feed>entry>content; rss>channel>item>description
                            
                            if ($itemindex >= 5) {
                                break;
                            } else {
                                $itemindex++;
                            }
                        }
                    }
                }
            }
        } else {
            throw new Exception ('Oops! There was a problem trying to get this feed: ' . $url);
        }
        return true;
    }
}

?>