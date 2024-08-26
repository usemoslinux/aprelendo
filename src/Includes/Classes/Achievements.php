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

class Achievements extends DBEntity
{
    private int $user_id = 0;
    private int $lang_id = 0;
    private string $time_zone = '';
    
    /**
    * Constructor
    *
    * @param \PDO $pdo
    * @param int $user_id
    * @param int $lang_id
    */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id, string $time_zone)
    {
        parent::__construct($pdo);
        $this->table = 'user_achievements';
        $this->user_id = $user_id;
        $this->lang_id = $lang_id;
        $this->time_zone = $time_zone;
    } // end __construct()
    
    /**
     * Calculates all user achievements, no matter if they were saved in the db or not
     *
     * @return array|null
     */
    public function checkAll(): ?array
    {
        // count gems & streak days
        $gems = new Gems($this->pdo, $this->user_id, $this->lang_id, $this->time_zone);
        $nr_of_gems  = (int)$gems->gems;
        $streak_days = (int)$gems->days_streak;
        
        // count words in user's vocabulary list for this particular language
        $words = new Words($this->pdo, $this->user_id, $this->lang_id);
        $word_count = $words->countSearchRows('');
        
        $gems_achievements = $this->checkByType(1, $nr_of_gems);
        $streak_achievements = $this->checkByType(2, $streak_days);
        $word_achievements = $this->checkByType(3, $word_count);

        return array_merge($gems_achievements, $streak_achievements, $word_achievements);
    } // end checkAll()

    /**
     * Verifies which user achievements were not yet saved in the db
     *
     * @return array
     */
    public function checkUnannounced(): array
    {
        // get all user achievements
        $all_achievements = $this->checkAll();
        $db_achievements = $this->checkSaved();
        
        // create a new array only with the differences between the previously created arrays
        // this new array will hold user achievements that were still not saved to the db
        // it's a nifty way to check what achievements need to be announced to the user and saved to the db
        $diff_array = [];

        if (count($all_achievements) > count($db_achievements)) {
            foreach ($all_achievements as $all_achievement) {
                foreach ($db_achievements as $db_achievement) {
                    $found_in_array = ($all_achievement['id'] == $db_achievement['achievement_id']);
                    if ($found_in_array) {
                        break;
                    }
                }
                if (!$found_in_array) {
                    $diff_array[] = $all_achievement;
                }
            }
        }

        return $diff_array;
    } // end checkUnannounced()

    /**
     * Saves in the db the achievements that are passed as a parameter
     *
     * @param array|null $achievements
     * @return void
     */
    public function saveUnannounced(?array $achievements): void
    {
        foreach ($achievements as $achievement) {
            $sql = "INSERT INTO `{$this->table}` (`user_id`, `lang_id`, `achievement_id`)
                VALUES (?, ?, ?); ";
            $this->sqlExecute($sql, [$this->user_id, $this->lang_id, $achievement['id']]);
        }
    } // end saveUnannounced()

    /**
     * Gets user achievements already saved in db
     *
     * @return array|null
     */
    public function checkSaved(): ?array
    {
        $sql = "SELECT ua.id, ua.user_id, ua.lang_id, ua.achievement_id, ua.date_created, a.description, a.img_uri
                FROM `{$this->table}` ua
                LEFT JOIN `achievements` a
                ON ua.achievement_id = a.id
                WHERE ua.user_id = ? AND ua.lang_id = ?
                ORDER BY ua.achievement_id ASC";

        return $this->sqlFetchAll($sql, [$this->user_id, $this->lang_id]);
    } // end checkSaved()

    /**
     * Returns achievements by $type_id and $threshold
     *
     * @param integer $type_id
     * @param integer $threshold
     * @return array|null
     */
    private function checkByType(int $type_id, int $threshold): ?array
    {
        $sql = "SELECT *
                FROM `achievements`
                WHERE `type_id`=? AND `threshold`<=?
                ORDER BY `threshold` ASC";

        return $this->sqlFetchAll($sql, [$type_id, $threshold]);
    } // end checkByType()
}
