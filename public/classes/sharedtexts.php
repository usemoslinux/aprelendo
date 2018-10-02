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

require_once('connect.php');

class SharedTexts extends Texts
{
    /**
     * Constructor
     *
     * @param mysqli_connect $con
     * @param integer $user_id
     * @param integer $learning_lang_id
     */
    public function __construct($con, $user_id, $learning_lang_id) {
        parent::__construct($con, $user_id, $learning_lang_id);
        $this->table = 'sharedtexts';
        $this->cols = array(
            'id' => 'stextId',
            'userid' => 'stextUserId', 
            'lgid' => 'stextLgId', 
            'title' => 'stextTitle', 
            'author' => 'stextAuthor', 
            'text' => 'stext', 
            'sourceURI' => 'stextSourceURI', 
            'audioURI' => 'stextAudioURI', 
            'type' => 'stextType', 
            'nrofwords' => 'stextNrOfWords',
            'level' => 'stextLevel');
    }
}


?>