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
        throw new UserException('Error retrieving that URL. Please check it is not empty or malformed.');
    }

    $url = $_GET['url'];
    $url = Curl::getFinalUrl($url);
    $file_contents = Curl::getUrlContents($url);
    $file_lang = extractLang($file_contents);
    $payload = $file_contents ? ['url' => $url, 'lang' => $file_lang, 'file_contents' => $file_contents] : '';
    
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

function extractLang($html) {
    $doc = new DOMDocument();
    @$doc->loadHTML($html); // Suppress warnings for malformed HTML

    $html_tag = $doc->getElementsByTagName('html')->item(0);
    
    if ($html_tag?->hasAttribute('lang')) {
        $lang = $html_tag->getAttribute('lang');
        
        // Normalize to two-letter code
        return strtolower(substr($lang, 0, 2));
    }

    return ''; // Return empty if no lang attribute is found
}
