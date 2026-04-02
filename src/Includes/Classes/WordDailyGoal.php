<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class WordDailyGoal extends DBEntity
{
    public $id              = 0;
    public $user_id         = 0;
    public $lang_id         = 0;
    public $last_streak     = null; // last streak date
    public $days_streak     = 0;    // nr of days streak
    public $daily_goal      = 10;
    private $time_zone      = '';
    private $diff_days      = -2;   // older than 1 day, ergo no streak
    
    /**
    * Constructor
    *
    * Sets 3 basic variables used to identify any record: $pdo, $user_id & lang_id
    *
    * @param \PDO $pdo
    * @param int $user_id
    * @param int $lang_id
    */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id, string $time_zone, bool $today_is_streak)
    {
        parent::__construct($pdo);
        $this->table = 'word_daily_goal';
        $this->user_id = $user_id;
        $this->lang_id = $lang_id;
        $this->time_zone = $time_zone;
        $this->loadRecord($today_is_streak);
    } 

    /**
     * Loads word_daily_goal record data
     *
     * @param int $id
     * @return void
     */
    private function loadRecord(bool $today_is_streak): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `lang_id` = ?";
        $row = $this->sqlFetch($sql, [$this->lang_id]);

        if ($row) {
            $this->id                 = $row['id'];
            $this->user_id            = $row['user_id'];
            $this->lang_id            = $row['lang_id'];
            $this->last_streak        = $row['last_streak'];
            $this->daily_goal         = $row['daily_goal'];

            $this->diff_days = $this->calculateDaysFromLastStreak($this->last_streak);
            // if $this->diff_days = yesterday (-1) or today (0)
            $this->days_streak        = ($this->diff_days == -1 || $this->diff_days == 0) ? $row['days_streak'] : 0;
        }

        // if $today_is_streak, but still not saved, then update record
        if ($today_is_streak) {
            $this->update();
        }
    } 
        
    /**
    * Updates user daily goal table
    *
    * @return void
    */
    public function update(): void
    {
        // if $this->diff_days = yesterday: -1; older than yesterday: < -1
        if ($this->diff_days == -1) {
            $this->days_streak++;
        } elseif ($this->diff_days < -1) {
            $this->days_streak = 1;
        }

        $today = new \DateTime("now", new \DateTimeZone($this->time_zone));  // current date/time
        $today->setTime(0, 0, 0); // reset time part, to prevent partial comparison
        $this->last_streak = $today->format('Y-m-d H:i:s');
        $this->diff_days = 0;

        // create record if it does not exist, else update
        $sql = "INSERT INTO `{$this->table}` (`user_id`, `lang_id`, `last_streak`, `days_streak`)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                `user_id`=?, `lang_id`=?, `last_streak`=?, `days_streak`=?";
        $this->sqlExecute($sql, [
            $this->user_id, $this->lang_id, $this->last_streak, $this->days_streak,
            $this->user_id, $this->lang_id, $this->last_streak, $this->days_streak
        ]);
    } 

    /**
     * Calculates the number of days from last streak (0 = today; -1 = yesterday and so on)
     *
     * @param string|null $last_streak_date
     * @return integer
     */
    private function calculateDaysFromLastStreak(?string $last_streak_date) : int
    {
        if (!isset($last_streak_date) || empty($last_streak_date)) {
            return -365;
        }
        
        $today = new \DateTime("now", new \DateTimeZone($this->time_zone));  // current date/time
        $today->setTime(0, 0, 0); // reset time part, to prevent partial comparison
        
        $last_streak_date = \DateTime::createFromFormat('Y-m-d H:i:s', $last_streak_date);
        $last_streak_date->setTime(0, 0, 0); // reset time part, to prevent partial comparison
        
        $diff = $today->diff($last_streak_date);
        return (integer)$diff->format("%R%a"); // Extract days count in interval
    }
}
