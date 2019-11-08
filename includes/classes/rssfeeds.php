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

use Aprelendo\Includes\Classes\RSSFeed;
use Aprelendo\Includes\Classes\Language;

class RSSFeeds
{
    private $feed1;
    private $feed2;
    private $feed3;

    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify feeds: $pdo, $user_id & lang_id
     * 
     * Gets up to 3 rss feeds for that user & language combination
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id) {
        try {
            $lang = new Language($pdo, $user_id);
            $lang->loadRecord($lang_id);

            $feed1uri = $lang->getRssFeed1Uri();
            $feed2uri = $lang->getRssFeed2Uri();
            $feed3uri = $lang->getRssFeed3Uri();

            $this->feed1 = new RSSFeed($feed1uri);
            $this->feed2 = new RSSFeed($feed2uri);
            $this->feed3 = new RSSFeed($feed3uri);
        } catch (\Exception $e) {
            throw new \Exception('Oops! There was an unexpected error trying to get your RSS feeds.');
        }        
    } // end __construct()

    /**
     * Get the value of feed1
     * @return RSSFeed
     */ 
    public function getFeed1(): RSSFeed
    {
        return $this->feed1;
    }

    /**
     * Get the value of feed2
     * @return RSSFeed
     */ 
    public function getFeed2(): RSSFeed
    {
        return $this->feed2;
    }

    /**
     * Get the value of feed3
     * @return RSSFeed
     */ 
    public function getFeed3(): RSSFeed
    {
        return $this->feed3;
    }
}


?>