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
use Aprelendo\Includes\Classes\Conversion;

class ArchivedTexts extends Texts
{
    /**
     * Constructor
     *
     * @param \PDO $con
     * @param integer $user_id
     * @param integer $lang_id
     */
    public function __construct(\PDO $con, int $user_id, int $lang_id) {
        parent::__construct($con, $user_id, $lang_id);
        $this->table = 'archived_texts';
    } // end __construct()

    /**
     * Unarchives text by using their $ids
     *
     * @param string $ids JSON with text ids to unarchive
     * @return boolean
     */
    public function unarchive(string $ids): bool {
        try {
            $ids_array = json_decode($ids);
            $id_params = str_repeat("?,", count($ids_array)-1) . "?";

            $insert_sql = "INSERT INTO `texts` 
                           SELECT *
                           FROM `{$this->table}` 
                           WHERE `id` IN ($id_params)";
            
            $stmt = $this->con->prepare($insert_sql);
            $stmt->execute($ids_array);
            
            $delete_sql = "DELETE FROM `{$this->table}` WHERE `id` IN ($id_params)";
            $stmt = $this->con->prepare($delete_sql);
            $stmt->execute($ids_array);
            
            return true;
        } catch (\Exception $e) {
            return false;
        } finally {
            $stmt = null;
        }
    }
} // end unarchive()


?>