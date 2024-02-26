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

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\Texts;
use Aprelendo\ArchivedTexts;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (isset($_POST['textIDs']) && isset($_POST['is_archived'])) {
        $text_ids = json_decode($_POST['textIDs']);
        $is_archived = $_POST['is_archived'];
        $user_id = $user->id;
        $lang_id = $user->lang_id;
    
        // decide wether we are deleting an archived text or not
        if ($is_archived) {
            $texts_table = new ArchivedTexts($pdo, $user_id, $lang_id);
        } else {
            $texts_table = new Texts($pdo, $user_id, $lang_id);
        }
    
        $texts_table->delete($text_ids);
    } else {
        throw new UserException('There was an error in the parameters provided to remove this text');
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
