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

require_once '../../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & checks if user is logged in

use Aprelendo\Includes\Classes\User;
use Aprelendo\Includes\Classes\EbookFile;

// only premium users are allowed to visit this page
if (!$user->isPremium()) {
    exit;
}

$user_id = $user->id;
$learning_lang_id = $user->learning_lang_id;
$id = $con->real_escape_string($_GET['id']);

try {
    $result = $con->query("SELECT textSourceURI FROM texts WHERE textId='$id' AND textUserId='$user_id' AND textLgId = '$learning_lang_id'") or die(mysqli_error($con));
    
    if ($result) {
        $row = $result->fetch_assoc();
        $ebook_file = new EbookFile($user->isPremium());
        $ebook_content = $ebook_file->get($row['textSourceURI']);
        if ($ebook_content != false) {
            return $ebook_content;
        } else {
            throw new Exception (404);
        }
    } else {
        throw new Exception (404);
    }
    
    
} catch (Exception $e) {
    http_response_code($e->getMessage());
}
