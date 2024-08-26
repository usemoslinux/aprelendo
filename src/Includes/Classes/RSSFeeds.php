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

namespace Aprelendo;

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
    public function __construct(\PDO $pdo, int $user_id, int $lang_id)
    {
        try {
            $lang = new Language($pdo, $user_id);
            $lang->loadRecordById($lang_id);

            $feed1uri = $lang->rss_feed1_uri;
            $feed2uri = $lang->rss_feed2_uri;
            $feed3uri = $lang->rss_feed3_uri;

            $this->feed1 = new RSSFeed($feed1uri);
            $this->feed2 = new RSSFeed($feed2uri);
            $this->feed3 = new RSSFeed($feed3uri);
        } catch (\Exception $e) {
            throw new UserException($e->getMessage());
        }
    } // end __construct()

    /**
     * Return array containing all feeds
     * @return array
     */
    public function get(): array
    {
        $feeds = [];

        if (!empty($this->feed1->url)) {
            $feeds[] = $this->feed1;
        }

        if (!empty($this->feed2->url)) {
            $feeds[] = $this->feed2;
        }

        if (!empty($this->feed3->url)) {
            $feeds[] = $this->feed3;
        }

        return $feeds;
    }
}
