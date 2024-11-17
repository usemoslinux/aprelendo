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

use Aprelendo\AIBot;
use Aprelendo\Language;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (!$_POST['prompt']) {
        throw new UserException('Error fetching AI reply. Prompt might be empty or malformed.');
    }

    $ai_bot = new AIBot($user->hf_token, $user->lang);
    $result = $ai_bot->fetchReply($_POST['prompt']);

    // echo json_encode($result);
    echo $result;
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
