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

require_once '../../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & checks if user is logged in

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

use Aprelendo\Includes\Classes\Reader;

$reader = new Reader($pdo, $user->getId(), $user->getLangId());

// for ebooks, use slower method to underline words (as input is HTML code)
// for everything else, use faster method (as input is simple text)
if (isset($_POST['txt']) && isset($_POST['is_ebook'])) {
    if ($_POST['is_ebook'] == true) {
        $result = $reader->colorizeWords(html_entity_decode($_POST['txt']));
        echo $reader->addLinks($result);
    } else {
        echo $reader->colorizeWordsFast(html_entity_decode($_POST['txt']), $user_dic, $freq_words);
    }
}

?>
