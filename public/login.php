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

require_once '../Includes/dbinit.php'; // connect to database

use Aprelendo\User;
use Aprelendo\UserAuth;

$user = new User($pdo);
$user_auth = new UserAuth($user);

// if user is already logged in, go to "My Texts" section
if ($user_auth->isLoggedIn()) {
    header('Location:/texts');
    exit;
}

require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'simpleheader.php';
?>

<main class="d-flex flex-grow-1 flex-column">
    <div class="container mtb">
        <div class="row">
            <div class="col-sm-12 col-md-10 offset-md-1 col-lg-6 offset-lg-3">
                <section>
                    <header>
                        <h3 class="text-center">Sign in</h3>
                    </header>
                    <br>
                    <div id="alert-box" class="d-none"></div>
                    <div id="g_id_onload"
                        data-client_id="913422235077-082170c2l6b58ck8ie0f03rigombl2pc.apps.googleusercontent.com"
                        data-callback="googleSignIn">
                    </div>
                    <div class="g_id_signin" data-type="standard"></div>

                    <div class="or-line">
                        <p class="or__line">
                            <span class="or__text">Or</span>
                        </p>
                    </div>
                    <form id="form_login">
                        <div class="mb-3">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" class="form-control" maxlength="20"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="password">Password:</label>
                            <small>
                                <em>at least 8 characters (including letters, digits &amp; special characters)</em>
                            </small>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control"
                                    pattern="(?=.*[0-9a-zA-Z])(?=.*[~`!@#$%^&*()\-_+={};:\[\]\?\.\/,]).{8,}"
                                    autocomplete="off" required>
                                <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                    aria-label="Show/hide password" tabindex="-1"><span class="bi bi-eye-slash-fill"
                                        aria-hidden="true"></span></button>
                            </div>
                            <small id="password-strength-text"></small>
                        </div>
                        <p></p>
                        <div class="d-grid gap-2">
                            <button type="submit" id="btn_login" class="btn btn-success">Sign in</button>
                        </div>
                    </form>
                    <br>
                    <footer>
                        <p class="text-muted text-center">
                            <a href="/forgotpassword">Forgot password</a>?
                        </p>
                        <p class="text-muted text-center">Not registered? <a href="/register">Create an account</a>
                            <br>
                            <small>If you enjoy using Aprelendo, please consider <a href="/donate">becoming a
                                Patron!</a></small>
                        </p>

                    </footer>
                </section>
            </div>
        </div>
    </div>
</main>

<script defer src="/js/login.min.js"></script>
<script defer src="/js/password.min.js"></script>
<script defer src="/js/helpers.min.js"></script>

<?php require_once 'footer.php'?>
