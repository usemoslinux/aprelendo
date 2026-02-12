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

class Gems extends DBEntity
{
    public int $id                 = 0;
    public int $user_id            = 0;
    public int $lang_id            = 0;
    public int $gems               = 0;
    public $last_study_session     = null;  // last study session date
    public int $days_streak        = 0;     // study streak
    public bool $today_is_streak   = false; // indicates if user continued his streak today or not yet
    private string $time_zone      = '';
    
    /**
    * Constructor
    *
    * Sets 3 basic variables used to identify any record: $pdo, $user_id & lang_id
    *
    * @param \PDO $pdo
    * @param int $user_id
    * @param int $lang_id
    */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id, string $time_zone)
    {
        parent::__construct($pdo);
        $this->table = 'gems';
        $this->user_id = $user_id;
        $this->lang_id = $lang_id;
        $this->time_zone = $time_zone;
        $this->loadUserRecord();
    } // end __construct()

    /**
     * Loads gems record data
     *
     * @param int $id
     * @return void
     */
    private function loadUserRecord(): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `user_id` = ? AND `lang_id` = ?";
        $row = $this->sqlFetch($sql, [$this->user_id, $this->lang_id]);
        
        if ($row) {
            $this->id                 = $row['id'];
            $this->user_id            = $row['user_id'];
            $this->lang_id            = $row['lang_id'];
            $this->gems               = $row['gems'];
            $this->last_study_session = $row['last_study_session'];
            
            $diff_days = $this->calculateDaysFromLastStudy($row['last_study_session']);
            
            $this->days_streak        = ($diff_days == -1 || $diff_days == 0) ? $row['days_streak'] : 0;
            $this->today_is_streak    = $diff_days >= 0;
        }
    } // end loadRecord()
        
    /**
    * Updates user gems score in database
    *
    * @param array $events Describes the events which will then be translated to "gems"
    * @return int
    */
    public function updateScore(array $events): int
    {
        $gems = 0;

        $gems += $events['words']['new'] ?? 0;
        $gems += $events['words']['learning'] ?? 0;
        $gems -= $events['words']['forgotten'] ?? 0;
        $gems += ($events['texts']['new'] ?? 0) * 2;
        $gems += ($events['texts']['reviewed'] ?? 0) * 5;

        $min_gems = $gems < 0 ? 0 : $gems;

        // if text was reviewed, update last_study_session & calculate days_streak
        if (isset($events['texts']['reviewed'])) {
            if (!empty($this->last_study_session) && !empty($this->days_streak)) {
                // if last update was made yesterday, then we have a streak
                $diff_days = $this->calculateDaysFromLastStudy($this->last_study_session);
                
                if ($diff_days == -1) {
                    $this->days_streak++;
                } elseif ($diff_days < -1) {
                    $this->days_streak = 1;
                }
            } else {
                $this->days_streak = 1;
            }

            $today = new \DateTime("now", new \DateTimeZone($this->time_zone));  // current date/time
            $today->setTime(0, 0, 0); // reset time part, to prevent partial comparison
            $this->last_study_session = $today->format('Y-m-d H:i:s');
        }

        // create record if it does not exist, else update
        $sql = "INSERT INTO `{$this->table}` (`user_id`, `lang_id`, `gems`, `last_study_session`, `days_streak`)
                VALUES (?,?,`gems` + ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                `user_id`=?, `lang_id`=?, `gems`=`gems` + ?, `last_study_session`=?, `days_streak`=?";

        $this->sqlExecute($sql, [
            $this->user_id, $this->lang_id, $min_gems, $this->last_study_session, $this->days_streak,
            $this->user_id, $this->lang_id, $gems, $this->last_study_session, $this->days_streak
        ]);

        return $gems;
    } // end update()

    /**
     * Calculates the number of days from last study session (0 = today; -1 = yesterday and so on)
     *
     * @param string|null $last_study_session
     * @return integer
     */
    private function calculateDaysFromLastStudy(?string $last_study_session) : int
    {
        if (!isset($last_study_session) || empty($last_study_session)) {
            return -365;
        }
        
        $today = new \DateTime("now", new \DateTimeZone($this->time_zone));  // current date/time
        $today->setTime(0, 0, 0); // reset time part, to prevent partial comparison
        
        $last_study_session = \DateTime::createFromFormat('Y-m-d H:i:s', $last_study_session);
        $last_study_session->setTime(0, 0, 0); // reset time part, to prevent partial comparison
        
        $diff = $today->diff($last_study_session);
        return (integer)$diff->format("%R%a"); // Extract days count in interval
    }
}
