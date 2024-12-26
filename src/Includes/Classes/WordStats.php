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

class WordStats extends DBEntity
{
    protected int $user_id = 0;
    protected int $lang_id = 0;

    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id)
    {
        parent::__construct($pdo);
        $this->table = 'words';
        $this->user_id = $user_id;
        $this->lang_id = $lang_id;
    } // end __construct()

    /**
     * Returns nr. of words reviewed by user today, except those marked as forgotten
     *
     * @return int
     */
    public function getRecalledToday(): int
    {
        $sql = "SELECT COUNT(word) AS `recalled_today`
                FROM `{$this->table}`
                WHERE `user_id`=? AND `lang_id`=? AND `status` < 3
                AND `date_modified` > CURRENT_DATE()";

        return $this->sqlCount($sql, [$this->user_id, $this->lang_id]);
    } // end getRecalledToday()

    /**
     * Gets total stats for user words in a specific language, grouped by status
     *
     * @return array
     */
    public function getTotals(): array
    {
        $temp = [];
        $result = [0 => 0,  // learned
                   1 => 0,  // learning
                   2 => 0,  // new
                   3 => 0,  // forgotten
                   4 => 0]; // total

        $sql = "SELECT `status`, COUNT(*) as count
            FROM `{$this->table}`
            WHERE `user_id`=? AND `lang_id`=?
            GROUP BY `status`";

        $temp = $this->sqlFetchAll($sql, [$this->user_id, $this->lang_id]);

        foreach ($temp as $value) {
            $result[$value['status']] = $value['count'];
        }

        $result[4] = $result[0] + $result[1] + $result[2] + $result[3];

        return $result;
        
    } // end getTotals()
}
