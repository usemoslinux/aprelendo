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
require_once PUBLIC_PATH . 'simpleheader.php';

use Aprelendo\Includes\Classes\User;
use Aprelendo\Includes\Classes\UserRegistrationManager;

?>

<div>
    <div class="container mtb d-flex flex-grow-1 flex-column">
        <div class="row">
            <div class="col-sm-0 col-sm-1 col-lg-3"></div>
            <div class="col-sm-12 col-sm-10 col-lg-6">
                <main>
                    <?php
                    if (!empty($_GET['username']) && !empty($_GET['hash'])) {
                        // check if username & hash values passed by the reset link are set
                        try {
                            $user = new User($pdo);
                            $user_reg = new UserRegistrationManager($user);
                            $user_reg->activate($_GET['username'], $_GET['hash']);
                            echo '<div class="text-success text-center">
                                <div class="display-1">
                                <span class="far fa-check-circle"></span>
                                </div>
                                <div id="alert_msg_2">Congratulations! Your account is now active.</div>
                                </div>
                                <div class="text-center">You can now login with the username and password you
                                provided when you signed up.</div>
                                <br><div class="text-center"><a href="/login" class="btn btn-lg btn-success">
                                Login now</a></div><br>';
                        } catch (\Exception $e) {
                            echo '<div class="text-danger text-center">
                            <div class="display-1">
                            <span class="fas fa-times-circle"></span>
                            </div>
                            <div id="alert_msg_2">Oh no! Your account activation failed.</div>
                            </div>
                            <br>
                            <div class="text-center">Try again later or <a
                            href="https://www.aprelendo.com/contact">contact support</a> for help.</div><br>';
                        }
                    } else { // $_GET parameters not set or empty
                        echo "<div id='alert_msg_2' class='alert alert-danger'>The activation link seems to be
                        malformed. Please try again using the one provided in the email we've sent you.</div>";
                    }
                    ?>
                    <br>
                </main>
            </div>
            <div class="col-sm-0 col-sm-1 col-lg-3"></div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'?>
