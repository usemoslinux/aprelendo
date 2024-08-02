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

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\User;
use Aprelendo\UserAuth;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $user = new User($pdo);
        $user_auth = new UserAuth($user);
        $user_auth->login($_POST['username'], $_POST['password']);
    } else {
        throw new UserException('Either username, email or password were not provided. Please try again.');
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
