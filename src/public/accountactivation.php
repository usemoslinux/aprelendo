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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once '../Includes/dbinit.php'; // connect to database
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'simpleheader.php';

use Aprelendo\User;
use Aprelendo\UserRegistrationManager;

?>

<div class="d-flex flex-grow-1 flex-column">
    <div class="container mtb">
        <div class="row">
            <div class="col-sm-0 col-sm-1 col-lg-3"></div>
            <div class="col-sm-12 col-sm-10 col-lg-6">
                <main class="my-5">
                    <div class="card">
                        <div class="card-body text-center">
                            <?php
                            if (!empty($_GET['username']) && !empty($_GET['hash'])) {
                                // check if username & hash values passed by the reset link are set
                                try {
                                    $user = new User($pdo);
                                    $user_reg = new UserRegistrationManager($user);
                                    $user_reg->activate($_GET['username'], $_GET['hash']);
                                    echo <<<'HTML_REGISTRATION_SUCCESS'
                                        <div class="display-1 text-success">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>

                                        <h1 class="text-success my-3">Welcome to Aprelendo!</h1>
                                        <p class="lead text-muted">
                                            Your account has been activated.
                                        </p>

                                        <p class="mt-4">
                                            Log in now to start learning new words
                                            and expanding your vocabulary in fun and engaging ways!
                                        </p>

                                        <a href="/login" class="btn btn-success btn-lg mt-3 px-5">
                                            <i class="bi bi-box-arrow-in-right"></i> Log in
                                        </a>
                                    HTML_REGISTRATION_SUCCESS;
                                } catch (\Exception $e) {
                                    echo <<<'HTML_REGISTRATION_FAILED'
                                    <div class="display-1 text-danger">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </div>

                                    <h1 class="text-danger my-3">Registration Failed</h1>
                                    <p class="lead text-muted">
                                        Something went wrong, and we couldn't activate your account.
                                        This might be due to a technical issue or incomplete information.
                                    </p>

                                    <p class="mt-4">
                                        Please try again later. If the issue persists,
                                        <a href="https://www.aprelendo.com/contact">contact</a> our support team
                                        for assistance.
                                    </p>

                                    <a href="/" class="btn btn-danger btn-lg mt-3 px-5">
                                        <i class="bi bi-arrow-left"></i> Back to Home
                                    </a>
                                    HTML_REGISTRATION_FAILED;
                                }
                            } else { // $_GET parameters not set or empty
                                echo <<<'HTML_MALFORMED_URL'
                                <div class="display-1 text-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>

                                <h1 class="text-warning my-3">Invalid Activation Link</h1>
                                <p class="lead text-muted">
                                    The activation link seems to be malformed or incomplete. Please try again using
                                    the correct link provided in the email we sent you.
                                </p>

                                <p class="mt-4">
                                    If you believe this is an error or the issue persists, feel free to
                                    <a href="https://www.aprelendo.com/contact">contact</a> our support team for
                                    assistance.
                                </p>

                                <a href="/" class="btn btn-warning btn-lg mt-3 px-5">
                                    <i class="bi bi-arrow-left"></i> Back to Home
                                </a>
                                HTML_MALFORMED_URL;
                            }
                            ?>
                        </div>
                    </div>
                </main>
            </div>
            <div class="col-sm-0 col-sm-1 col-lg-3"></div>
        </div>
    </div>
</div>

<?php require_once 'footer.php' ?>