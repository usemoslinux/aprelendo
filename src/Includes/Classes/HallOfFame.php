<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class HallOfFame extends DBEntity
{
    protected string $lang_iso  = "";
    protected int $top_limit = 5;
    protected string $period = "";
    private static array $valid_periods = ['weekly', 'monthly', 'all_time'];

    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param string $lang_iso
     * @param string $period
     */
    public function __construct(\PDO $pdo, string $lang_iso, string $period)
    {
        parent::__construct($pdo);
        $this->lang_iso = $lang_iso;

        $valid_periods = ['weekly', 'monthly', 'all_time'];
        if (!in_array($period, self::$valid_periods, true)) {
            throw new \InvalidArgumentException("Invalid period: $period");
        }
        $this->period = $period;
    } // end __construct()

    /**
     * Returns nr. of users who have added at least one word
     *
     * @return int
     */
    public function getActiveLearners(): int
    {
        $learned_status_value = WordStatus::learned->value;

        $sql = "SELECT 
                    COUNT(DISTINCT w.user_id) AS users
                FROM 
                    words AS w
                JOIN 
                    languages AS l ON w.lang_id = l.id
                WHERE 
                    l.name = '{$this->lang_iso}' AND w.status = {$learned_status_value}";

        return $this->sqlCount($sql);
    } // end getActiveLearners()

    /**
     * Returns total nr. of words added by all users
     *
     * @return int
     */
    public function getTotalWordsLearned(): int
    {
        $learned_status_value = WordStatus::learned->value;

        $sql = "SELECT 
                    COUNT(*) AS words
                FROM 
                    words AS w
                JOIN 
                    languages AS l ON w.lang_id = l.id
                WHERE 
                    l.name = '{$this->lang_iso}' AND w.status = {$learned_status_value}";

        return $this->sqlCount($sql);
    } // end getTotalWordsLearned()

    /**
     * Returns total nr. of gems eaerned by all users
     *
     * @return int
     */
    public function getTotalGemsEarned(): int
    {
        $sql = "SELECT 
                    SUM(gems) AS gems
                FROM 
                    gems AS g
                JOIN 
                    languages AS l ON g.lang_id = l.id
                WHERE 
                    l.name = '{$this->lang_iso}'";

        return $this->sqlCount($sql);
    } // end getTotalGemsEarned()

    /**
     * Returns average streak considering all users
     *
     * @return int
     */
    public function GetAvgStreakDays(): int
    {
        $sql = "SELECT 
                    AVG(days_streak) AS days_streak
                FROM 
                    gems AS g
                JOIN 
                    languages AS l ON g.lang_id = l.id
                WHERE 
                    l.name = '{$this->lang_iso}'";

        $result = $this->sqlFetch($sql)['days_streak'];
        return $result ? (int)round($result) : 0;
    } // end GetAvgStreakDays()

    /**
     * Returns top users with most gems earned (user name & nr. of gems)
     *
     * @return array
     */
    public function GetTopGemsEarned(): array
    {
        $sql = "SELECT 
                    u.name AS `user_name`,
                    g.gems AS `value`
                FROM 
                    gems AS g
                JOIN 
                    users AS u ON g.user_id = u.id
                JOIN 
                    languages AS l ON g.lang_id = l.id
                WHERE 
                    l.name = '{$this->lang_iso}'
                ORDER BY 
                    g.gems DESC
                LIMIT {$this->top_limit}";

        return $this->sqlFetchAll($sql);
    } // end GetTopGemsEarned()

    /**
     * Returns top users with longest streaks (user name & longest streak)
     *
     * @return array
     */
    public function getTopStreaks(): array
    {
        $sql = "SELECT 
                    u.name AS `user_name`,
                    g.days_streak AS `value`,
                    g.last_study_session AS `last_study_session`
                FROM 
                    gems AS g
                JOIN 
                    users AS u ON g.user_id = u.id
                JOIN 
                    languages AS l ON g.lang_id = l.id
                WHERE 
                    l.name = '{$this->lang_iso}'
                
                ORDER BY `days_streak` DESC, u.name ASC
                LIMIT {$this->top_limit}";

        return $this->sqlFetchAll($sql);
    } // end getTopStreaks()

    /**
     * Returns top users with most words learned (user name & nr. of words learned)
     *
     * @return array
     */
    public function getTopWordsLearned(): array
    {
        $learned_status_value = WordStatus::learned->value;

        $sql = "SELECT 
                    u.name AS `user_name`,
                    COUNT(*) AS `value`
                FROM 
                    words AS w
                JOIN 
                    users AS u ON w.user_id = u.id
                JOIN 
                    languages AS l ON w.lang_id = l.id
                WHERE 
                    l.name = '{$this->lang_iso}' AND w.status = {$learned_status_value}
                GROUP BY u.id
                ORDER BY `value` DESC, u.name ASC
                LIMIT {$this->top_limit}";

        return $this->sqlFetchAll($sql);
    } // end getTopWordsLearned()

    /**
     * Returns top achievement hunters (user names & nr. of achievements)
     *
     * @return array
     */
    public function getTopAchievements(): array
    {
        $sql = "SELECT 
                    u.name AS `user_name`,
                    COUNT(*) AS `value`
                FROM 
                    user_achievements AS a
                JOIN 
                    users AS u ON a.user_id = u.id
                JOIN 
                    languages AS l ON a.lang_id = l.id
                WHERE 
                    l.name = '{$this->lang_iso}'
                GROUP BY u.id
                ORDER BY `value` DESC, u.name ASC
                LIMIT {$this->top_limit}";

        return $this->sqlFetchAll($sql);
    } // end getTopAchievements()

    /**
     * Builds the HTML leaderboard list.
     *
     * @param array $leaderboard_data
     * @param string $follow_str
     * @return string
     */
    public function print_leaderboard(array $leaderboard_data, string $follow_str): string
    {
        $total_rows = max($this->top_limit, count($leaderboard_data));
        $html = '';

        for ($i = 0; $i < $total_rows; $i++) {
            $rank = $i + 1;

            if (isset($leaderboard_data[$i])) {
                $name = htmlspecialchars($leaderboard_data[$i]['user_name']);
                $value = number_format((int)$leaderboard_data[$i]['value']);
            } else {
                $name = 'N/A';
                $value = '0';
            }

            // Determine badge and trophy style
            [$icon, $badge_class] = match ($rank) {
                1 => ['<i class="bi bi-trophy-fill trophy-icon trophy-gold"></i>', 'rank-badge rank-1 me-3'],
                2 => ['<i class="bi bi-trophy-fill trophy-icon trophy-silver"></i>', 'rank-badge rank-2 me-3'],
                3 => ['<i class="bi bi-trophy-fill trophy-icon trophy-bronze"></i>', 'rank-badge rank-3 me-3'],
                default => ['', 'rank-badge bg-secondary text-white me-3'],
            };

            $html .= <<<HTML_LEADERBOARD
            <div class="list-group-item d-flex align-items-center">
                <div class="$badge_class">$rank</div>
                <div class="flex-grow-1">
                    <strong><a href="/stats?u=$name">$name</a></strong>
                    <div>$value <small class="text-muted">$follow_str</small></div>
                </div>
                $icon
            </div>
            HTML_LEADERBOARD;
        }

        return $html;
    } // end print_leaderboard()

    /**
     * Builds the HTML value for a community statistic.
     *
     * @param int $community_stat
     * @return string
     */
    public function print_community_stat(int $community_stat): string
    {
        $html = '';

        if (isset($community_stat)) {
            $value = number_format($community_stat);
        } else {
            $value = '-';
        }

        $html = "<h4 class='mt-3'>$value</h4>";

        return $html;
    } // end print_community_stat()
}
