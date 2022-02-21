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

use Aprelendo\Includes\Classes\DBEntity;

class Statistics extends DBEntity {
    private $lang_id = 0;

    /**
     * Constructor
     *
     * @param PDO $pdo 
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id) {
        parent::__construct($pdo, $user_id);
        $this->lang_id = $lang_id;
        
    } // end __construct()

    /**
     * Returns array with user word stats
     *
     * @param int $days
     * @return array
     */
    public function get(int $days): array {    
        $this->table = 'words';    
        --$days;

        // get how many words acquired "learned" status in each of the last $days
        for ($i=$days; $i >= 0; $i--) { 
            $sql = "SELECT COUNT(word) AS `learned` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `status`=0 AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY AND `date_modified` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['learned'][] = $row['learned'];
        }

        // get how many words acquired "learning" status in each of the last $days
        for ($i=$days; $i >= 0; $i--) { 
            $sql = "SELECT SUM(learning) learning
                    FROM
                    (SELECT COUNT(word) AS `learning` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `status`=1 AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY AND `date_modified` > CURDATE() - INTERVAL ? DAY
                    UNION ALL
                    SELECT COUNT(word) AS `learning` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `status`=2 AND `date_modified` > `date_created` AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY AND `date_modified` > CURDATE() - INTERVAL ? DAY) s";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $i, $i, $this->user_id, $this->lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['learning'][] = $row['learning'];
        }

        // get how many words acquired "new" status in each of the last $days
        for ($i=$days; $i >= 0; $i--) {
            $sql = "SELECT COUNT(word) AS `new` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `status`=2 AND `date_created` < CURDATE() - INTERVAL ?-1 DAY AND `date_created` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['new'][] = $row['new'];
        }

        // get how many learned words acquired "forgotten" status in each of the last $days
        for ($i=$days; $i >= 0; $i--) { 
            $sql = "SELECT COUNT(word) AS `forgotten` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `status`=3 AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY AND `date_modified` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['forgotten'][] = $row['forgotten'];
        }
        
        return $stats;
    } // end get()

    /**
     * Returns array with user text stats
     *
     * @param integer $days
     * @return void
     */
    public function getTextStats(int $days) {
        $this->table = 'texts';
        --$days;

        // get how many texts were created in each of the last $days
        $sql = "SELECT COUNT(text) AS `total_created` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND 
            `date_created` >= CURDATE() - INTERVAL ? DAY";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->user_id, $this->lang_id, $days]);
        $row = $stmt->fetch();
        $stats['total_created'] = $row['total_created'];

        // check if user uploaded in the last $days a new text type for the first time 
        // types: 1 = Articles; 2 = Conversations; 3 = Letters; 4 = Lyrics; 5 = Videos; 6 = Ebooks; 7 = Others
        for ($text_type=1; $text_type < 8; $text_type++) { 
            $stats['first_upload'][$text_type] = $this->checkFirstUploadByType($days, $text_type);
        }

        $stats['most_active_alltime_uploader'] = $this->isMostActiveAllTimeUploader();

        return $stats;
    } // end getTextStats()
}

?>