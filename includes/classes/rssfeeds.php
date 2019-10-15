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

use Aprelendo\Includes\Classes\RSSFeed;

class RSSFeeds
{
    public $feed1;
    public $feed2;
    public $feed3;

    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify feeds: $con, $user_id & learning_lang_id
     * 
     * Gets up to 3 rss feeds for that user & language combination
     *
     * @param mysqli_connect $con
     * @param integer $user_id
     * @param integer $learning_lang_id
     */
    public function __construct($con, $user_id, $learning_lang_id) {
        $lang = new Language($con, $user_id);
        $result = $lang->loadRecord($learning_lang_id);

        if ($result) {
            $feed1uri = $lang->getRssFeed1Uri();
            $feed2uri = $lang->getRssFeed2Uri();
            $feed3uri = $lang->getRssFeed3Uri();

            $this->feed1 = new RSSFeed($feed1uri);
            $this->feed2 = new RSSFeed($feed2uri);
            $this->feed3 = new RSSFeed($feed3uri);
        } else {
            throw new \Exception ('Oops! There was an unexpected error trying to get your RSS feeds.');
        }

        $stmt->close();
    }
}


?>