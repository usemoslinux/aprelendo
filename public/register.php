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
?>

<div>
    <div class="container mtb">
        <div class="row">
            <div class="col-12 col-lg-6 offset-lg-3">
                <section>
                    <header>
                        <h1 class="text-center">
                            <?php
                            $title_array = array('English' => array('en', 'Welcome!'),
                                'Spanish' => array('es', '¡Bienvenido!'),
                                'Portuguese' => array('pt', 'Bemvindo!'),
                                'French' => array('fr', 'Bienvenue!'),
                                'Italian' => array('it', 'Benvenuto!'),
                                'German' => array('de', 'Willkommen!'));
                            
                            $to_lang = isset($_GET['tolang']) ? htmlspecialchars(ucfirst($_GET['tolang']), ENT_QUOTES, 'UTF-8') : 'English';
                            $native_lang = isset($_GET['srclang']) ? ucfirst($_GET['srclang']) : 'English';
                            
                            echo '<img src="img/flags/' . $title_array["$to_lang"][0] . '.svg" alt="' . $to_lang . '" class="flag-icon">';
                            echo $title_array["$to_lang"][1];
                            ?>
                        </h1>
                        <div class="text-muted text-center">You are only one step away from learning
                            <?php echo $to_lang; ?>.</div>
                    </header>
                    <br />
                    <div id="error-msg" class="d-none"></div>
                    <form id="form_register">
                            <div class="form-group">
                                <label for="native_lang">Native language:</label>
                                <select name="native_lang" class="form-control custom-select" id="native_lang">
                                    <option value="en" selected>English</option>
                                    <option value="es">Spanish</option>
                                    <option value="pt">Portuguese</option>
                                    <option value="fr">French</option>
                                    <option value="it">Italian</option>
                                    <option value="de">German</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="learning_lang">Want to learn:</label>
                                <select name="learning_lang" class="form-control custom-select" id="learning_lang">
                                    <option value="en" <?php echo $to_lang=='English' ? 'selected' : '' ; ?>>English</option>
                                    <option value="es" <?php echo $to_lang=='Spanish' ? 'selected' : '' ; ?>>Spanish</option>
                                    <option value="pt" <?php echo $to_lang=='Portuguese' ? 'selected' : '' ; ?>>Portuguese</option>
                                    <option value="fr" <?php echo $to_lang=='French' ? 'selected' : '' ; ?>>French</option>
                                    <option value="it" <?php echo $to_lang=='Italian' ? 'selected' : '' ; ?>>Italian</option>
                                    <option value="de" <?php echo $to_lang=='German' ? 'selected' : '' ; ?>>German</option>
                                </select>
                            </div>
                        <div>
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" class="form-control" maxlength="20"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="email">E-mail address:</label>
                                <input type="email" id="email" name="email" class="form-control" maxlength="50"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <small>
                                    <i>at least 8 chars long (including letters, numbers &amp; special chars)</i>
                                </small>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control" pattern="(?=.*[0-9a-zA-Z])(?=.*[~`!@#$%^&*()\-_+={};:\[\]\?\.\/,]).{8,}" title="Password must contain a letter, a special character and a digit. Password length must be minimum 8 characters" autocomplete="off" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary show-hide-password-btn" type="button" tabindex="-1"><i class="fa fa-eye-slash" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                                <small id="password-strength-text"></small>
                            </div>
                            <div class="form-group">
                                <label for="password">Confirm password:</label>
                                <div class="input-group">
                                    <input type="password" id="password-confirmation" name="password-confirmation" class="form-control" pattern="(?=.*[0-9a-zA-Z])(?=.*[~`!@#$%^&*()\-_+={};:\[\]\?\.\/,]).{8,}" title="Password must contain a letter, a special character and a digit. Password length must be minimum 8 characters" autocomplete="off" required >
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary show-hide-password-btn" type="button" tabindex="-1"><i class="fa fa-eye-slash" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                                <small id="passwords-match-text"></small>
                            </div>
                            <button type="submit" id="btn_register" class="btn btn-block btn-success">Register</button>
                            <small>By registering, you declare to have read and accepted the <a href="privacy.php" target="_blank" rel="noopener noreferrer">privacy policy</a>.</small>    
                        </div>
                    </form>
                    <br />
                    <footer>
                        <p class="text-muted text-center">
                            Already have an account? <a href="login.php">Sign in</a>
                        </p>
                    </footer>
                </section>
            </div>
        </div>
    </div>
</div>

<script defer src="js/register.js"></script>

<?php require_once 'footer.php'?>