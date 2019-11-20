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

require_once '../includes/dbinit.php'; // connect to database
require_once PUBLIC_PATH . 'head.php';

use Aprelendo\Includes\Classes\User;

$user = new User($pdo);

if (!$user->isLoggedIn()) {
    require_once PUBLIC_PATH . 'simpleheader.php';
} else {
    require_once PUBLIC_PATH . 'header.php';
}

$error_title = isset($_GET['error']) ? $_GET['error'] : '';
$error_msg = isset($_GET['error_msg']) ? $_GET['error_msg'] : '';

// Hmm... that's weird!
// There was a fatal error trying to connect to the database. Please try again later.
?>
    <div class="container mtb">
        <div class="row">
            <div class="col-sm-12">
                <h1><?php echo $error_title; ?></h1>
                <h5><?php echo $error_msg; ?></h5>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
            </div>
            
        </div>
        <!-- /row -->
    </div>
    <!-- /container -->

    <?php require_once 'footer.php'?>