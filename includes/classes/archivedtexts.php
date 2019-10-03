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

        // create archived_texts table if it doesn't exist
        $sql = "CREATE TABLE `archived_texts` (
            `id` int(11) unsigned NOT NULL,
            `user_id` int(10) unsigned NOT NULL,
            `lang_id` int(11) NOT NULL,
            `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
            `author` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
            `text` text COLLATE utf8_unicode_ci NOT NULL,
            `audio_uri` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
            `source_uri` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
            `type` tinyint(3) unsigned NOT NULL,
            `word_count` mediumint(8) unsigned DEFAULT NULL,
            `level` tinyint(3) unsigned DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `LgId` (`lang_id`),
            KEY `delATextUserId` (`user_id`),
            CONSTRAINT `delATextUserId` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

        $this->con->query($sql);
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
                      WHERE `id` IN ($textIDs)";
                      
        $deletesql = "DELETE FROM `archived_texts` WHERE `id` IN ($textIDs)";
        
        if ($result = $this->con->query($insertsql)) {
            $result = $this->con->query($deletesql);
        }
        
        return $result;
    }
}


?>