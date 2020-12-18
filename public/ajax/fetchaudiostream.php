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

require_once '../../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & checks if user is logged in

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\Includes\Classes\VoiceRSS;
use Aprelendo\Includes\Classes\LogAudioStreams;

try {
    if (!isset($_POST['text']) || empty($_POST['text'] || !isset($_POST['langiso']) || empty($_POST['langiso']))){
        throw new \Exception('Malformed request', 400);
    }

    $stream_log = new LogAudioStreams($pdo, $user->getId());
    $nr_of_streams_today = $stream_log->countTodayRecords();
    $premium_user = $user->isPremium();

    if ((!$premium_user && $nr_of_streams_today >= 1) || ($premium_user && $nr_of_streams_today >= 3)){
        throw new \Exception('Forbidden', 403);
    }

    $audiolang = array( 'en' => 'en-us', 
                        'es' => 'es-es', 
                        'pt' => 'pt-br', 
                        'fr' => 'fr-fr', 
                        'it' => 'it-it', 
                        'de' => 'de-de');
    
    $tts = new VoiceRSS;
    $voice = $tts->speech([
        'key' => VOICERSS_API_KEY,
        'hl' => $audiolang[$_POST['langiso']],
        'src' => $_POST['text'],
        'r' => '0',
        'c' => 'mp3',
        'f' => '44khz_16bit_stereo',
        'ssml' => 'false',
        'b64' => 'true'
    ]);
    
    echo json_encode($voice);

    // if no errors, log audio stream 
    if ($voice['error'] === NULL && $voice['response'] !== false) {
        $stream_log->addRecord();
    } else {
        throw new \Exception($voice['error'], 400);
    }
    
} catch (\Exception $e) {
    http_response_code($e->getCode());
}


?>