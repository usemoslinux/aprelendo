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
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'simpleheader.php';
?>

<div class="d-flex flex-grow-1 flex-column">
    <div class="container mtb">
        <div class="row">
            <div class="col-12 col-lg-6 offset-lg-3">
                <section>
                    <header>
                        <h1 class="text-center">
                            <?php
                            $title_array = [
                                'Arabic'    => ['ar', 'أهلا بك!'],
                                'Bulgarian' => ['bg', 'Добре дошли!'],
                                'Catalan'   => ['ca', 'Benvingut!'],
                                'Czech'     => ['cs', 'Vítejte!'],
                                'Danish'    => ['da', 'Velkommen!'],
                                'German'    => ['de', 'Willkommen!'],
                                'Greek'     => ['el', 'Καλώς ήρθατε!'],
                                'English'   => ['en', 'Welcome!'],
                                'Spanish'   => ['es', '¡Bienvenido!'],
                                'French'    => ['fr', 'Bienvenue!'],
                                'Hebrew'    => ['he', 'ברוך הבא!'],
                                'Hindi'     => ['hi', 'स्वागत है!'],
                                'Croatian'  => ['hr', 'Dobrodošli!'],
                                'Hungarian' => ['hu', 'Üdvözöljük!'],
                                'Italian'   => ['it', 'Benvenuto!'],
                                'Japanese'  => ['ja', 'ようこそ！'],
                                'Korean'    => ['ko', '환영합니다!'],
                                'Dutch'     => ['nl', 'Welkom!'],
                                'Norwegian' => ['no', 'Velkommen!'],
                                'Polish'    => ['pl', 'Witaj!'],
                                'Portuguese' => ['pt', 'Bem-vindo!'],
                                'Romanian'  => ['ro', 'Bun venit!'],
                                'Russian'   => ['ru', 'Добро пожаловать!'],
                                'Slovak'    => ['sk', 'Vitajte!'],
                                'Slovenian' => ['sl', 'Dobrodošli!'],
                                'Swedish'   => ['sv', 'Välkommen!'],
                                'Turkish'   => ['tr', 'Hoş geldiniz!'],
                                'Vietnamese' => ['vi', 'Chào mừng!'],
                                'Chinese'   => ['zh', '欢迎！']
                            ];

                            $to_lang = isset($_GET['tolang'])
                                ? htmlspecialchars(ucfirst($_GET['tolang']), ENT_QUOTES, 'UTF-8')
                                : 'English';

                            $native_lang = isset($_GET['srclang']) ? ucfirst($_GET['srclang']) : 'English';

                            echo '<img id="learning-flag" src="img/flags/'
                                . $title_array["$to_lang"][0]
                                . '.svg" alt="' . $to_lang . '"><br>';
                            echo $title_array["$to_lang"][1];
                            ?>
                        </h1>
                        <div id="welcome-msg" class="text-muted text-center">You are only one step away from learning
                            <?php echo $to_lang; ?>.</div>
                    </header>
                    <br>
                    <div id="alert-box" class="d-none"></div>
                    <form id="form-register">
                        <div class="mb-3">
                            <label for="native-lang">Native language:</label>
                            <select name="native-lang" class="form-control form-select" id="native-lang">
                                <option value="ar">Arabic</option>
                                <option value="bg">Bulgarian</option>
                                <option value="ca">Catalan</option>
                                <option value="zh">Chinese</option>
                                <option value="hr">Croatian</option>
                                <option value="cs">Czech</option>
                                <option value="da">Danish</option>
                                <option value="nl">Dutch</option>
                                <option value="en" selected>English</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                                <option value="el">Greek</option>
                                <option value="he">Hebrew</option>
                                <option value="hi">Hindi</option>
                                <option value="hu">Hungarian</option>
                                <option value="it">Italian</option>
                                <option value="ja">Japanese</option>
                                <option value="ko">Korean</option>
                                <option value="no">Norwegian</option>
                                <option value="pl">Polish</option>
                                <option value="pt">Portuguese</option>
                                <option value="ro">Romanian</option>
                                <option value="ru">Russian</option>
                                <option value="sk">Slovak</option>
                                <option value="sl">Slovenian</option>
                                <option value="es">Spanish</option>
                                <option value="sv">Swedish</option>
                                <option value="tr">Turkish</option>
                                <option value="vi">Vietnamese</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="learning-lang">Want to learn:</label>
                            <select name="learning-lang" class="form-control form-select" id="learning-lang">
                                <option value="ar" <?php echo $to_lang == 'Arabic'     ? 'selected' : ''; ?>>Arabic</option>
                                <option value="bg" <?php echo $to_lang == 'Bulgarian'  ? 'selected' : ''; ?>>Bulgarian</option>
                                <option value="ca" <?php echo $to_lang == 'Catalan'    ? 'selected' : ''; ?>>Catalan</option>
                                <option value="zh" <?php echo $to_lang == 'Chinese'    ? 'selected' : ''; ?>>Chinese</option>
                                <option value="hr" <?php echo $to_lang == 'Croatian'   ? 'selected' : ''; ?>>Croatian</option>
                                <option value="cs" <?php echo $to_lang == 'Czech'      ? 'selected' : ''; ?>>Czech</option>
                                <option value="da" <?php echo $to_lang == 'Danish'     ? 'selected' : ''; ?>>Danish</option>
                                <option value="nl" <?php echo $to_lang == 'Dutch'      ? 'selected' : ''; ?>>Dutch</option>
                                <option value="en" <?php echo $to_lang == 'English'    ? 'selected' : ''; ?>>English</option>
                                <option value="fr" <?php echo $to_lang == 'French'     ? 'selected' : ''; ?>>French</option>
                                <option value="de" <?php echo $to_lang == 'German'     ? 'selected' : ''; ?>>German</option>
                                <option value="el" <?php echo $to_lang == 'Greek'      ? 'selected' : ''; ?>>Greek</option>
                                <option value="he" <?php echo $to_lang == 'Hebrew'     ? 'selected' : ''; ?>>Hebrew</option>
                                <option value="hi" <?php echo $to_lang == 'Hindi'      ? 'selected' : ''; ?>>Hindi</option>
                                <option value="hu" <?php echo $to_lang == 'Hungarian'  ? 'selected' : ''; ?>>Hungarian</option>
                                <option value="it" <?php echo $to_lang == 'Italian'    ? 'selected' : ''; ?>>Italian</option>
                                <option value="ja" <?php echo $to_lang == 'Japanese'   ? 'selected' : ''; ?>>Japanese</option>
                                <option value="ko" <?php echo $to_lang == 'Korean'     ? 'selected' : ''; ?>>Korean</option>
                                <option value="no" <?php echo $to_lang == 'Norwegian'  ? 'selected' : ''; ?>>Norwegian</option>
                                <option value="pl" <?php echo $to_lang == 'Polish'     ? 'selected' : ''; ?>>Polish</option>
                                <option value="pt" <?php echo $to_lang == 'Portuguese' ? 'selected' : ''; ?>>Portuguese</option>
                                <option value="ro" <?php echo $to_lang == 'Romanian'   ? 'selected' : ''; ?>>Romanian</option>
                                <option value="ru" <?php echo $to_lang == 'Russian'    ? 'selected' : ''; ?>>Russian</option>
                                <option value="sk" <?php echo $to_lang == 'Slovak'     ? 'selected' : ''; ?>>Slovak</option>
                                <option value="sl" <?php echo $to_lang == 'Slovenian'  ? 'selected' : ''; ?>>Slovenian</option>
                                <option value="es" <?php echo $to_lang == 'Spanish'    ? 'selected' : ''; ?>>Spanish</option>
                                <option value="sv" <?php echo $to_lang == 'Swedish'    ? 'selected' : ''; ?>>Swedish</option>
                                <option value="tr" <?php echo $to_lang == 'Turkish'    ? 'selected' : ''; ?>>Turkish</option>
                                <option value="vi" <?php echo $to_lang == 'Vietnamese' ? 'selected' : ''; ?>>Vietnamese</option>
                            </select>
                        </div>
                        <div>
                            <div class="mb-3">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" class="form-control" maxlength="20" required>
                            </div>
                            <div class="mb-3">
                                <label for="email">E-mail address:</label>
                                <input type="email" id="email" name="email" class="form-control" maxlength="50" required>
                            </div>
                            <div class="mb-3">
                                <label for="newpassword">Password:</label>
                                <small>
                                    <em>at least 8 characters (including letters, digits &amp; special characters)</em>
                                </small>
                                <div class="input-group">
                                    <input type="password" id="newpassword" name="newpassword" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" autocomplete="off" required>
                                    <button class="btn btn-outline-secondary show-hide-password-btn" type="button" aria-label="Show/hide password" tabindex="-1"><span class="bi bi-eye-slash-fill" aria-hidden="true"></span></button>
                                </div>
                                <small id="password-strength-text"></small>
                            </div>
                            <div class="mb-3">
                                <label for="newpassword-confirmation">Confirm password:</label>
                                <div class="input-group">
                                    <input type="password" id="newpassword-confirmation" name="newpassword-confirmation" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" autocomplete="off" required>
                                    <button class="btn btn-outline-secondary show-hide-password-btn" type="button" aria-label="Show/hide password confirmation" tabindex="-1">
                                        <span class="bi bi-eye-slash-fill" aria-hidden="true"></span></button>
                                </div>
                                <small id="passwords-match-text"></small>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" id="btn_register" class="btn btn-success">Sign up</button>
                            </div>
                            <small>By registering, you declare to have read and accepted our <a href="/privacy" target="_blank" rel="noopener noreferrer">privacy policy</a>.</small>
                        </div>
                    </form>

                    <br>
                    <footer>
                        <p class="text-muted text-center">
                            Already have an account? <a href="/login">Sign in</a>
                        </p>
                    </footer>
                </section>
            </div>
        </div>
    </div>
</div>

<script defer src="/js/register.min.js"></script>
<script defer src="/js/password.min.js"></script>
<script defer src="/js/helpers.min.js"></script>

<?php require_once 'footer.php'; ?>
