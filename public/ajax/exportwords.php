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

use Aprelendo\Includes\Classes\Words;

$user_id = $user->getId();
$lang_id = $user->getLangId();

try {
    if ($user->isPremium()) {
        // set search criteria, if any
        $search_text = isset($_GET['s']) ? $_GET['s'] : '';
        $order_by = isset($_GET['o']) ? $_GET['o'] : -1;

        // export to csv
        $words_table = new Words($con, $user_id, $lang_id);
        $result = $words_table->createCSVFile($search_text, $order_by);

        if (!$result) {
            throw new \Exception ('There was an unexpected error trying to export your word list');
        }
    } else {
        throw new \Exception ('Only premium users are allowed to export word lists');
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>
