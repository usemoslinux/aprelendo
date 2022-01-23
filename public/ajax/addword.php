<?php
/**
 * Copyright (C) 2019 Pablo Castagnino
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

use Aprelendo\Includes\Classes\Words;

if (isset($_POST['word'])) {    
    $user_id = $user->getId();
    $lang_id = $user->getLangId();
    
    try {
        $word = $_POST['word'];
        $is_phrase =  (isset($_POST['is_phrase']) && !empty($_POST['is_phrase'])) ? $_POST['is_phrase'] : false;
        
        $words_table = new Words($pdo, $user_id, $lang_id);
 
        // if word already exists in table, status = 3 ("forgotten")
        // otherwise, $status = 2 ("new")
        $status = $words_table->exists($word) ? 3 : 2;
        
        $words_table->add($word, $status, $is_phrase);
    } catch (Exception $e) {
        $error = array('error_msg' => $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode($error);
    }
}
?>