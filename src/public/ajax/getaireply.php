<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/bootstrap.php'; // initialize application

use Aprelendo\AuthGuard;
use Aprelendo\AIBot;
use Aprelendo\UserException;

$user = AuthGuard::requireAjaxUser();

header('Content-Type: application/x-ndjson; charset=utf-8');
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
    echo json_encode(['type' => 'error', 'message' => $e->getMessage()], JSON_INVALID_UTF8_SUBSTITUTE) . "\n";
    exit;
} catch (Throwable $e) {
    echo json_encode(['type' => 'error', 'message' => 'Unable to get AI response. Please try again.']) . "\n";
    exit;
}
