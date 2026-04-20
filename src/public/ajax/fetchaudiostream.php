<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\VoiceRSS;
use Aprelendo\LogAudioStreams;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $text = trim((string) ($_POST['text'] ?? ''));
    $lang_iso = trim((string) ($_POST['langiso'] ?? ''));

    if ($text === '' || $lang_iso === '') {
        throw new UserException('Malformed request', 400);
    }

    $stream_log = new LogAudioStreams($pdo, $user->id);
    $nr_of_streams_today = $stream_log->countTodayRecords();

    if ($nr_of_streams_today >= 3) {
        throw new UserException('Forbidden', 403);
    }

    $audiolang = [
        'ar' => 'ar-sa', // Arabic
        'bg' => 'bg-bg', // Bulgarian
        'ca' => 'ca-es', // Catalan
        'zh' => 'zh-cn', // Chinese
        'hr' => 'hr-hr', // Croatian
        'cs' => 'cs-cz', // Czech
        'da' => 'da-dk', // Danish
        'nl' => 'nl-nl', // Dutch
        'en' => 'en-us', // English
        'fr' => 'fr-fr', // French
        'de' => 'de-de', // German
        'el' => 'el-gr', // Greek
        'he' => 'he-il', // Hebrew
        'hi' => 'hi-in', // Hindi
        'hu' => 'hu-hu', // Hungarian
        'it' => 'it-it', // Italian
        'ja' => 'ja-jp', // Japanese
        'ko' => 'ko-kr', // Korean
        'no' => 'nb-no', // Norwegian
        'pl' => 'pl-pl', // Polish
        'pt' => 'pt-br', // Portuguese
        'ro' => 'ro-ro', // Romanian
        'ru' => 'ru-ru', // Russian
        'sk' => 'sk-sk', // Slovak
        'sl' => 'sl-si', // Slovenian
        'es' => 'es-es', // Spanish
        'sv' => 'sv-se', // Swedish
        'tr' => 'tr-tr', // Turkish
        'vi' => 'vi-vn'  // Vietnamese
    ];

    if (!isset($audiolang[$lang_iso])) {
        throw new UserException('Malformed request', 400);
    }

    $tts = new VoiceRSS;
    $payload = $tts->speech([
        'key' => VOICERSS_API_KEY,
        'hl' => $audiolang[$lang_iso],
        'src' => $text,
        'r' => '0',
        'c' => 'mp3',
        'f' => '44khz_16bit_mono',
        'ssml' => 'false',
        'b64' => 'true'
    ]);

    if ($payload['error'] !== null || empty($payload['response'])) {
        throw new UserException($payload['error'] ?? 'No audio response received', 400);
    }
        
    $stream_log->addRecord(); // if no errors, log audio stream
    $response = ['success' => true, 'payload' => $payload];
    echo json_encode($response);
    exit;
} catch (Throwable $e) {
    $status_code = (int) $e->getCode();
    http_response_code(($status_code >= 400 && $status_code <= 599) ? $status_code : 500);
    exit;
}
