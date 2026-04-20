<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

if (empty($_GET) || !isset($_GET['id'])) {
    http_response_code(400);
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
    
    if ($ebook_content === '') {
        throw new UserException('Book content is empty.', 404);
    }

    header('Content-Type: application/epub+zip');
    header('Content-Length: ' . strlen($ebook_content));
    echo $ebook_content;
    exit;
} catch (Throwable $e) {
    $status_code = (int)$e->getCode();

    if ($status_code < 400 || $status_code > 599) {
        $status_code = 500;
    }

    http_response_code($status_code);
    exit;
}
