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

?>

<main>
    <div class="container mtb d-flex flex-grow-1 flex-column">
        <div class="row">
            <div class="col-sm-0 col-sm-1 col-lg-3"></div>
            <div class="col-sm-12 col-sm-10 col-lg-6">
                <section>
                    <header>
                        <h1 class="text-center">Restore password</h1>
                    </header>
                    <br>
                    

                    <?php 
                    // 1. check if email & password values passed by the reset link are set
                    if(isset($_GET['email']) && isset($_GET['reset'])) {
                        // check if email & password exist in db
                        $email = $_GET['email'];
                        $password_hash = $_GET['reset'];
                        $user = new User($pdo);
                                
                        // 1.1. if email & password values passed by the reset link are found in db, then...
                        if ($user->existsByEmailAndPasswordHash($email, $password_hash)) { // 
                    ?>

                    <div id="alert-msg" class="alert alert-info">Enter your new password twice.</div>
                    <form id="form_create_new_password">
                        <input type="hidden" id="email" name="email" value="<?php echo $email ; ?>">
                        <div class="form-group">
                            <label for="newpassword">Password:</label>
                            <small>
                                <i>at least 8 characters (including letters, numbers &amp; special characters)</i>
                            </small>
                            <div class="input-group">
                                <input type="password" id="newpassword" name="newpassword" class="form-control"
                                    pattern="(?=.*[0-9a-zA-Z])(?=.*[~`!@#$%^&*()\-_+={};:\[\]\?\.\/,]).{8,}"
                                    title="Password must contain a letter, a special character and a digit. Password length must be minimum 8 characters"
                                    autocomplete="off" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                        aria-label="Show/hide password" tabindex="-1"><i class="fas fa-eye-slash"
                                            aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <small id="password-strength-text"></small>
                        </div>
                        <div class="form-group">
                            <label for="newpassword-confirmation">Confirm password:</label>
                            <div class="input-group">
                                <input type="password" id="newpassword-confirmation" name="newpassword-confirmation"
                                    class="form-control"
                                    pattern="(?=.*[0-9a-zA-Z])(?=.*[~`!@#$%^&*()\-_+={};:\[\]\?\.\/,]).{8,}"
                                    title="Password must contain a letter, a special character and a digit. Password length must be minimum 8 characters"
                                    autocomplete="off" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                        aria-label="Show/hide password confirmation" tabindex="-1"><i
                                            class="fas fa-eye-slash" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <small id="passwords-match-text"></small>
                        </div>
                        <button type="submit" id="create_new_password" class="btn btn-block btn-success">Save new password</button>
                    </form>

                    <?php
                        } else { // 1.2. if email & password are set but not found in db, then ...
                    ?>

                    <div id="alert-msg" class="alert alert-danger">Can't reset user password. Possibly the recovery link has expired.</div>

                    <?php 
                        }
                    } else { // 2. if email & password are NOT set, show form to send the reset password link
                    ?>

                    <div id="alert-msg" class="alert alert-info">Enter your email address to receive a link to reset your password.</div>
                    <form id="form_forgot_password">
                        <div class="form-group">
                            <label for="email">E-mail address:</label>
                            <input type="email" id="email" name="email" class="form-control" maxlength="50" required>
                        </div>
                        <button type="submit" id="btn_forgot_password" class="btn btn-block btn-success">Request password</button>
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

<script defer src="js/forgotpassword-min.js"></script>
<script defer src="js/password-min.js"></script>

<?php require_once 'footer.php'?>

