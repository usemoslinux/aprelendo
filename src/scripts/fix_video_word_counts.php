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

const TYPE_VIDEO = 5;

$apply_changes = in_array("--apply", $argv, true);
$pdo = Database::connection();

/**
 * Counts words using the same rule used when texts are saved.
 *
 * @param string $text
 * @return int
 */
function countWords(string $text): int
{
    return preg_match_all('/\w+/u', $text);
}

/**
 * Fetches all uploaded video texts.
 *
 * @param PDO $pdo
 * @return array
 */
function fetchVideoTexts(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT id, text, word_count FROM shared_texts WHERE type = ? ORDER BY id");
    $stmt->execute([TYPE_VIDEO]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Updates one shared video text word count.
 *
 * @param PDO $pdo
 * @param int $id
 * @param int $word_count
 * @return void
 */
function updateWordCount(PDO $pdo, int $id, int $word_count): void
{
    static $stmt = null;

    if ($stmt === null) {
        $stmt = $pdo->prepare("UPDATE shared_texts SET word_count = ? WHERE id = ? AND type = ?");
    }

    $stmt->execute([$word_count, $id, TYPE_VIDEO]);
}

try {
    $rows = fetchVideoTexts($pdo);
    $checked_count = 0;
    $changed_count = 0;
    $skipped_count = 0;

    if ($apply_changes) {
        $pdo->beginTransaction();
    }

    foreach ($rows as $row) {
        $checked_count++;
        $transcript_text = TextsUtilities::extractFromXML($row["text"]);

        if ($transcript_text === false) {
            $skipped_count++;
            fwrite(STDERR, "Skipped shared_texts.id={$row["id"]}: transcript XML could not be extracted.\n");
            continue;
        }

        $current_word_count = (int)$row["word_count"];
        $new_word_count = countWords($transcript_text);

        if ($new_word_count === $current_word_count) {
            continue;
        }

        $changed_count++;
        echo "shared_texts.id={$row["id"]}: {$current_word_count} -> {$new_word_count}\n";

        if ($apply_changes) {
            updateWordCount($pdo, (int)$row["id"], $new_word_count);
        }
    }

    if ($apply_changes) {
        $pdo->commit();
    }

    $mode = $apply_changes ? "Applied" : "Dry run";
    echo "{$mode}. Checked: {$checked_count}. Changed: {$changed_count}. Skipped: {$skipped_count}.\n";

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
