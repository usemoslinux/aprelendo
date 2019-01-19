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

require_once('../../includes/dbinit.php');  // connect to database
require_once(APP_ROOT . 'includes/checklogin.php'); // check if user is logged in and set $user object

use Aprelendo\Includes\Classes\Videos;

try {
    if (isset($_POST['video_id']) && !empty($_POST['video_id'])) {
        $video_id = $_POST['video_id'];
        $video = new Videos($con, $user->id, $user->learning_lang_id);
        echo $video->fetchVideo($user->learning_lang, $video_id);
    } else {
        throw new Exception ('There was a problem retrieving that URL. Please check it is not empty or malformed');
    }
    
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>