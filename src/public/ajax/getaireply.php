<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

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

    $ai_bot = new AIBot($user->hf_token, $user->lang, $user->native_lang);

    // Stream the AI response
    $ai_bot->streamReply($_POST['prompt']);
} catch (UserException $e) {
    echo "Error: " . $e->getMessage();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
