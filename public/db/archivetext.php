<?php
/**
 * Copyright (C) 2018 Pablo Castagnino
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

require_once('dbinit.php'); // connect to database
require_once(PUBLIC_PATH . '/classes/texts.php'); // loads Texts class
require_once(PUBLIC_PATH . '/classes/archivedtexts.php'); // loads ArchivedTexts class
require_once(PUBLIC_PATH . '/classes/words.php'); // loads Words class
require_once(PUBLIC_PATH . '/db/checklogin.php'); // loads User class & checks if user is logged in

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;

try {
    // if text is archived using green button at the end, update learning status of words first
    if (isset($_POST['words'])) {
        $words_table = new Words($con, $user_id, $learning_lang_id);
        $words_table->updateByName($_POST['words']);    
    }  

    // if text is not shared, then archive or unarchive text accordingly
    if (isset($_POST['textIDs']) && !empty($_POST['textIDs'])) {
        if ($_POST['archivetext'] === 'true') { //archive text
            $texts_table = new Texts($con, $user_id, $learning_lang_id);
            $result = $texts_table->archiveByIds($_POST['textIDs']);
        } else { // unarchive text
            $texts_table = new ArchivedTexts($con, $user_id, $learning_lang_id);
            $result = $texts_table->unarchiveByIds($_POST['textIDs']);
        }

        if (!$result) {
            throw new Exception ('There was an unexpected error trying to (un)archive this text.');
        }
    }
} catch (Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}

?>