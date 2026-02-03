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

// Disable all error reporting
error_reporting(0);

// Ensure no errors are displayed to the user
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
?>

<main class="d-flex flex-grow-1 align-items-center">
    <div class="container mtb">
        <div class="row">
            <div class="col-12 col-lg-6 offset-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center my-2">Reset password</h3>
                        <br>
                        <?php
                        // 1. Check if a token is present in the URL. If so, show the password reset form.
                        if (isset($_GET['token']) && !empty($_GET['token'])) {
                            $token = htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8');
                        ?>
                            <form id="form_create_new_password">
                                <input type="hidden" id="token" name="token" value="<?php echo $token; ?>">
                                <div class="mb-3">
                                    <label for="newpassword">Password:</label>
                                    <div class="input-group">
                                        <input type="password" id="newpassword" name="newpassword" class="form-control"
                                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                            autocomplete="off" required>
                                        <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                            aria-label="Show/hide password" tabindex="-1"><span class="bi bi-eye-slash-fill"
                                                aria-hidden="true"></span></button>
                                    </div>
                                    <small>
                                        <em>8+ characters, with uppercase, lowercase, and a number.</em>
                                    </small>
                                    <br>
                                    <small id="password-strength-text"></small>
                                </div>
                                <div class="mb-3">
                                    <label for="newpassword-confirmation">Confirm password:</label>
                                    <div class="input-group">
                                        <input type="password" id="newpassword-confirmation" name="newpassword-confirmation"
                                            class="form-control"
                                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                            autocomplete="off" required>
                                        <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                            aria-label="Show/hide password confirmation" tabindex="-1">
                                            <span class="bi bi-eye-slash-fill" aria-hidden="true"></span></button>
                                    </div>
                                    <small id="passwords-match-text"></small>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" id="create_new_password" class="btn btn-success">
                                        Save new password
                                    </button>
                                </div>
                            </form>

                        <?php
                        } else { // 2. If no token is present, show the form to request a reset link.
                        ?>

                            <div id="alert-box" class="alert alert-info">
                                <div class="alert-flag fs-5">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Information
                                </div>
                                <div class="alert-msg">
                                    Please provide your email address below, and we will send you a secure link that will
                                    allow you to reset your password. This link will be sent to the email address associated
                                    with your account, so please make sure it's accurate. If an account exists for this email, you will receive instructions shortly.
                                </div>
                            </div>
                            <form id="form_forgot_password">
                                <div class="mb-3">
                                    <label for="email">E-mail address:</label>
                                    <input type="email" id="email" name="email" class="form-control" maxlength="50" required>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" id="btn_forgot_password" class="btn btn-success">
                                        Request password reset
                                    </button>
                                </div>
                            </form>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script defer src="/js/forgotpassword.min.js"></script>
<script defer src="/js/password.min.js"></script>
<script defer src="/js/helpers.min.js"></script>

<?php require_once 'footer.php' ?>