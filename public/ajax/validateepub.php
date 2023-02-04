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

use Aprelendo\Includes\Classes\AprelendoException;

// check that $_POST is set & not empty
if (!isset($_POST) || empty($_POST)) {
    exit;
}

try {
    $file_uri = APP_ROOT . 'uploads/' . $_POST['filename'];

    // check file name & if file exists
    if (!isset($_POST['filename']) || empty($_POST['filename']) || !file_exists($file_uri)) {
        throw new AprelendoException("Incorrect file name error.");
    }

    // verify epub file structure & integrity ($return == non-zero in case of error)
    exec('java -jar ' . APP_ROOT . 'tools/epubcheck/epubcheck.jar ' . $file_uri, $output, $return);
    
    if ($return) {
        throw new AprelendoException('Your ebook was uploaded, but it contains errors and may not render well. '
            . 'For more info go to http://validator.idpf.org/');
    }
} catch (\Exception $e) {
    $error = array('error_msg' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
}
