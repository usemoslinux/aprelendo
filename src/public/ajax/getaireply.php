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
use Aprelendo\UserException;

header('Content-Type: text/plain');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no'); // Disable buffering in Nginx
header('Connection: keep-alive');

try {
    if (!isset($_POST['prompt']) || empty($_POST['prompt'])) {
        throw new UserException('Error: Empty or malformed prompt.');
    }

    $ai_bot = new AIBot($user->hf_token, $user->lang);

    // Stream the AI response
    $ai_bot->streamReply($_POST['prompt']);
} catch (UserException $e) {
    echo "Error: " . $e->getMessage();
}
