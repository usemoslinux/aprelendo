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
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & checks if user is logged in

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

$sql = "SELECT * FROM `languages` WHERE `user_id`=? AND `id` = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ss", $user_id, $learning_lang_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

echo json_encode($row);