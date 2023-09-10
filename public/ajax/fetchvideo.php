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

require_once '../../includes/dbinit.php';  // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // check if user is logged in and set $user object

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\Includes\Classes\Videos;
use Aprelendo\Includes\Classes\InternalException;
use Aprelendo\Includes\Classes\UserException;

try {
    if (!empty($_POST['video_id'])) {
        $video_id = $_POST['video_id'];
        $video = new Videos($pdo, $user->id, $user->lang_id);
        echo $video->fetchVideo($user->lang, $video_id);
    } else {
        throw new UserException('Error retrieving that URL. Please check it is not empty or malformed');
    }
    
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
