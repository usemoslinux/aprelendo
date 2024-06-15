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

namespace Aprelendo;

use Aprelendo\DBEntity;

class Likes extends DBEntity
{
    private int $user_id = 0;
    private int $text_id = 0;
    private int $lang_id = 0;
    
    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $text_id
     * @param int $user_id
     * @param int $lang_id
     *
     */
    public function __construct(\PDO $pdo, int $text_id, int $user_id, int $lang_id)
    {
        parent::__construct($pdo);
        $this->table = 'likes';
        $this->user_id = $user_id;
        $this->text_id = $text_id;
        $this->lang_id = $lang_id;
    } // end __construct()

    /**
     * Toggles like for a specific text
     *
     * @return void
     */
    public function toggle(): void
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `text_id`=? AND `user_id`=?";

        if ($this->sqlCount($sql, [$this->text_id, $this->user_id]) > 0) {
            $sql = "DELETE FROM `{$this->table}` WHERE `text_id`=? AND `user_id`=?";
            $this->sqlExecute($sql, [$this->text_id, $this->user_id]);
        } else {
            $sql = "INSERT INTO `{$this->table}` (`text_id`, `user_id`, `lang_id`) VALUES (?, ?, ?)";
            $this->sqlExecute($sql, [$this->text_id, $this->user_id, $this->lang_id]);
        }
    } // end toggle()

    /**
     * Returns total likes for current text
     *
     * @return integer
     */
    public function get(): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `text_id` = ?";
        return $this->sqlCount($sql, [$this->text_id]);
    } // end get()

    /**
     * Checks if user already gave like to current text
     *
     * @return bool
     */
    public function userLiked(): bool
    {
        $sql = "SELECT COUNT(*) AS `user_liked` FROM `{$this->table}` WHERE `text_id` = ? AND `user_id` = ?";
        return $this->sqlCount($sql, [$this->text_id, $this->user_id]) === 1;
    } // end userLiked()
}
