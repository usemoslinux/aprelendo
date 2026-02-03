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
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_GET)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\User;
use Aprelendo\Language;
use Aprelendo\WordStats;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $user_name = !empty($_GET['u']) ? $_GET['u'] : $user->name;
    $lang_name = $user->lang;

    // if the GET u is different from the current user name
    if ($user_name != $user->name) {
        $user = new User($pdo);
        $user->loadRecordByName($user_name);

        $lang = new Language($pdo, $user->id);
        $lang->loadRecordByName($lang_name);

        $user->lang = $lang->name;
        $user->lang_id = $lang->id;
    }

    if ($_GET['type'] === "words") {
        $stats = new WordStats($pdo, $user->id, $user->lang_id);
        $payload = $stats->getTotals();
    }
    
    $response = ['success' => true, 'payload' => $payload];
    echo json_encode($response);
    exit;
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
