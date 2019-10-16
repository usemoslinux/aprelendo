<?php 
/**
 * Copyright (C) 2018 Pablo Castagnino
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
    private $id = 0;
    private $text_id = 0;
    private $lang_id = 0;
    
    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify any text: $con, $user_id & lang_id
     *
     * @param mysqli_connect $con
     * @param integer $text_id
     * @param integer $user_id
     * @param integer $lang_id
     * 
     */
    public function __construct($con, $text_id, $user_id, $lang_id) {
        parent::__construct($con, $user_id);
        $this->text_id = $text_id;
        $this->lang_id = $lang_id;
        $this->table = 'likes';
    } // end __construct()

    /**
     * Toggles like for a specific text
     *
     * @return mysqli|boolean
     */
    public function toggle(): bool {
        try {
            $sql = "SELECT `text_id` FROM `{$this->table}` WHERE `text_id`=? AND `user_id`=?";
            $stmt = $this->con->prepare($sql);
            $stmt->execute([$this->text_id, $this->user_id]);
            $result = $stmt->fetchAll();
            
            if (count($result) > 0) {
                $sql = "DELETE FROM `{$this->table}` WHERE `text_id`=? AND `user_id`=?";
                $stmt = $this->con->prepare($sql);
                $result = $stmt->execute([$this->text_id, $this->user_id]);
            } else {
                $sql = "INSERT INTO `{$this->table}` (`text_id`, `user_id`, `lang_id`) VALUES (?, ?, ?)";
                $stmt = $this->con->prepare($sql);
                $result = $stmt->execute([$this->text_id, $this->user_id, $this->lang_id]);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    } // end toggle()
}



?>