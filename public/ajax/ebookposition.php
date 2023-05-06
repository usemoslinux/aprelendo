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

use Aprelendo\Includes\Classes\Texts;

try {
    if (isset($_POST['mode']) && !empty($_POST['mode'])) {
        $text = new Texts($pdo, $user->getId(), $user->getLangId());

        if ($_POST['mode'] == "GET") {
            $text->loadRecord($_POST['id']);
            
            $result['audio_pos'] = $text->getAudioPos();
            $result['text_pos'] = $text->getTextPos();

            echo json_encode($result);
        } elseif ($_POST['mode'] == "SAVE") {
            $result['audio_pos'] = isset($_POST['audio_pos']) && !empty($_POST['audio_pos']) ? $_POST['audio_pos'] : null;
            $result['text_pos'] = isset($_POST['text_pos']) && !empty($_POST['text_pos']) ? $_POST['text_pos'] : null;

            $text->update($_POST['id'], $result);
        }
    }
} catch (\Exception $e) {
    $result['error_msg'] = $e->getMessage();
    echo json_encode($result);
}
