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

if (empty($_POST)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\Texts;
use Aprelendo\SharedTexts;
use Aprelendo\Videos;
use Aprelendo\EbookFile;
use Aprelendo\LogFileUploads;
use Aprelendo\Gems;
use Aprelendo\Curl;
use Aprelendo\InternalException;
use Aprelendo\UserException;

const DEFAULT_LEVEL = 2;
const TYPE_ARTICLE  = 1;
const TYPE_EBOOK    = 6;

function normalize_post(array $post): array {
    return array_map(static function ($v) {
        return is_string($v) ? trim(str_replace("\r", '', $v)) : $v;
    }, $post);
}

function selected_texts_table(PDO $pdo, int $userId, int $langId, bool $isShared): Texts|SharedTexts {
    return $isShared ? new SharedTexts($pdo, $userId, $langId)
                     : new Texts($pdo, $userId, $langId);
}

function ensure_required(string $value, string $message): void {
    if ($value === '') {
        throw new UserException($message);
    }
}

function validate_audio(?string &$audioUri): void {
    if ($audioUri === null || $audioUri === '') {
        return;
    }
    $audioUri = Curl::getFinalUrl($audioUri);
    $headers = @get_headers($audioUri);
    if (!$headers || stripos($headers[0], '200') === false) {
        throw new UserException('The provided audio file cannot be accessed. Try another URL address.');
    }
}

function ensure_not_exists(Texts|SharedTexts $table, string $sourceUri, bool $isShared): void {
    if ($sourceUri === '') {
        return;
    }
    if ($table->exists($sourceUri)) {
        $msg = $isShared
            ? 'The text you are trying to add already exists. Look for it in the <a class="alert-link" href="/sharedtexts">shared texts</a> section.'
            : 'The text you are trying to add already exists. Look for it in your <a class="alert-link" href="/texts">private library</a>. '
            . 'Remember that you may have <a class="alert-link" href="/texts?sa=1">archived</a> it.';
        throw new UserException($msg);
    }
}

function award_gems(PDO $pdo, int $userId, int $langId, string $tz): void {
    $events = ['texts' => ['new' => 1]];
    (new Gems($pdo, $userId, $langId, $tz))->updateScore($events);
}

function handle_simple_or_video(PDO $pdo, int $userId, int $langId, array $r, string $mode): ?array {
    $title      = $r['title']      ?? '';
    $author     = $r['author']     ?? '';
    $source_uri = $r['url']        ?? '';

    if ($mode === 'video' && Videos::isYTVideo($r['url'])) {
        $source_uri = Videos::toDesktopUrl($r['url']);
    }

    $audio_uri  = $r['audio-url']  ?? '';
    $text       = $r['text']       ?? '';
    $type       = (int)($r['type'] ?? 0);
    $level      = (int)($r['level'] ?? DEFAULT_LEVEL);
    $is_shared  = ($mode === 'video') || !empty($r['shared-text']);

    ensure_required($title, 'Title is a required field. Please enter one and try again.');
    ensure_required($text, 'Text is a required field. Please enter one and try again. In case you are uploading a video, enter a valid YouTube URL and fetch the correct transcript. Only videos with subtitles in your target language are supported.');
    validate_audio($audio_uri);

    $texts_table = selected_texts_table($pdo, $userId, $langId, $is_shared);
    ensure_not_exists($texts_table, $source_uri, $is_shared);

    $texts_table->add($title, $author, $text, $source_uri, $audio_uri, $type, $level);

    return null;
}

function handle_rss(PDO $pdo, int $userId, int $langId, array $r): array {
    if (!isset($r['title'], $r['text'])) {
        throw new UserException('Missing fields.');
    }

    $title      = $r['title'];
    $author     = $r['author']     ?? '';
    $source_uri = $r['url']        ?? '';
    $text       = $r['text'];
    $type       = TYPE_ARTICLE;
    $level      = (int)($r['level'] ?? DEFAULT_LEVEL);

    ensure_required($title, 'Title is a required field.');
    ensure_required($text,  'Text is a required field.');

    $texts_table = new SharedTexts($pdo, $userId, $langId);
    ensure_not_exists($texts_table, $source_uri, true);

    $insert_id = (int)$texts_table->add($title, $author, $text, $source_uri, '', $type, $level);
    if ($insert_id <= 0) {
        throw new UserException('There was an error saving this text.');
    }

    return ['insert_id' => $insert_id];
}

function handle_ebook(PDO $pdo, int $userId, int $langId, array $r, array $files): array {
    if (!isset($r['title'], $r['author'], $files['url'])) {
        throw new UserException('Please, complete all the required fields: name, author & epub file.');
    }

    $title  = $r['title'];
    $author = $r['author'];
    $type   = TYPE_EBOOK;
    $level  = (int)($r['level'] ?? DEFAULT_LEVEL);
    $audio  = $r['audio-uri'] ?? '';

    ensure_required($title,  'Please enter a title.');
    ensure_required($author, 'Please enter an author.');

    if (!isset($files['url']) || $files['url']['error'] === UPLOAD_ERR_NO_FILE) {
        throw new UserException('File not found. Please select a file to upload.');
    }

    $file_upload_log = new LogFileUploads($pdo, $userId);
    if ($file_upload_log->countTodayRecords() >= $file_upload_log::MAX_UPLOAD_LIMIT) {
        throw new UserException('Sorry, you have reached your file upload limit for today.');
    }

    $ebook = new EbookFile($files['url']['name']);
    $ebook->put($files['url'], true);
    $ebook->strip();
    $stored = $ebook->name;

    $texts_table = new Texts($pdo, $userId, $langId);
    $insert_id = (int)$texts_table->add($title, $author, '', $stored, $audio, $type, $level);
    if ($insert_id <= 0) {
        throw new UserException('There was an error uploading this text.');
    }

    $file_upload_log->addRecord();

    return ['filename' => $stored, 'insert_id' => $insert_id];
}

try {
    $user_id = (int)$user->id;
    $lang_id = (int)$user->lang_id;

    $post = normalize_post($_POST);
    $mode = $post['mode'] ?? '';

    switch ($mode) {
        case 'simple':
        case 'video': {
            $payload = handle_simple_or_video($pdo, $user_id, $lang_id, $post, $mode);
            $response = ['success' => true, 'payload' => $payload];
            break;
        }

        case 'rss': {
            $payload = handle_rss($pdo, $user_id, $lang_id, $post);
            $response = ['success' => true, 'payload' => $payload];
            break;
        }

        case 'ebook': {
            $payload = handle_ebook($pdo, $user_id, $lang_id, $post, $_FILES);
            $response = ['success' => true, 'payload' => $payload];
            break;
        }

        default:
            // keep original silent default by not throwing, but it's clearer to error:
            throw new UserException('Unknown mode.');
    }

    if ($response['success']) {
        award_gems($pdo, $user_id, $lang_id, $user->time_zone);
    }
    
    echo json_encode($response);
    exit;
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}
