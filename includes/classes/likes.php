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

class Likes extends DBEntity
{
    private $text_id = 0;
    private $lang_id = 0;
    
    /**
     * Constructor
     *
     * Sets 3 basic variables used to identify any text: $pdo, $user_id & lang_id
     *
     * @param \PDO $pdo
     * @param int $text_id
     * @param int $user_id
     * @param int $lang_id
     *
     */
    public function __construct(\PDO $pdo, int $text_id, int $user_id, int $lang_id)
    {
        parent::__construct($pdo, $user_id);
        $this->text_id = $text_id;
        $this->lang_id = $lang_id;
        $this->table = 'likes';
    } // end __construct()

    /**
     * Toggles like for a specific text
     *
     * @return void
     */
    public function toggle(): void
    {
        try {
            $sql = "SELECT `text_id` FROM `{$this->table}` WHERE `text_id`=? AND `user_id`=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->text_id, $this->user_id]);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            if (count($result) > 0) {
                $sql = "DELETE FROM `{$this->table}` WHERE `text_id`=? AND `user_id`=?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$this->text_id, $this->user_id]);
                
            } else {
                $sql = "INSERT INTO `{$this->table}` (`text_id`, `user_id`, `lang_id`) VALUES (?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$this->text_id, $this->user_id, $this->lang_id]);
            }

            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to toggle like for this text.');
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to toggle like for this text.');
        } finally {
            $stmt = null;
        }
    } // end toggle()
}
