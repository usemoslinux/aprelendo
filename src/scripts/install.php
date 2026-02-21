#!/usr/bin/env php
<?php
/**
 * Copyright (C) 2026 Pablo Castagnino
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

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script can only be executed from CLI.\n");
    exit(1);
}

$project_root = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
$config_file_path = $project_root . 'src/config/config.php';
$autoload_file_path = $project_root . 'vendor/autoload.php';

if (!is_file($config_file_path)) {
    fwrite(STDERR, "Missing config file: {$config_file_path}\n");
    fwrite(STDERR, "Create it first: cp src/config/config-example.php src/config/config.php\n");
    exit(1);
}

if (!is_file($autoload_file_path)) {
    fwrite(STDERR, "Missing autoload file: {$autoload_file_path}\n");
    fwrite(STDERR, "Install dependencies first: composer install\n");
    exit(1);
}

require_once $config_file_path;
require_once $autoload_file_path;

$force_install = in_array('--force', $argv, true);
$db_name_escaped = str_replace('`', '``', DB_NAME);
$schema_file_path = APP_ROOT . 'config/aprelendo-schema.sql';
$pdo_options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION];

if (defined('PDO::MYSQL_ATTR_MULTI_STATEMENTS')) {
    $pdo_options[\PDO::MYSQL_ATTR_MULTI_STATEMENTS] = true;
}

echo "[1/5] Connecting to database server ({$db_name_escaped})...\n";

try {
    $server_dsn = DB_DRIVER . ':host=' . DB_HOST . ';charset=' . DB_CHARSET;
    $server_pdo = new \PDO($server_dsn, DB_USER, DB_PASSWORD, $pdo_options);
} catch (\PDOException $e) {
    fwrite(STDERR, "Failed to connect to DB server: {$e->getMessage()}\n");
    exit(1);
}

echo "[2/5] Ensuring database exists...\n";

try {
    $create_db_sql = "CREATE DATABASE IF NOT EXISTS `{$db_name_escaped}` "
        . "DEFAULT CHARACTER SET " . DB_CHARSET;
    $server_pdo->exec($create_db_sql);
} catch (\PDOException $e) {
    fwrite(STDERR, "Failed to create database `{$db_name_escaped}`: {$e->getMessage()}\n");
    exit(1);
}

echo "[3/5] Connecting to application database...\n";

try {
    $app_dsn = DB_DRIVER
        . ':host=' . DB_HOST
        . ';dbname=' . DB_NAME
        . ';charset=' . DB_CHARSET;
    $app_pdo = new \PDO($app_dsn, DB_USER, DB_PASSWORD, $pdo_options);
} catch (\PDOException $e) {
    fwrite(STDERR, "Failed to connect to database `" . DB_NAME . "`: {$e->getMessage()}\n");
    exit(1);
}

$table_count_sql = 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :table_schema';
$table_count_statement = $app_pdo->prepare($table_count_sql);
$table_count_statement->execute(['table_schema' => DB_NAME]);
$table_count = (int) $table_count_statement->fetchColumn();

if ($table_count > 0 && !$force_install) {
    echo "[4/5] Existing schema detected ({$table_count} tables). Skipping import.\n";
    echo "[5/5] Installation complete. Use --force to rebuild the schema.\n";
    exit(0);
}

if ($force_install) {
    echo "[4/5] --force detected. Rebuilding database...\n";

    try {
        $server_pdo->exec("DROP DATABASE IF EXISTS `{$db_name_escaped}`");
        $server_pdo->exec(
            "CREATE DATABASE `{$db_name_escaped}` DEFAULT CHARACTER SET " . DB_CHARSET
        );
    } catch (\PDOException $e) {
        fwrite(STDERR, "Failed to rebuild database: {$e->getMessage()}\n");
        exit(1);
    }

    try {
        $app_dsn = DB_DRIVER
            . ':host=' . DB_HOST
            . ';dbname=' . DB_NAME
            . ';charset=' . DB_CHARSET;
        $app_pdo = new \PDO($app_dsn, DB_USER, DB_PASSWORD, $pdo_options);
    } catch (\PDOException $e) {
        fwrite(STDERR, "Failed to reconnect after rebuild: {$e->getMessage()}\n");
        exit(1);
    }
} else {
    echo "[4/5] Importing schema from {$schema_file_path}...\n";
}

if (!is_readable($schema_file_path)) {
    fwrite(STDERR, "Schema file is missing or unreadable: {$schema_file_path}\n");
    exit(1);
}

$schema_sql = file_get_contents($schema_file_path);

if ($schema_sql === false) {
    fwrite(STDERR, "Failed to read schema file: {$schema_file_path}\n");
    exit(1);
}

// Remove DB-specific directives so install uses DB_NAME from config.
$schema_sql = preg_replace('/^\s*CREATE DATABASE\s+IF\s+NOT\s+EXISTS\s+`[^`]+`.*;\s*$/mi', '', $schema_sql);
$schema_sql = preg_replace('/^\s*USE\s+`[^`]+`\s*;\s*$/mi', '', $schema_sql);

try {
    $app_pdo->exec($schema_sql);
} catch (\PDOException $e) {
    fwrite(STDERR, "Schema import failed: {$e->getMessage()}\n");
    exit(1);
}

$table_count_statement->execute(['table_schema' => DB_NAME]);
$table_count = (int) $table_count_statement->fetchColumn();

echo "[5/5] Installation complete. Imported schema with {$table_count} tables.\n";
