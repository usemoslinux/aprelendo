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
use Aprelendo\TextDifficultyClassifier;

$pdo = Database::connection();
$classifier = new TextDifficultyClassifier($pdo);

/**
 * Encodes classifier metrics as JSON for storage.
 *
 * @param array $metrics
 * @return string
 */
function encode_metrics(array $metrics): string
{
    $metrics_json = json_encode($metrics, JSON_UNESCAPED_UNICODE);

    if ($metrics_json === false) {
        throw new RuntimeException("Could not encode difficulty metrics.");
    }

    return $metrics_json;
}

/**
 * Builds update values, preserving the current level when no tokens can be classified.
 *
 * @param TextDifficultyClassifier $classifier
 * @param array $row
 * @return array
 */
function build_update_values(
    TextDifficultyClassifier $classifier,
    array $row,
): array {
    $result = $classifier->classify($row["text"], $row["lang_iso"]);

    if ($result["metrics"]["total_tokens"] === 0 && $row["level"] !== null) {
        $result["level"] = (int) $row["level"];
        $result["score"] = null;
        $result["confidence"] =
            TextDifficultyClassifier::DIFFICULTY_CONFIDENCE_LOW;
    }

    return [
        $result["level"],
        $result["score"],
        $result["confidence"],
        encode_metrics($result["metrics"]),
        $result["version"],
        $row["id"],
    ];
}

/**
 * Recalculates difficulty for private texts.
 *
 * @param PDO $pdo
 * @param TextDifficultyClassifier $classifier
 * @return int
 */
function recalculate_private_texts(
    PDO $pdo,
    TextDifficultyClassifier $classifier,
): int {
    $sql = "SELECT t.id, t.text, t.level, l.name AS lang_iso
            FROM texts t
            JOIN languages l ON l.id = t.lang_id
            WHERE l.name IS NOT NULL
            AND l.name != ''
            AND (
                t.level IS NULL
                OR t.difficulty_version IS NULL
                OR t.difficulty_version != 'v1'
            )
            LIMIT 100";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $update = $pdo->prepare("UPDATE texts
        SET level = ?,
            difficulty_score = ?,
            difficulty_confidence = ?,
            difficulty_metrics = ?,
            difficulty_version = ?,
            difficulty_updated_at = NOW()
        WHERE id = ?");

    foreach ($rows as $row) {
        $update->execute(build_update_values($classifier, $row));
    }

    return count($rows);
}

/**
 * Recalculates difficulty for shared texts.
 *
 * @param PDO $pdo
 * @param TextDifficultyClassifier $classifier
 * @return int
 */
function recalculate_shared_texts(
    PDO $pdo,
    TextDifficultyClassifier $classifier,
): int {
    $sql = "SELECT st.id, st.text, st.level, l.name AS lang_iso
            FROM shared_texts st
            JOIN languages l ON l.id = st.lang_id
            WHERE l.name IS NOT NULL
            AND l.name != ''
            AND (
                st.level IS NULL
                OR st.difficulty_version IS NULL
                OR st.difficulty_version != 'v1'
            )
            LIMIT 100";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $update = $pdo->prepare("UPDATE shared_texts
        SET level = ?,
            difficulty_score = ?,
            difficulty_confidence = ?,
            difficulty_metrics = ?,
            difficulty_version = ?,
            difficulty_updated_at = NOW()
        WHERE id = ?");

    foreach ($rows as $row) {
        $update->execute(build_update_values($classifier, $row));
    }

    return count($rows);
}

try {
    $private_count = recalculate_private_texts($pdo, $classifier);
    $shared_count = recalculate_shared_texts($pdo, $classifier);

    echo "Updated private texts: {$private_count}\n";
    echo "Updated shared texts: {$shared_count}\n";
} catch (Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . "\n");
    exit(1);
}

