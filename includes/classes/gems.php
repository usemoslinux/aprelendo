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

use Aprelendo\Includes\Classes\AprelendoException;

class Gems extends DBEntity
{
    protected $id                 = 0;
    protected $user_id            = 0;
    protected $lang_id            = 0;
    protected $gems               = 0;
    protected $last_study_session = null;  // last study session date
    protected $days_streak        = 0;     // study streak
    protected $today_is_streak    = false; // indicates if user continued his streak today or not yet
    private   $time_zone          = '';
    
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
        parent::__construct($pdo, $user_id);
        $this->lang_id = $lang_id;
        $this->table = 'gems';
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
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `lang_id` = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->lang_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->id                 = $row['id'];
                $this->user_id            = $row['user_id'];
                $this->lang_id            = $row['lang_id'];
                $this->gems               = $row['gems'];
                $this->last_study_session = $row['last_study_session'];
                
                $diff_days = $this->calculateDaysFromLastStudy($row['last_study_session']);
                
                $this->days_streak        = ($diff_days == -1 || $diff_days == 0) ? $row['days_streak'] : 0;
                $this->today_is_streak    = $this->days_streak > 0 || $diff_days == 0;
            }
        } catch (\PDOException $e) {
            throw new AprelendoException('There was an unexpected error trying to load record from gems table.');
        } finally {
            $stmt = null;
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
        try {
            // calculate nr. of gems to add
            $gems = 0;

            $gems += isset($events['words']['new'])       ? $events['words']['new']          : 0;
            $gems += isset($events['words']['learning'])  ? $events['words']['learning']     : 0;
            $gems -= isset($events['words']['forgotten']) ? $events['words']['forgotten']    : 0;
            
            $gems += isset($events['texts']['new'])       ? $events['texts']['new']      * 2 : 0;
            $gems += isset($events['texts']['reviewed'])  ? $events['texts']['reviewed'] * 5 : 0;

            // if text was reviewed, update last_study_session & calculate days_streak
            if (isset($events['texts']['reviewed'])) {
                if (isset($this->last_study_session) && !empty($this->last_study_session) &&
                    isset($this->days_streak) && !empty($this->days_streak)) {
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
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->lang_id, $gems, $this->last_study_session, $this->days_streak,
                            $this->user_id, $this->lang_id, $gems, $this->last_study_session, $this->days_streak]);
            
            return $gems;
        } catch (\PDOException $e) {
            throw new AprelendoException('There was an unexpected error trying to update record from gems table.');
        } finally {
            $stmt = null;
        }
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
    
    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    } // end getId()

    /**
     * Get the value of user_id
     */
    public function getUserId(): int
    {
        return $this->user_id;
    } // end getUserId()

    /**
     * Get the value of lang_id
     */
    public function getLangId(): int
    {
        return $this->lang_id;
    } // end getLangId()

    /**
     * Get the value of gems
     */
    public function getGems(): int
    {
        return $this->gems;
    } // end getGems()

    /**
     * Get the value of last_study_session
     */
    public function getLastStudySession(): ?string
    {
        return $this->last_study_session;
    } // end getLastStudySession()
    
    /**
     * Get the value of days_streak
     */
    public function getDaysStreak(): ?int
    {
        return $this->days_streak;
    } // end getDaysStreak()

    /**
     * Get the value of today_is_streak
     */
    public function getTodayIsStreak(): bool
    {
        return $this->today_is_streak;
    } // end getTodayIsStreak()
}
