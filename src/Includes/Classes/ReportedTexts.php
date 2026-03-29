<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class ReportedTexts extends DBEntity
{
    public int $id;
    public int $text_id;
    public int $user_id;
    public string $reason;

    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $text_id
     * @param int $user_id
     */
    public function __construct(\PDO $pdo, int $text_id, int $user_id)
    {
        parent::__construct($pdo);
        $this->table = 'reported_texts';
        $this->text_id = $text_id;
        $this->user_id = $user_id;
    } // end __construct()

    /**
     * Adds record to reported_texts table
     *
     * @param string $reason
     * @return void
     */
    public function add(string $reason): void
    {
        if ($this->exists()) {
            throw new UserException("You have already reported this content. It is now under review.");
        }
        
        $this->reason  = $reason;

        $sql = "INSERT INTO `{$this->table}` (`text_id`, `user_id`, `reason`)
                VALUES (?, ?, ?)";
        $this->sqlExecute($sql, [$this->text_id,$this->user_id, $this->reason]);
    } // end add()

    /**
     * Checks if text already was reported by this same user.
     * To do so, it checks the reported_texts table by text_id & user_id.
     *
     * @return boolean
     */
    private function exists(): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `text_id` = ? AND `user_id` = ?";
        return $this->sqlCount($sql, [$this->text_id, $this->user_id]);
    } // end exists()

    /**
     * Loads reported text data into class properties
     *
     * @param int $text_id
     * @return void
     */
    public function loadRecord(): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `text_id` = ?";
        $row = $this->sqlFetch($sql, [$this->text_id]);
        
        if ($row) {
            $this->reason = $row['reason'];
        }
    } // end loadRecord()
}
