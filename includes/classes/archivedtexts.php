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

use Aprelendo\Includes\Classes\Connect;

class ArchivedTexts extends Texts
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
        $this->table = 'archived_texts';
    }

    /**
     * Unarchives text by using their $ids
     *
     * @param string $ids JSON with text ids to unarchive
     * @return boolean
     */
    public function unarchiveByIds($ids) {
        $textIDs = $this->JSONtoCSV($ids);

        $insertsql = "INSERT INTO `texts` 
                      SELECT *
                      FROM `archived_texts` 
                      WHERE `id` IN (?)";
        
        $deletesql = "DELETE FROM `archived_texts` WHERE `id` IN (?)";
        
        $stmt = $this->con->prepare($insertsql);
        $stmt->bind_param("s", $textIDs);
        $result = $stmt->execute();

        if ($result) {
            $stmt = $this->con->prepare($deletesql);
            $stmt->bind_param("s", $textIDs);
            $result = $stmt->execute();
        }
        
        $stmt->close();
        return $result;
    }
}


?>