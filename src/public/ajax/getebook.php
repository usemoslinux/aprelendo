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
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_GET) || !isset($_GET['id'])) {
    echo json_encode($response);
    exit;
}

use Aprelendo\Texts;
use Aprelendo\EbookFile;
use Aprelendo\UserException;

try {
    $user_id = $user->id;
    $lang_id = $user->lang_id;
    $id = (int)$_GET['id'];
    
    $text = new Texts($pdo, $user_id, $lang_id);
    $text->loadRecord($id);
    $file_name = $text->source_uri;

    if (empty($file_name)) {
        throw new UserException('Empty file name.', 404);
    }
    
    $ebook_file = new EbookFile($file_name);
    $ebook_content = $ebook_file->get();
    
    if (!$ebook_content) {
        throw new UserException('Book content is empty.', 404);
    }

    $response = ['success' => true];
    echo json_encode($response);
    exit;
} catch (Throwable $e) {
    // catches UserException but also possible Exceptions from fileread() in $ebook_file->get()
    http_response_code($e->getCode());
    exit;
}