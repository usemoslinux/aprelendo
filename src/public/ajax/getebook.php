<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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