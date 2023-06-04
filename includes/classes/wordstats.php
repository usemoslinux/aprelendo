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

class wordStats extends DBEntity {
    protected $lang_id = 0;

    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id)
    {
        parent::__construct($pdo, $user_id);
        $this->table = 'words';
        $this->lang_id = $lang_id;
    } // end __construct()
    
    /**
     * Returns nr. of words reviewed by user today, except those marked as forgotten
     *
     * @return int
     */
    public function getReviewedToday(): int
    {
        try {
            $sql = "SELECT COUNT(word) AS `reviewed_today`
                    FROM `{$this->table}`
                    WHERE `user_id`=? AND `lang_id`=? AND `status` < 3
                    AND `date_modified` > CURRENT_DATE()";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $row ? $row['reviewed_today'] : 0;
        } catch (\PDOException $e) {
            throw new AprelendoException('There was an unexpected error trying to get word stats.');
        } finally {
            $stmt = null;
        }
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