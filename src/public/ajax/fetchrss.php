<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user

header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false];

if (empty($_GET)) {
    echo json_encode($response);
    exit;
}

use Aprelendo\Curl;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (empty($_GET['url'])) {
        throw new UserException('Empty URL.');
    }

    $url = $_GET['url'];
    $scheme = parse_url($url, PHP_URL_SCHEME);

    if (empty($scheme) || !in_array(strtolower($scheme), ['http', 'https'], true)) {
        throw new UserException('Invalid RSS URL. Please check it is not empty or malformed.');
    }

    $final_url = Curl::getFinalUrl($url);
    $file_contents = Curl::getUrlContents($final_url);
    $payload = $file_contents ? ['url' => $final_url, 'rss' => $file_contents] : '';

    $response = ['success' => true, 'payload' => $payload];
    echo json_encode($response);
    exit;
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
    exit;
} catch (Throwable $e) {
    echo json_encode($response);
    exit;
}