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
require_once APP_ROOT . 'Includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

use Aprelendo\Preferences;
use Aprelendo\InternalException;
use Aprelendo\UserException;

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

// save preferences to database
try {
    $pref = new Preferences($pdo, $user->id);
    $pref->edit(
        $_POST['fontfamily'],
        $_POST['fontsize'],
        $_POST['lineheight'],
        $_POST['alignment'],
        $_POST['mode'],
        $_POST['assistedlearning']
    );
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
