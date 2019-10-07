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
    private $text_id;
    private $learning_lang_id;
    
    /**
     * Constructor
     * 
     * Sets 3 basic variables used to identify any text: $con, $user_id & learning_lang_id
     *
     * @param mysqli_connect $con
     * @param integer $user_id
     * @param integer $learning_lang_id
     */
    public function __construct($con, $text_id, $user_id, $learning_lang_id) {
        parent::__construct($con, $user_id);
        $this->text_id = $text_id;
        $this->learning_lang_id = $learning_lang_id;
        $this->table = 'likes';
    }

    /**
     * Toggles like for a specific text
     *
     * @return mysqli|boolean
     */
    public function toggle()
    {
        $sql = "SELECT `text_id` FROM `likes` WHERE `text_id`=? AND `user_id`=?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("ss", $this->text_id, $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $sql = "DELETE FROM `likes` WHERE `text_id`=? AND `user_id`=?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("ss", $this->text_id, $this->user_id);
            $result = $stmt->execute();
        } else {
            $sql = "INSERT INTO `likes` (`text_id`, `user_id`, `lang_id`) VALUES (?, ?, ?)";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("sss", $this->text_id, $this->user_id, $this->learning_lang_id);
            $result = $stmt->execute();
        }

        $stmt->close();
        return $result;
    }
}



?>