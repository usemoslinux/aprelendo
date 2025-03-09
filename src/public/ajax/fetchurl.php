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

use Aprelendo\Curl;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    if (!empty($_GET['url'])) {
        $url = $_GET['url'];
        $url = Curl::getFinalUrl($url);
        $file_contents = Curl::getUrlContents($url);
        $file_lang = extractLang($file_contents);
        $result = $file_contents ? ['url' => $url, 'lang' => $file_lang, 'file_contents' => $file_contents] : '';
        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        throw new UserException('Error retrieving that URL. Please check it is not empty or malformed.');
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}

function extractLang($html) {
    $doc = new DOMDocument();
    @$doc->loadHTML($html); // Suppress warnings for malformed HTML

    $html_tag = $doc->getElementsByTagName('html')->item(0);
    
    if ($html_tag && $html_tag->hasAttribute('lang')) {
        $lang = $html_tag->getAttribute('lang');
        
        // Normalize to two-letter code
        return strtolower(substr($lang, 0, 2));
    }

    return ''; // Return empty if no lang attribute is found
}