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

use Aprelendo\Includes\Classes\User;

$user = new User($pdo);

// if user is already logged in, go to "My Texts" section
if ($user->isLoggedIn()) {
    header('Location:/texts.php');
    exit;
}

require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'simpleheader.php';
?>

<main>
    <div class="container mtb d-flex flex-grow-1 flex-column">
        <div class="row">
            <div class="col-sm-12 col-md-10 offset-md-1 col-lg-6 offset-lg-3">
                <section>
                    <header>
                        <h3 class="text-center">Sign in</h3>
                    </header>
                    <br>
                    <div id="error-msg" class="d-none"></div>
                    <div class="g-signin2" data-longtitle="true" data-onsuccess="googleSignIn" data-theme="dark"></div>
                    <div class="or-line">
                        <p class="or__line">
                            <span class="or__text">Or</span>
                        </p>
                    </div>
                    <form id="form_login">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" class="form-control" maxlength="20"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" class="form-control"
                                pattern="(?=.*[0-9a-zA-Z])(?=.*[~`!@#$%^&*()\-_+={};:\[\]\?\.\/,]).{8,}"
                                title="Password must contain a letter, a special character and a digit. Password length must be minimum 8 characters"
                                autocomplete="off">
                        </div>
                        <p>

                        </p>
                        <button type="submit" id="btn_login" class="btn btn-block btn-success">Sign in</button>
                    </form>
                    <br>
                    <footer>
                        <p class="text-muted text-center">
                            <a href="forgotpassword.php">Forgot password</a>?
                        </p>
                        <p class="text-muted text-center">Not registered? <a href="register.php">Create an account</a>
                            <br>
                            <small>If you like Aprelendo, you can later upgrade to <a
                                    href="gopremium.php">Premium</a></small>
                        </p>

                    </footer>
                </section>
            </div>
        </div>
    </div>
</main>

<script defer src="js/login-min.js"></script>

<?php require_once 'footer.php'?>
