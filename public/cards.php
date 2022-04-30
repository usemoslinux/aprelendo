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
require_once APP_ROOT . 'includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';
?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="texts.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Cards</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-12">
            <main>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="alert-msg" class="d-none"></div>
                    </div>
                    <div class="col-sm-12">
                        <div class="d-flex justify-content-center">
                            <div id="card" class="card text-center" style="min-width: 100%;">
                                <div id="card-header" class="card-header"></div>
                                <div class="card-body">
                                    <div id="card-loader" class="lds-ellipsis m-auto">
                                        <div></div><div></div><div></div><div></div>
                                    </div>
                                    <p id="card-text" class="card-text"></p>
                                </div>
                                <div id="card-footer" class="card-footer">
                                    <p id="card-counter" class="card-text"></p>
                                    <p class="card-text">Did you remember the meaning?</p>
                                    <button id="btn-remember-yes" type="button" class="btn btn-success btn-remember">Yes</button>
                                    <button id="btn-remember-no" type="button" class="btn btn-danger btn-remember">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </main>
        </div>
    </div>
</div>

<?php 

require_once PUBLIC_PATH . 'showdicmodal.php'; // load dictionary modal window

?>
<script defer src="js/cards-min.js"></script>

<?php require_once 'footer.php' ?>