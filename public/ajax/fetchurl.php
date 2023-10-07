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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once '../../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // load $user & $user_auth objects & check if user is logged

use Aprelendo\Curl;
use Aprelendo\InternalException;
use Aprelendo\UserException;

try {
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => FICTIONAL_USER_AGENT,
        CURLOPT_FOLLOWLOCATION => true
    ];
    if (!empty($_GET['url'])) {
        $url = $_GET['url'];
        $file_contents = Curl::getUrlContents($url, $options);
        $url = Curl::getFinalUrl($url);
        $result = $file_contents ? ['url' => $url, 'file_contents' => $file_contents] : '';
        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        throw new UserException('Error retrieving that URL. Please check it is not empty or malformed.');
    }
} catch (InternalException | UserException $e) {
    echo $e->getJsonError();
}
