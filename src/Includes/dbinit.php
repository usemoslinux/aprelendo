<?php
// SPDX-License-Identifier: GPL-3.0-or-later

// perform all database initialization here, in a single file

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(APP_ROOT) . '/vendor/autoload.php';

use Aprelendo\Connect;

try {
    $db_connection = new Connect(DB_DRIVER, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_CHARSET);
    $pdo = $db_connection->connect();
} catch (\Exception $e) {
    http_response_code(500);
    exit;
}
