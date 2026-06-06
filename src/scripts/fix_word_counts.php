#!/usr/bin/env php
<?php
// SPDX-License-Identifier: GPL-3.0-or-later

if (PHP_SAPI !== "cli") {
    fwrite(STDERR, "This script can only be executed from CLI.\n");
    exit(1);
}

$_SERVER["HTTP_HOST"] ??= "localhost";

require_once dirname(__DIR__) . "/Includes/bootstrap.php";

use Aprelendo\Database;
use Aprelendo\TextsUtilities;

const TEXT_TABLES = ["texts", "shared_texts"];

$apply_changes = in_array("--apply", $argv, true);
$pdo = Database::connection();

/**
 * Ensures dynamic table names stay limited to known text tables.
 *
 * @param string $table
 * @return void
 */
function ensureTextTable(string $table): void
{
    if (!in_array($table, TEXT_TABLES, true)) {
        throw new InvalidArgumentException("Unsupported text table: {$table}");
    }
}

/**
 * Fetches stored text rows from a text table.
 *
 * @param PDO $pdo
 * @param string $table
 * @return array
 */
function fetchTextRows(PDO $pdo, string $table): array
{
    ensureTextTable($table);

    $stmt = $pdo->prepare("SELECT id, text, word_count FROM {$table} ORDER BY id");
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Updates one stored text word count.
 *
 * @param PDO $pdo
 * @param string $table
 * @param int $id
 * @param int $word_count
 * @return void
 */
function updateWordCount(PDO $pdo, string $table, int $id, int $word_count): void
{
    static $statements = [];

    ensureTextTable($table);

    if (!isset($statements[$table])) {
        $statements[$table] = $pdo->prepare("UPDATE {$table} SET word_count = ? WHERE id = ?");
    }

    $statements[$table]->execute([$word_count, $id]);
}

try {
    $total_checked_count = 0;
    $total_changed_count = 0;

    if ($apply_changes) {
        $pdo->beginTransaction();
    }

    foreach (TEXT_TABLES as $table) {
        $rows = fetchTextRows($pdo, $table);
        $checked_count = 0;
        $changed_count = 0;

        foreach ($rows as $row) {
            $checked_count++;
            $total_checked_count++;

            $current_word_count = (int)$row["word_count"];
            $new_word_count = TextsUtilities::countWordsInText($row["text"] ?? "");

            if ($new_word_count === $current_word_count) {
                continue;
            }

            $changed_count++;
            $total_changed_count++;
            echo "{$table}.id={$row["id"]}: {$current_word_count} -> {$new_word_count}\n";

            if ($apply_changes) {
                updateWordCount($pdo, $table, (int)$row["id"], $new_word_count);
            }
        }

        echo "{$table}: checked {$checked_count}, changed {$changed_count}.\n";
    }

    if ($apply_changes) {
        $pdo->commit();
    }

    $mode = $apply_changes ? "Applied" : "Dry run";
    echo "{$mode}. Checked: {$total_checked_count}. Changed: {$total_changed_count}.\n";

    if (!$apply_changes) {
        echo "Run with --apply to update the database.\n";
    }
} catch (Throwable $throwable) {
    if ($apply_changes && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, $throwable->getMessage() . "\n");
    exit(1);
}
