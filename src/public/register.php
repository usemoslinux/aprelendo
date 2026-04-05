<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/dbinit.php'; // connect to database
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'simpleheader.php';
?>

<main class="auth-main flex-grow-1">
    <div class="container auth-shell">
        <div class="row g-3 g-xl-4 align-items-stretch">
            <div class="col-12 col-lg-5 order-2 order-lg-1">
                <section class="auth-side-panel">
                    <div class="auth-panel-body">
                        <?php
                        $title_array = [
                            'Arabic'    => 'أهلا بك!',
                            'Bulgarian' => 'Добре дошли!',
                            'Catalan'   => 'Benvingut!',
                            'Czech'     => 'Vítejte!',
                            'Danish'    => 'Velkommen!',
                            'German'    => 'Willkommen!',
                            'Greek'     => 'Καλώς ήρθατε!',
                            'English'   => 'Welcome!',
                            'Spanish'   => '¡Bienvenido!',
                            'French'    => 'Bienvenue!',
                            'Hebrew'    => 'ברוך הבא!',
                            'Hindi'     => 'स्वागत है!',
                            'Croatian'  => 'Dobrodošli!',
                            'Hungarian' => 'Üdvözöljük!',
                            'Italian'   => 'Benvenuto!',
                            'Japanese'  => 'ようこそ！',
                            'Korean'    => '환영합니다!',
                            'Dutch'     => 'Welkom!',
                            'Norwegian' => 'Velkommen!',
                            'Polish'    => 'Witaj!',
                            'Portuguese' => 'Bem-vindo!',
                            'Romanian'  => 'Bun venit!',
                            'Russian'   => 'Добро пожаловать!',
                            'Slovak'    => 'Vitajte!',
                            'Slovenian' => 'Dobrodošli!',
                            'Swedish'   => 'Välkommen!',
                            'Turkish'   => 'Hoş geldiniz!',
                            'Vietnamese' => 'Chào mừng!',
                            'Chinese'   => '欢迎！'
                        ];

                        $to_lang = htmlspecialchars(ucfirst($_GET['tolang'] ?? 'English'), ENT_QUOTES, 'UTF-8');
                        $welcome_title = $title_array[$to_lang] ?? $title_array['English'];
                        ?>

                        <p class="auth-side-eyebrow mb-3">Set your direction</p>
                        <h1 id="register-language-title" class="auth-side-title mb-3"><?php echo $welcome_title; ?></h1>
                        <p id="welcome-msg" class="auth-side-copy mb-4">You are only one step away from learning <?php echo $to_lang; ?>.</p>

                        <ul class="auth-feature-list mb-4">
                            <li>
                                <span class="bi bi-translate" aria-hidden="true"></span>
                                <div>
                                    <strong>Choose the right language pair</strong>
                                    <p>Set both your native language and target language before you begin.</p>
                                </div>
                            </li>
                            <li>
                                <span class="bi bi-lightning-charge" aria-hidden="true"></span>
                                <div>
                                    <strong>Start quickly</strong>
                                    <p>The setup stays short so you can get into reading and reviewing fast.</p>
                                </div>
                            </li>
                            <li>
                                <span class="bi bi-journal-text" aria-hidden="true"></span>
                                <div>
                                    <strong>Learn from real content</strong>
                                    <p>Use ebooks, web texts, and videos instead of isolated drills.</p>
                                </div>
                            </li>
                        </ul>

                        <p class="auth-side-caption mb-2">Popular choices</p>
                        <div class="auth-language-grid mb-3">
                            <button type="button" class="btn btn-secondary auth-language-pill<?php echo $to_lang == 'Arabic' ? ' is-active' : ''; ?>" data-learning-lang="ar">
                                <img src="img/flags/ar.svg" alt="Arabic">
                                Arabic
                            </button>
                            <button type="button" class="btn btn-secondary auth-language-pill<?php echo $to_lang == 'Chinese' ? ' is-active' : ''; ?>" data-learning-lang="zh">
                                <img src="img/flags/zh.svg" alt="Chinese">
                                Chinese
                            </button>
                            <button type="button" class="btn btn-secondary auth-language-pill<?php echo $to_lang == 'English' ? ' is-active' : ''; ?>" data-learning-lang="en">
                                <img src="img/flags/en.svg" alt="English">
                                English
                            </button>
                            <button type="button" class="btn btn-secondary auth-language-pill<?php echo $to_lang == 'French' ? ' is-active' : ''; ?>" data-learning-lang="fr">
                                <img src="img/flags/fr.svg" alt="French">
                                French
                            </button>
                            <button type="button" class="btn btn-secondary auth-language-pill<?php echo $to_lang == 'German' ? ' is-active' : ''; ?>" data-learning-lang="de">
                                <img src="img/flags/de.svg" alt="German">
                                German
                            </button>
                            <button type="button" class="btn btn-secondary auth-language-pill<?php echo $to_lang == 'Italian' ? ' is-active' : ''; ?>" data-learning-lang="it">
                                <img src="img/flags/it.svg" alt="Italian">
                                Italian
                            </button>
                            <button type="button" class="btn btn-secondary auth-language-pill<?php echo $to_lang == 'Japanese' ? ' is-active' : ''; ?>" data-learning-lang="ja">
                                <img src="img/flags/ja.svg" alt="Japanese">
                                Japanese
                            </button>
                            <button type="button" class="btn btn-secondary auth-language-pill<?php echo $to_lang == 'Korean' ? ' is-active' : ''; ?>" data-learning-lang="ko">
                                <img src="img/flags/ko.svg" alt="Korean">
                                Korean
                            </button>
                            <button type="button" class="btn btn-secondary auth-language-pill<?php echo $to_lang == 'Portuguese' ? ' is-active' : ''; ?>" data-learning-lang="pt">
                                <img src="img/flags/pt.svg" alt="Portuguese">
                                Portuguese
                            </button>
                            <button type="button" class="btn btn-secondary auth-language-pill<?php echo $to_lang == 'Russian' ? ' is-active' : ''; ?>" data-learning-lang="ru">
                                <img src="img/flags/ru.svg" alt="Russian">
                                Russian
                            </button>
                            <button type="button" class="btn btn-secondary auth-language-pill<?php echo $to_lang == 'Spanish' ? ' is-active' : ''; ?>" data-learning-lang="es">
                                <img src="img/flags/es.svg" alt="Spanish">
                                Spanish
                            </button>
                            <button type="button" class="btn btn-secondary auth-language-pill" data-focus-learning-lang="true">
                                <img src="img/flags/un.svg" alt="All languages">
                                +19 more
                            </button>
                        </div>

                        <p class="auth-side-support mb-0">Aprelendo is 100% free and open source. Registration is only needed to save your language setup, texts, reviews, and progress across sessions. It keeps your learning personalized and ready whenever you come back.</p>
                    </div>
                </section>
            </div>

            <div class="col-12 col-lg-7 order-1 order-lg-2">
                <section class="auth-form-card">
                    <div class="auth-panel-body">
                        <p class="auth-form-eyebrow mb-2">Create account</p>
                        <h2 class="auth-form-title mb-2">Start learning in about a minute</h2>
                        <p class="auth-form-copy mb-3">
                            Choose your languages, create your account, and start building active vocabulary from real content.
                        </p>

                        <div id="alert-box" class="d-none"></div>

                        <form id="form-register" class="auth-form-stack">
                            <section class="auth-section-card">
                                <div class="mb-3">
                                    <p class="auth-section-eyebrow mb-1">Step 1</p>
                                    <h3 class="auth-section-title">Choose your languages</h3>
                                    <p class="auth-section-copy mb-0">
                                        Set the language you already speak and the one you want to study.
                                    </p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label for="native-lang" class="form-label">I speak</label>
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

                                    <div class="col-12 col-md-6">
                                        <label for="learning-lang" class="form-label">I want to learn</label>
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
                                </div>
                            </section>

                            <section class="auth-section-card">
                                <div class="mb-3">
                                    <p class="auth-section-eyebrow mb-1">Step 2</p>
                                    <h3 class="auth-section-title">Create your account</h3>
                                    <p class="auth-section-copy mb-0">
                                        Add a few details so we can save your progress and sync it across devices.
                                    </p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" id="username" name="username" class="form-control"
                                            maxlength="20" autocomplete="username" required>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="email" class="form-label">E-mail address</label>
                                        <input type="email" id="email" name="email" class="form-control"
                                            maxlength="50" autocomplete="email" required>
                                    </div>

                                    <div class="col-12">
                                        <label for="newpassword" class="form-label">Password</label>
                                        <div class="input-group">
                                            <input type="password" id="newpassword" name="newpassword" class="form-control"
                                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                                autocomplete="new-password" required>
                                            <button class="btn show-hide-password-btn" type="button"
                                                aria-label="Show/hide password" tabindex="-1">
                                                <span class="bi bi-eye-slash-fill" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                        <small class="auth-password-help">8+ characters, with uppercase, lowercase, and a number.</small>
                                        <small id="password-strength-text"></small>
                                    </div>

                                    <div class="col-12">
                                        <label for="newpassword-confirmation" class="form-label">Confirm password</label>
                                        <div class="input-group">
                                            <input type="password" id="newpassword-confirmation"
                                                name="newpassword-confirmation" class="form-control"
                                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                                autocomplete="new-password" required>
                                            <button class="btn show-hide-password-btn" type="button"
                                                aria-label="Show/hide password confirmation" tabindex="-1">
                                                <span class="bi bi-eye-slash-fill" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                        <small id="passwords-match-text"></small>
                                    </div>
                                </div>
                            </section>

                            <div class="d-grid">
                                <button type="submit" id="btn_register" class="btn btn-warning auth-primary-btn">
                                    <i class="bi bi-person-plus me-2" aria-hidden="true"></i>
                                    Create account
                                </button>
                            </div>

                            <p class="auth-legal-copy mb-0">
                                By creating an account, you agree to our
                                <a href="/termsofservice" target="_blank" rel="noopener noreferrer" class="auth-muted-link">
                                    Terms of Service
                                </a>
                                and acknowledge our
                                <a href="/privacy" target="_blank" rel="noopener noreferrer" class="auth-muted-link">
                                    Privacy Policy
                                </a>.
                            </p>
                        </form>

                        <div class="auth-footer-note text-center mt-3">
                            Already have an account? <a href="/login" class="auth-muted-link">Log in</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

<script defer src="/js/register.js"></script>
<script defer src="/js/password.js"></script>
<script defer src="/js/helpers.js"></script>

<?php require_once 'footer.php'; ?>
