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

use Aprelendo\Includes\Classes\DBEntity;

class Statistics extends DBEntity {
    private $learning_lang_id = 0;

    /**
     * Constructor
     *
     * @param PDO $con 
     * @param int $user_id
     * @param int $learning_lang_id
     */
    public function __construct(\PDO $con, int $user_id, int $learning_lang_id) {
        parent::__construct($con, $user_id);
        $this->learning_lang_id = $learning_lang_id;
        $this->table = 'words';
    }

    /**
     * Returns array with 
     *
     * @param integer $days
     * @return array
     */
    public function get(int $days): array {        
        --$days;

        // get how many words were created in each of the last 7 days
        for ($i=$days; $i >= 0; $i--) {
            $sql = "SELECT COUNT(word) AS `created` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `date_created` < CURDATE() - INTERVAL ?-1 DAY AND `date_created` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $this->learning_lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['created'][] = $row['created'];
        }

        // get how many words' status were modified in each of the last 7 days
        for ($i=$days; $i >= 0; $i--) { 
            $sql = "SELECT COUNT(word) AS `modified` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `status`>0 AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY AND `date_modified` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $this->learning_lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['modified'][] = $row['modified'];
        }

        // get how many words were learned in each of the last 7 days
        for ($i=$days; $i >= 0; $i--) { 
            $sql = "SELECT COUNT(word) AS `learned` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `status`=0 AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY AND `date_modified` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $this->learning_lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['learned'][] = $row['learned'];
        }

        // get how many learned words were forgotten in each of the last 7 days
        for ($i=$days; $i >= 0; $i--) { 
            $sql = "SELECT COUNT(word) AS `forgotten` FROM `{$this->table}` WHERE `user_id`=? AND `lang_id`=? AND `status`=2 AND `date_modified`>`date_created` AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY AND `date_modified` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->user_id, $this->learning_lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['forgotten'][] = $row['forgotten'];
        }
        
        return $stats;
    }

}

?>