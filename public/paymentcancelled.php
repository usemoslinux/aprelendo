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
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & checks if user is logged in
require_once PUBLIC_PATH . 'head.php';

?>

    <div class="container mtb d-flex flex-grow-1 flex-column">
        <div class="row">
            <div class="col-sm-12">
                <div class="d-flex flex-row justify-content-center">
                    <img class="img-fluid" style="max-width: 150px;" src="img/logo.svg" alt="Aprelendo logo">
                </div>
                <br>

                <h1 class="text-danger text-center"><i class="far fa-check-circle"></i> Paypal payment cancelled</h1>
                <h5 class="text-center">Your susbscription to Aprelendo was cancelled</h5>
                <hr>
                <div class="text-center">
                    <a href="/" class="btn btn-primary btn-lg" role="button">Go back to Home page</a>
                </div>
            </div>
            
        </div>
    </div>

<?php require_once 'footer.php'?>
