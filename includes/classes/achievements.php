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
use Aprelendo\Includes\Classes\Gems;
use Aprelendo\Includes\Classes\Words;

class Achievements extends DBEntity
{
    private $lang_id = 0;
    private $time_zone = '';
    
    /**
    * Constructor
    *
    * @param PDO $pdo
    * @param int $user_id
    * @param int $lang_id
    */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id, string $time_zone)
    {
        parent::__construct($pdo, $user_id);
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
        $nr_of_gems  = (int)$gems->getGems();
        $streak_days = (int)$gems->getDaysStreak();
        
        // count words in user's vocabulary list for this particular language
        $words = new Words($this->pdo, $this->user_id, $this->lang_id);
        $word_count = $words->countSearchRows('');
        
        $gems_achievements = $this->checkByType(1, $nr_of_gems);
        $streak_achievements = $this->checkByType(2, $streak_days);
        $word_achievements = $this->checkByType(3, $word_count);

        return array_merge($gems_achievements, $streak_achievements, $word_achievements);
    }

    /**
     * Verifies which user achievements were not yet saved in the db
     *
     * @return array
     */
    public function checkUnannounced(): array
    {
        // get all user achievements
        $all_achievements = $this->checkAll();
        
        try {
            // get user achievements already saved in db
        $sql = "SELECT *
                FROM `user_achievements`
                WHERE `user_id`=? AND `lang_id`=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->user_id, $this->lang_id]);
        $db_achievements = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to insert new record in user '
                . 'achievements table.');
        } finally {
            $stmt = null;
        }
        
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
    }

    /**
     * Saves in the db the achievements that are passed as a parameter
     *
     * @param array|null $achievements
     * @return void
     */
    public function saveUnannounced(?array $achievements): void
    {
        try {
            foreach ($achievements as $achievement) {
                $sql = "INSERT INTO `user_achievements` (`user_id`, `lang_id`, `achievement_id`)
                    VALUES (?, ?, ?); ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$this->user_id, $this->lang_id, $achievement['id']]);
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to insert new record in user '
            . 'achievements table.');
        } finally {
            $stmt = null;
        }
    }

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
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$type_id, $threshold]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
