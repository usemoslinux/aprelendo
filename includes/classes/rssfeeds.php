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
        $user_id = $con->real_escape_string($user_id);
        $learning_lang_id = $con->real_escape_string($learning_lang_id);

        $result = $con->query("SELECT `rss_feed1_uri`, `rss_feed2_uri`, `rss_feed3_uri` FROM `languages` WHERE `user_id`='$user_id' AND `id`='$learning_lang_id'");

        if ($result) {
            $rows = $result->fetch_assoc();
            $feed1uri = $rows['rss_feed1_uri'];
            $feed2uri = $rows['rss_feed2_uri'];
            $feed3uri = $rows['rss_feed3_uri'];

            $this->feed1 = new RSSFeed($feed1uri);
            $this->feed2 = new RSSFeed($feed2uri);
            $this->feed3 = new RSSFeed($feed3uri);
        } else {
            throw new \Exception ('Oops! There was an unexpected error trying to get your RSS feeds.');
        }
    }
}


?>