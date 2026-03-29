<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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
        $forgotten_status_value = WordStatus::forgotten->value;

        $sql = "SELECT COUNT(word) AS `recalled_today`
                FROM `{$this->table}`
                WHERE `user_id`=? AND `lang_id`=? AND `status` < ?
                AND `date_modified` > CURRENT_DATE()";

        return $this->sqlCount($sql, [$this->user_id, $this->lang_id, $forgotten_status_value]);
    } // end getRecalledToday()

    /**
     * Returns nr. of words reviewed by user, grouped by date
     *
     * @return array
     */
    public function getReviewsPerDay(): array
    {
        $sql = "SELECT DATE(`date_modified`) AS `date`, COUNT(*) AS `count`
                FROM `{$this->table}`
                WHERE `user_id`=? AND `lang_id`=?
                GROUP BY DATE(`date_modified`)
                ORDER BY DATE(`date_modified`) DESC";

        return $this->sqlFetchAll($sql, [$this->user_id, $this->lang_id]);
    }

    /**
     * Gets total stats for user words in a specific language, grouped by status
     *
     * @return array
     */
    public function getTotals(): array
    {
        $result = [];
        foreach (WordStatus::getAll() as $word_status) {
            $result[$word_status->value] = 0;
        }

        $sql = "SELECT `status`, COUNT(*) as count
            FROM `{$this->table}`
            WHERE `user_id`=? AND `lang_id`=?
            GROUP BY `status`";

        $status_totals = $this->sqlFetchAll($sql, [$this->user_id, $this->lang_id]);

        foreach ($status_totals as $status_total) {
            $word_status = WordStatus::tryFrom((int)$status_total['status']);
            if ($word_status !== null) {
                $result[$word_status->value] = (int)$status_total['count'];
            }
        }

        $result[4] = array_sum($result);

        return $result;
        
    } // end getTotals()
}
