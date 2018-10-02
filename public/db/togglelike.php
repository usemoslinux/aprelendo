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
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in
require_once(PUBLIC_PATH . '/classes/likes.php'); // loads Likes class

try {
    if ($_POST['id']) {
        $like = new Likes($con, $_POST['id'], $user->id, $user->learning_lang_id);
        $result = $like->toggle();
        if (!$result) {
            throw new Exception('Oops! There was a problem trying to give a like to that text.');
        }
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>
