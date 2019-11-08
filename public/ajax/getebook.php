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

use Aprelendo\Includes\Classes\User;
use Aprelendo\Includes\Classes\Texts;
use Aprelendo\Includes\Classes\EbookFile;

// only premium users are allowed to visit this page
if (!$user->isPremium()) {
    exit;
}

$user_id = $user->getId();
$lang_id = $user->getLangId();
$id = $_GET['id'];

try {
    $text = new Texts($pdo, $user_id, $lang_id);
    $text->loadRecord($id);
    $file_name = $text->getSourceUri();

    if (!empty($file_name)) {
        $ebook_file = new EbookFile($file_name, $user->isPremium());
        $ebook_content = $ebook_file->get();
        if ($ebook_content != false) {
            return $ebook_content;
        } else {
            throw new \Exception(404);
        }
    } else {
        throw new \Exception(404);
    }
} catch (Exception $e) {
    http_response_code($e->getMessage());
}
