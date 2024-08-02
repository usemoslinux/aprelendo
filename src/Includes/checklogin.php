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

use Aprelendo\User;
use Aprelendo\UserAuth;

if (!isset($user)) {
    require_once 'dbinit.php'; // connect to database

    $user = new User($pdo);
    $user_auth = new UserAuth($user);
    $user_is_logged = $user_auth->isLoggedIn();
    $pdo->exec("SET time_zone='{$user->time_zone}';"); // use user local time zone

    if (!$user_is_logged && !isset($no_redirect)) {
        header('Location:/login.php');
        exit;
    }
}
