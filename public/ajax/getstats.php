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

require_once '../../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & check if user is logged in

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

// get how many words were created in each of the last 7 days
for ($i=6; $i >= 0; $i--) { 
    $result = $con->query("SELECT COUNT(word) FROM `words` WHERE `user_id`='$user_id' AND `lang_id`='$learning_lang_id' AND `date_created` < CURDATE() - INTERVAL $i-1 DAY AND `date_created` > CURDATE() - INTERVAL $i DAY") 
    or die(mysqli_error($con));
    $row = $result->fetch_array();
    $array['created'][] = $row[0];
}

// get how many words' status were modified in each of the last 7 days
for ($i=6; $i >= 0; $i--) { 
    $result = $con->query("SELECT COUNT(word) FROM `words` WHERE `user_id`='$user_id' AND `lang_id`='$learning_lang_id' AND `status`>0 AND `date_modified` < CURDATE() - INTERVAL $i-1 DAY AND `date_modified` > CURDATE() - INTERVAL $i DAY") 
    or die(mysqli_error($con));
    $row = $result->fetch_array();
    $array['modified'][] = $row[0];
}

// get how many words were learned in each of the last 7 days
for ($i=6; $i >= 0; $i--) { 
    $result = $con->query("SELECT COUNT(word) FROM `words` WHERE `user_id`='$user_id' AND `lang_id`='$learning_lang_id' AND `status`=0 AND `date_modified` < CURDATE() - INTERVAL $i-1 DAY AND `date_modified` > CURDATE() - INTERVAL $i DAY") 
    or die(mysqli_error($con));
    $row = $result->fetch_array();
    $array['learned'][] = $row[0];
}

// get how many learned words were forgotten in each of the last 7 days
for ($i=6; $i >= 0; $i--) { 
    $result = $con->query("SELECT COUNT(word) FROM `words` WHERE `user_id`='$user_id' AND `lang_id`='$learning_lang_id' AND `status`=2 AND `date_modified`>`date_created` AND `date_modified` < CURDATE() - INTERVAL $i-1 DAY AND `date_modified` > CURDATE() - INTERVAL $i DAY") 
    or die(mysqli_error($con));
    $row = $result->fetch_array();
    $array['forgotten'][] = $row[0];
}

echo json_encode($array);
