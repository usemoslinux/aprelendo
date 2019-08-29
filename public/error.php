<?php 
/**
 * Copyright (C) 2018 Pablo Castagnino
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

$user = new User($con);

if (!$user->isLoggedIn()) {
    require_once PUBLIC_PATH . 'simpleheader.php';
} else {
    require_once PUBLIC_PATH . 'header.php';
}
?>
    <div class="container mtb">
        <div class="row">
            <div class="col-sm-12">
                <h1>Hmm... that's weird!</h1>
                <h5>There was a fatal error trying to connect to the database. Please try again later.</h5>
                <br>
                <button type="button" class="btn btn-lg btn-success" onclick="window.location.href='index.php'">Go Home</button>
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