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

class textStats extends Statistics {

    /**
     * Returns array with user text stats
     *
     * @todo implement this method
     * @param int $days the amount of days of the interval (7=1 week), or index of day to show stats (today=1, yesterday=2)
     * @param bool $interval retrieve stats for an interval or 1 day only?
     * @return array
     */
    public function get(int $days, bool $interval): array
    {
        // $this->table = 'texts';
        // --$days;

        // // get how many texts were created in each of the last $days
        // $sql = "SELECT COUNT(text) AS `total_created`
        //         FROM `{$this->table}`
        //         WHERE `user_id`=? AND `lang_id`=?
        //         AND `date_created` >= CURDATE() - INTERVAL ? DAY";
        // $stmt = $this->pdo->prepare($sql);
        // $stmt->execute([$this->user_id, $this->lang_id, $days]);
        // $row = $stmt->fetch();
        // $stats['total_created'] = $row['total_created'];

        // // check if user uploaded in the last $days a new text type for the first time
        // // types: 1 = Articles; 2 = Conversations; 3 = Letters; 4 = Lyrics; 5 = Videos; 6 = Ebooks; 7 = Others
        // for ($text_type=1; $text_type < 8; $text_type++) {
        //     $stats['first_upload'][$text_type] = $this->checkFirstUploadByType($days, $text_type);
        // }

        // $stats['most_active_alltime_uploader'] = $this->isMostActiveAllTimeUploader();

        // return $stats;
        return [];
    } // end getTextStats()
}