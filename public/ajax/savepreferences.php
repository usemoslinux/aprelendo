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

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

// save preferences to database
$fontfamily = isset($_POST['fontfamily']) ? $_POST['fontfamily'] : "Helvetica";
$fontsize = isset($_POST['fontsize']) ? $_POST['fontsize'] : '12pt';
$lineheight = isset($_POST['lineheight']) ? $_POST['lineheight'] : '1.5';
$alignment = isset($_POST['alignment']) ? $_POST['alignment'] : 'left';
$mode = isset($_POST['mode']) ? $_POST['mode'] : 'light';
$assistedlearning = isset($_POST['assistedlearning']) ? $_POST['assistedlearning'] : true;

try {
    $result = $con->query("REPLACE INTO preferences (prefUserId, prefFontFamily,
    prefFontSize, prefLineHeight, prefAlignment, prefMode, prefAssistedLearning)
    VALUES ('$user_id', '$fontfamily', '$fontsize', '$lineheight', '$alignment', '$mode', '$assistedlearning')");
    
    if (!$result) {
        throw new Exception ('There was an unexpected error trying to save your preferences. Please, try again later.');
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>