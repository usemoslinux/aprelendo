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

require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & check if user is logged in

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

// get how many words were created in each of the last 7 days
for ($i=6; $i >= 0; $i--) { 
    $result = $con->query("SELECT COUNT(word) FROM words WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND wordCreated < CURDATE() - INTERVAL $i-1 DAY AND wordCreated > CURDATE() - INTERVAL $i DAY") 
    or die(mysqli_error($con));
    $row = $result->fetch_array();
    $array['created'][] = $row[0];
}

// get how many words' status were modified in each of the last 7 days
for ($i=6; $i >= 0; $i--) { 
    $result = $con->query("SELECT COUNT(word) FROM words WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND wordStatus>0 AND wordModified < CURDATE() - INTERVAL $i-1 DAY AND wordModified > CURDATE() - INTERVAL $i DAY") 
    or die(mysqli_error($con));
    $row = $result->fetch_array();
    $array['modified'][] = $row[0];
}

// get how many words were learned in each of the last 7 days
for ($i=6; $i >= 0; $i--) { 
    $result = $con->query("SELECT COUNT(word) FROM words WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND wordStatus=0 AND wordModified < CURDATE() - INTERVAL $i-1 DAY AND wordModified > CURDATE() - INTERVAL $i DAY") 
    or die(mysqli_error($con));
    $row = $result->fetch_array();
    $array['learned'][] = $row[0];
}

// get how many learned words were forgotten in each of the last 7 days
for ($i=6; $i >= 0; $i--) { 
    $result = $con->query("SELECT COUNT(word) FROM words WHERE wordUserId='$user_id' AND wordLgId='$learning_lang_id' AND wordStatus=2 AND wordModified>wordCreated AND wordModified < CURDATE() - INTERVAL $i-1 DAY AND wordModified > CURDATE() - INTERVAL $i DAY") 
    or die(mysqli_error($con));
    $row = $result->fetch_array();
    $array['forgotten'][] = $row[0];
}

echo json_encode($array);
