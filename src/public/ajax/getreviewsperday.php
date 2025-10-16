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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // loads User class & check if user is logged in

use Aprelendo\User;
use Aprelendo\WordStats;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_name = !empty($_GET['u']) ? $_GET['u'] : $user->name;

    // if the GET u is different from the current user name
    if ($user_name != $user->name) {
        $user = new User($pdo);
        $user->loadRecordByName($user_name);
    }

    $stats = new WordStats($pdo, $user->id, $user->lang_id);
    $result = $stats->getReviewsPerDay();
    echo json_encode($result);
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
