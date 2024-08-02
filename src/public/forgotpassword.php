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

?>

<main class="d-flex flex-grow-1 flex-column">
    <div class="container mtb">
        <div class="row">
            <div class="col-sm-0 col-sm-1 col-lg-3"></div>
            <div class="col-sm-12 col-sm-10 col-lg-6">
                <section>
                    <header>
                        <h3 class="text-center">Restore password</h3>
                    </header>
                    <br>

                    <?php
                    // 1. check if email & password values passed by the reset link are set
                    if (isset($_GET['email']) && isset($_GET['reset'])) {
                        // check if email & password exist in db
                        $email = $_GET['email'];
                        $password_hash = $_GET['reset'];
                        $user = new User($pdo);
                                
                        // 1.1. if email & password values passed by the reset link are found in db, then...
                        if ($user->existsByEmailAndPasswordHash($email, $password_hash)) {
                    ?>

                    <div id="alert-box" class="alert alert-info">
                        <div class="alert-flag fs-5">
                            <i class="bi bi-info-circle-fill"></i>
                            Information
                        </div>
                        <div class="alert-msg">
                            Enter your new password twice.
                        </div>
                    </div>

                    <form id="form_create_new_password">
                        <input type="hidden" id="email" name="email" value="<?php echo $email ; ?>">
                        <div class="mb-3">
                            <label for="newpassword">Password:</label>
                            <small>
                                <em>at least 8 characters (including letters, digits &amp; special characters)</em>
                            </small>
                            <div class="input-group">
                                <input type="password" id="newpassword" name="newpassword" class="form-control"
                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                    autocomplete="off" required>
                                <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                    aria-label="Show/hide password" tabindex="-1"><span class="bi bi-eye-slash-fill"
                                        aria-hidden="true"></span></button>
                            </div>
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
                        } else { // 1.2. if email & password are set but not found in db, then ...
                    ?>

                    <div id="alert-box" class="alert alert-danger">
                        <div class="alert-flag fs-5">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            Information
                        </div>
                        <div class="alert-msg">
                            Can't reset user password. Possibly the recovery link has expired.
                        </div>
                    </div>

                    <?php
                        }
                    } else { // 2. if email & password are NOT set, show form to send the reset password link
                    ?>

                    <div id="alert-box" class="alert alert-info">
                        <div class="alert-flag fs-5">
                            <i class="bi bi-info-circle-fill"></i>
                            Information
                        </div>
                        <div class="alert-msg">
                            Please provide your email address below, and we will send you a secure link that will
                            allow you to reset your password. This link will be sent to the email address associated
                            with your account, so please make sure it's accurate.
                        </div>
                    </div>

                    <form id="form_forgot_password">
                        <div class="mb-3">
                            <label for="email">E-mail address:</label>
                            <input type="email" id="email" name="email" class="form-control" maxlength="50" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" id="btn_forgot_password" class="btn btn-success">
                                Request password
                            </button>
                        </div>
                    </form>

                    <?php
                    }
                    ?>

                    <br>
                    <footer>
                    </footer>
                </section>
            </div>
            <div class="col-sm-0 col-sm-1 col-lg-3"></div>
        </div>
    </div>
</main>

<script defer src="/js/forgotpassword.min.js"></script>
<script defer src="/js/password.min.js"></script>
<script defer src="/js/helpers.min.js"></script>

<?php require_once 'footer.php'?>
