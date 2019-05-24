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
require_once PUBLIC_PATH . 'simpleheader.php';
use Aprelendo\Includes\Classes\User;
?>

<div>
    <div class="container mtb">
        <div class="row">
            <div class="col-sm-0 col-sm-1 col-lg-3"></div>
            <div class="col-sm-12 col-sm-10 col-lg-6">
                <section>
                    <header>
                        <h1 class="text-center">Account activation</h1>
                    </header>
                    <br />
                    <div id="alert_msg" class="d-none"></div>

                    <?php 
                // 1. check if username & hash values passed by the reset link are set
                if (isset($_GET['username']) && !empty($_GET['username']) && isset($_GET['hash']) && !empty($_GET['hash'])) :
                ?>

                    <?php 
                $user = new User($con);
                if ($user->activate($_GET['username'], $_GET['hash'])) : ?>

                    <div id="alert_msg_2" class="alert alert-success">Congratulations! Your account is now active.</div>

                    <p>You can now login with the username and password you provided when you signed up.</p>

                    <div class="text-center">
                        <a href="login.php" class="btn btn-lg btn-success">Login now</a>
                    </div>

                    <br />
                    <br />

                    <?php
                else : // activation failed
                ?>

                    <div id="alert_msg_2" class="alert alert-danger">Your account activation failed.</div>

                    <?php
                endif; 
                else : // $_GET parameters not set or empty
                ?>

                    <div id="alert_msg_2" class="alert alert-danger">The activation link seems to be malformed. Please
                        try again using the one provided in the email we've sent you.</div>

                    <?php 
                endif;
                ?>

                    <br />
                    <footer>
                    </footer>
                </section>
            </div>
            <div class="col-sm-0 col-sm-1 col-lg-3"></div>
        </div>
    </div>
</div>


<?php require_once 'footer.php'?>