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

require_once('../includes/dbinit.php'); // connect to database
require_once(APP_ROOT . 'includes/checklogin.php'); // check if logged in and set $user

use Aprelendo\Includes\Classes\User;

// only premium users are allowed to visit this page
if (!$user->isPremium()) {
    header('Location:texts.php');
    exit;
}

require_once('header.php');

?>

    <div class="container mtb">
        <div class="row">
            <div class="col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="texts.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="active">Add RSS article</a>
                    </li>
                </ol>
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> All RSS texts you add to Aprelendo will be shared with the rest of our community. You will find them in the "<a href="sharedtexts.php">shared texts</a>" section.</div>
            </div>
            <div class="col-12">
                <div class="row flex">
                    <div class="col-sm-12">
                        <div class="lds-ripple mx-auto">
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/addrss.js"></script>
    <?php require_once('footer.php') ?>

    