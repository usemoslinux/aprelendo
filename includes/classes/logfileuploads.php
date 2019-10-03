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

class LogFileUploads extends Log
{
    public function __construct($con, $user_id) {
        parent::__construct($con, $user_id);
        $this->table = 'log_file_uploads';

        // create popular_sources table if it doesn't exist
        $sql = "CREATE TABLE `log_file_uploads` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` int(10) unsigned NOT NULL,
            `date_created` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `log_uploads` (`user_id`) USING BTREE,
            CONSTRAINT `log_uploads` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
           ) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8";

        $this->con->query($sql);
    }
}