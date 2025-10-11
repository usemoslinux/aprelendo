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
require_once APP_ROOT . 'Includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

use Aprelendo\Texts;
use Aprelendo\SharedTexts;
use Aprelendo\Curl;
use Aprelendo\InternalException;
use Aprelendo\UserException;

const DEFAULT_LEVEL = 2;

function respond_json(int $code, array|string|null $payload = null): void {
    http_response_code($code);
    if ($payload !== null) {
        header('Content-Type: application/json; charset=utf-8');
        echo is_array($payload) ? json_encode($payload) : $payload;
    }
}

function normalize_post(array $post): array {
    return array_map(static function ($v) {
        return is_string($v) ? trim(str_replace("\r", '', $v)) : $v; // unify CRLF/LF once
    }, $post);
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

/**
 * Loads the text record and verifies the current user can edit it.
 * Keeps your original authorization logic: if loadRecord leaves title/text empty, deny.
 */
function load_text_for_edit(PDO $pdo, int $userId, int $langId, int $id): Texts {
    $texts = new Texts($pdo, $userId, $langId);
    $texts->loadRecord($id);
    if (empty($texts->title) && empty($texts->text)) {
        throw new UserException('You are not authorized to edit this text!');
    }
    return $texts;
}

// check that $_POST is set & not empty (preserve original behavior)
if (!isset($_POST) || empty($_POST)) {
    exit;
}

try {
    $user_id = (int)$user->id;
    $lang_id = (int)$user->lang_id;

    $post = normalize_post($_POST);

    $id         = (int)($post['id'] ?? 0);
    $title      = $post['title']      ?? '';
    $author     = $post['author']     ?? '';
    $source_uri = $post['url']        ?? '';
    $audio_uri  = $post['audio-url']  ?? '';
    $text       = $post['text']       ?? '';
    $type       = (int)($post['type'] ?? 0);
    $level      = (int)($post['level'] ?? DEFAULT_LEVEL);
    $is_shared  = !empty($post['shared-text']);

    if ($id === 0) {
        throw new InternalException('Text id is empty');
    }

    $texts_table = load_text_for_edit($pdo, $user_id, $lang_id, $id);

    ensure_required($title, 'Title is a required field. Please enter one and try again.');
    ensure_required($text,  'Text is a required field. Please enter one and try again.');
    validate_audio($audio_uri);

    // partial update of the existing private text
    $update_record = compact('title', 'author', 'text', 'source_uri', 'audio_uri', 'type', 'level');
    $texts_table->update($id, $update_record);
    
    if ($is_shared) {
        $texts_table->share($id);
    }
    
    respond_json(204);

} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}