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

class wordStats extends Statistics {
    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id)
    {
        parent::__construct($pdo, $user_id, $lang_id);
        $this->table = 'words';
    } // end __construct()
    
    /**
     * Returns array with user word stats
     *
     * @param int $days the amount of days of the interval (7=1 week), or index of day to show stats (today=1, yesterday=2)
     * @param bool $interval retrieve stats for an interval or 1 day only?
     * @return array
     */
    public function get(int $days, bool $interval): array
    {
        --$days;

        // get how many words acquired "learned" status in each of the last $days
        for ($i=$days; $i >= 0; $i--) {
            $sql = "SELECT COUNT(word) AS `learned`
                    FROM `{$this->table}`
                    WHERE `user_id`=? AND `lang_id`=? AND `status`=0 AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY
                    AND `date_modified` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $i, $i]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stats['learned'][] = $row['learned'];
            
            if (!$interval) break;
        }

        // get how many words acquired "learning" status in each of the last $days
        for ($i=$days; $i >= 0; $i--) {
            $sql = "SELECT SUM(learning) learning
                    FROM
                    (SELECT COUNT(word) AS `learning`
                        FROM `{$this->table}`
                        WHERE `user_id`=? AND `lang_id`=? AND `status`=1
                        AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY
                        AND `date_modified` > CURDATE() - INTERVAL ? DAY
                    UNION ALL
                    SELECT COUNT(word) AS `learning`
                        FROM `{$this->table}`
                        WHERE `user_id`=? AND `lang_id`=? AND `status`=2
                        AND `date_modified` > `date_created`
                        AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY
                        AND `date_modified` > CURDATE() - INTERVAL ? DAY) s";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $i, $i, $this->user_id, $this->lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['learning'][] = $row['learning'];
            
            if (!$interval) break;
        }

        // get how many words acquired "new" status in each of the last $days
        for ($i=$days; $i >= 0; $i--) {
            $sql = "SELECT COUNT(word) AS `new`
                    FROM `{$this->table}`
                    WHERE `user_id`=? AND `lang_id`=? AND `status`=2
                    AND `date_modified` = `date_created`
                    AND `date_created` < CURDATE() - INTERVAL ?-1 DAY
                    AND `date_created` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['new'][] = $row['new'];

            if (!$interval) break;
        }

        // get how many learned words acquired "forgotten" status in each of the last $days
        for ($i=$days; $i >= 0; $i--) {
            $sql = "SELECT COUNT(word) AS `forgotten`
                    FROM `{$this->table}`
                    WHERE `user_id`=? AND `lang_id`=? AND `status`=3
                    AND `date_modified` < CURDATE() - INTERVAL ?-1 DAY
                    AND `date_modified` > CURDATE() - INTERVAL ? DAY";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $i, $i]);
            $row = $stmt->fetch();
            $stats['forgotten'][] = $row['forgotten'];

            if (!$interval) break;
        }
        
        return $stats;
    } // end get()

    /**
     * Gets total stats for user words in a specific language, grouped by status
     *
     * @return array
     */
    public function getTotals(): array {
        try {
            $sql = "SELECT COUNT(*) as count
                 FROM `{$this->table}`
                 WHERE `user_id`=? AND `lang_id`=?
                 GROUP BY `status`";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id]);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            throw new AprelendoException('There was an unexpected error trying to get word stats.');
        } finally {
            $stmt = null;
        }
    } // end getTotals()
}