<?php
// SPDX-License-Identifier: GPL-3.0-or-later

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

$google_client_id = defined('GOOGLE_CLIENT_ID')
    ? GOOGLE_CLIENT_ID
    : '913422235077-082170c2l6b58ck8ie0f03rigombl2pc.apps.googleusercontent.com';

require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'simpleheader.php';
?>

<main class="auth-main flex-grow-1">
    <div class="container auth-shell">
        <div class="row g-3 g-xl-4 align-items-stretch">
            <div class="col-12 col-lg-4 order-2 order-lg-1">
                <section class="auth-side-panel auth-side-panel-compact">
                    <div class="auth-panel-body">
                        <p class="auth-side-eyebrow mb-3">Welcome back</p>
                        <h1 class="auth-side-title mb-3">Your texts, words and reviews are waiting.</h1>
                        <p class="auth-side-copy mb-0">
                            Log in to continue exactly where you left off, with your saved progress and language setup
                            ready to go.
                        </p>
                    </div>
                </section>
            </div>

            <div class="col-12 col-lg-8 order-1 order-lg-2">
                <section class="auth-form-card">
                    <div class="auth-panel-body">
                        <p class="auth-form-eyebrow mb-2">Log in</p>
                        <h2 class="auth-form-title mb-2">Continue where you left off</h2>
                        <p class="auth-form-copy mb-3">Sign in and get back to learning.</p>

                        <div id="alert-box" class="d-none"></div>

                        <form id="form_login" class="auth-form-stack">
                            <div>
                                <label for="username" class="form-label">Username</label>
                                <input type="text" id="username" name="username" class="form-control" maxlength="20"
                                    autocomplete="username" required>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="password" class="form-label mb-0">Password</label>
                                    <?php if (!IS_SELF_HOSTED): ?>
                                        <a href="/forgotpassword" class="auth-muted-link auth-inline-link">Forgot password?</a>
                                    <?php endif; ?>
                                </div>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control"
                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                        autocomplete="current-password" required>
                                    <button class="btn show-hide-password-btn" type="button"
                                        aria-label="Show/hide password" tabindex="-1">
                                        <span class="bi bi-eye-slash-fill" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" id="btn_login" class="btn btn-warning auth-primary-btn">
                                    <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>
                                    Log in
                                </button>
                            </div>
                        </form>

                        <?php if (!IS_SELF_HOSTED): ?>
                            <hr class="or-divider" data-text="Or continue with">
                            <div id="g_id_onload"
                                data-client_id="<?php echo htmlspecialchars($google_client_id, ENT_QUOTES, 'UTF-8'); ?>"
                                data-callback="googleLogIn">
                            </div>
                            <div class="google-btn-wrapper">
                                <div class="g_id_signin"
                                    data-type="standard"
                                    data-theme="outline"
                                    data-size="large"
                                    data-width="300">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="auth-footer-note text-center mt-3">
                            New to Aprelendo? <a href="/register" class="auth-muted-link">Create an account</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

<?php if (!IS_SELF_HOSTED): ?>
    <script src="/js/googlelogin.js"></script> <!-- Don't user "defer" for this one, otherwise google login won't work -->
<?php endif; ?>
<script defer src="/js/login.js"></script>
<script defer src="/js/password.js"></script>
<script defer src="/js/helpers.js"></script>

<?php require_once 'footer.php' ?>
