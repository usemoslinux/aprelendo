#!/usr/bin/env php
<?php
// SPDX-License-Identifier: GPL-3.0-or-later

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

try {
    $pdo = new PDO(DB_DRIVER . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $column = $pdo->query("SHOW COLUMNS FROM preferences LIKE 'ebook_mode'")->fetch(PDO::FETCH_ASSOC);
    if (!$column) {
        $pdo->exec("ALTER TABLE preferences ADD COLUMN ebook_mode varchar(6) NOT NULL DEFAULT 'auto'");
    }
    $column = $pdo->query("SHOW COLUMNS FROM texts LIKE 'text_pos'")->fetch(PDO::FETCH_ASSOC);
    if ($column && strtolower($column['Type']) !== 'text') {
        $pdo->exec('ALTER TABLE texts MODIFY COLUMN text_pos TEXT DEFAULT NULL');
    }
    fwrite(STDOUT, "Ebook reader schema is up to date.\n");
} catch (Throwable $error) {
    fwrite(STDERR, "Ebook reader upgrade failed: {$error->getMessage()}\n");
    exit(1);
}
