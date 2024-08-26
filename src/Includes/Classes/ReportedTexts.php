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
