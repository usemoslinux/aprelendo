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

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\VoiceRSS;
use Aprelendo\LogAudioStreams;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (!isset($_POST['text']) || empty($_POST['text'] || !isset($_POST['langiso']) || empty($_POST['langiso']))) {
        throw new UserException('Malformed request', 400);
    }

    $stream_log = new LogAudioStreams($pdo, $user->id);
    $nr_of_streams_today = $stream_log->countTodayRecords();

    if ($nr_of_streams_today >= 3) {
        throw new UserException('Forbidden', 403);
    }

    $audiolang = [
        'ar' => 'ar-sa',
        'zh' => 'zh-cn',
        'nl' => 'nl-nl',
        'en' => 'en-us',
        'fr' => 'fr-fr',
        'de' => 'de-de',
        'el' => 'el-gr',
        'he' => 'he-il',
        'hi' => 'hi-in',
        'it' => 'it-it',
        'ja' => 'ja-jp',
        'ko' => 'ko-kr',
        'pt' => 'pt-br',
        'ru' => 'ru-ru',
        'es' => 'es-es'
    ];

    $tts = new VoiceRSS;
    $voice = $tts->speech([
        'key' => VOICERSS_API_KEY,
        'hl' => $audiolang[$_POST['langiso']],
        'src' => $_POST['text'],
        'r' => '0',
        'c' => 'mp3',
        'f' => '44khz_16bit_mono',
        'ssml' => 'false',
        'b64' => 'true'
    ]);

    echo json_encode($voice);

    // if no errors, log audio stream
    if ($voice['error'] === null && $voice['response']) {
        $stream_log->addRecord();
    } else {
        throw new UserException($voice['error'], 400);
    }
} catch (\Exception $e) {
    http_response_code($e->getCode());
}
