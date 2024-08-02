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
    public function getReviewedToday(): int
    {
        $sql = "SELECT COUNT(word) AS `reviewed_today`
                FROM `{$this->table}`
                WHERE `user_id`=? AND `lang_id`=? AND `status` < 3
                AND `date_modified` > CURRENT_DATE()";

        return $this->sqlCount($sql, [$this->user_id, $this->lang_id]);
    } // end get()

    /**
     * Gets total stats for user words in a specific language, grouped by status
     *
     * @return array
     */
    public function getTotals(): array
    {
        $sql = "SELECT COUNT(*) as count
            FROM `{$this->table}`
            WHERE `user_id`=? AND `lang_id`=?
            GROUP BY `status`";

        return $this->sqlFetchAll($sql, [$this->user_id, $this->lang_id]);
    } // end getTotals()
}
