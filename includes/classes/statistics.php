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

        // get how many words were created in each of the last $days
        for ($i=$days; $i >= 0; $i--) {
            $sql = "SELECT COUNT(word) AS `created` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `date_created` < CURDATE() - INTERVAL ?-1 DAY AND `date_created` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['created'][] = $row['created'];
        }

        // get how many words' status were modified in each of the last $days
        for ($i=$days; $i >= 0; $i--) { 
            $sql = "SELECT COUNT(word) AS `modified` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `status`=1 AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY AND `date_modified` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['modified'][] = $row['modified'];
        }

        // get how many words were learned in each of the last $days
        for ($i=$days; $i >= 0; $i--) { 
            $sql = "SELECT COUNT(word) AS `learned` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `status`=0 AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY AND `date_modified` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['learned'][] = $row['learned'];
        }

        // get how many learned words were forgotten in each of the last $days
        for ($i=$days; $i >= 0; $i--) { 
            $sql = "SELECT COUNT(word) AS `forgotten` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `status`=2 AND `date_modified`>`date_created` AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY AND `date_modified` > CURDATE() - INTERVAL ? DAY";
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
    
    private function checkFirstUploadByType(int $days, int $text_type) {
        $result = [];

        // get nr. of uploaded texts of each type
        $sql = "SELECT `user_id`, `type`, 
                    COUNT(`text`) AS `count_total`,
                    SUM(`date_created` >= CURDATE() - INTERVAL ? DAY) AS `count_last_week`
                FROM `texts`
                WHERE `user_id`=? AND `lang_id`=? AND `type`=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$days, $this->user_id, $this->lang_id, $text_type]);
        $row_texts = $stmt->fetch();
        
        // get nr. of uploaded shared texts of each type
        $sql = "SELECT `user_id`, `type`, 
                    COUNT(`text`) AS `count_total`,
                    SUM(`date_created` >= CURDATE() - INTERVAL ? DAY) AS `count_last_week`
                FROM `shared_texts`
                WHERE `user_id`=? AND `lang_id`=? AND `type`=?";
                $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$days, $this->user_id, $this->lang_id, $text_type]);
        $row_shared_texts = $stmt->fetch();
        
        // add up both results
        $row_sum = array();
        foreach (array_keys($row_texts + $row_shared_texts) as $key) {
            $row_sum[$key] = (isset($row_texts[$key]) ? $row_texts[$key] : 0) + (isset($row_shared_texts[$key]) ? $row_shared_texts[$key] : 0);
        }
        
        // check if user uploaded in the last $days a new text of this $type for the first time 
        if ($row_sum['count_total'] > 0 AND $row_sum['count_total'] == $row_sum['count_last_week'] ) {
            $result = TRUE;
        }

        return $result;
    }

    private function isMostActiveAllTimeUploader() {
        $lang = new Language($this->pdo, $this->user_id);
        $lang->loadRecord($this->lang_id);
        $lang_iso = $lang->getName();

        $sql = "SELECT t.`user_id` AS `user_id`, t.lang_id AS `lang_id`, COUNT(t.`text`)
                FROM texts AS t
                INNER JOIN languages AS l ON t.lang_id = l.id 
                WHERE l.name='$lang_iso'
                GROUP BY `lang_id`
                ORDER BY `user_id` ASC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row['user_id'] == $this->user_id;
    }

}

?>