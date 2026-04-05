<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

use Aprelendo\SupportedLanguages;
use Aprelendo\SecureEncryption;

$google_login = !empty($user->google_id);

?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/texts">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">User profile</span>
                    </li>
                </ol>
            </nav>
            <main>
                <div id="alert-box" class="d-none"></div>
                <form id="userprofile-form" class="" method="post">
                    <div class="card">
                        <div class="card-header">User details</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" class="form-control"
                                    value="<?php echo $user->name; ?>" maxlength="20" required>
                            </div>
                            <div class="mb-3 <?php echo $google_login ? 'disabled-card' : '' ?>">
                                <label for="email">E-mail address:</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?php echo $user->email; ?>" maxlength="50" required>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card <?php echo $google_login ? 'disabled-card' : '' ?>">
                        <div class="card-header">Password</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="password">Current password:</label>
                                <small>
                                    <em>8+ characters, with uppercase, lowercase, and a number.</em>
                                </small>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control"
                                        autocomplete="off" <?php echo $google_login ? '' : 'required' ?> >
                                    <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                        aria-label="Show/hide current password" tabindex="-1">
                                        <span class="bi bi-eye-slash-fill" aria-hidden="true"></span></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password">New password:</label>
                                <small>
                                    <em>8+ characters, with uppercase, lowercase, and a number.</em>
                                </small>
                                <div class="input-group">
                                    <input type="password" id="newpassword" name="newpassword" class="form-control"
                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" autocomplete="off">
                                    <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                        aria-label="Show/hide new password" tabindex="-1">
                                        <span class="bi bi-eye-slash-fill" aria-hidden="true"></span></button>
                                </div>
                                <small id="password-strength-text"></small>
                            </div>
                            <div class="mb-3">
                                <label for="password">Repeat new password:</label>
                                <div class="input-group">
                                    <input type="password" id="newpassword-confirmation" name="newpassword-confirmation"
                                        class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                        autocomplete="off">
                                    <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                        aria-label="Show/hide new password confirmation" tabindex="-1">
                                        <span class="bi bi-eye-slash-fill" aria-hidden="true"></span></button>
                                </div>
                                <small id="passwords-match-text"></small>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card">
                        <div class="card-header">Languages</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="src_lang">Your native language:</label>
                                <select name="src_lang" class="form-control form-select" id="src_lang">
                                    <?php
                                    $sort_by = 'name';
                                    $languages = SupportedLanguages::getAll($sort_by);
                                    $native_lang_iso = $user->native_lang;
                                    $html = '';

                                    foreach ($languages as $language) {
                                        $selected = ($language['ISO-639-1'] === $native_lang_iso) ? ' selected' : '';
                                        $html .= '<option value="' . $language['ISO-639-1'] . '"'
                                            . $selected . '>'
                                            . ucfirst($language['name']) . "</option>";
                                    }

                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="to_lang">Active learning language:</label>
                                <select name="to_lang" class="form-control form-select" id="to_lang">
                                    <?php
                                    $learning_lang_iso = $user->lang;
                                    $html = '';

                                    foreach ($languages as $language) {
                                        $selected = ($language['ISO-639-1'] === $learning_lang_iso) ? ' selected' : '';
                                        $html .= '<option value="' . $language['ISO-639-1'] . '"'
                                            . $selected . '>'
                                            . ucfirst($language['name']) . "</option>";
                                    }

                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card">
                        <div class="card-header">Lingobot</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="hf-token">Hugging Face API key:</label>
                                <div class="input-group">
                                    <input type="password" id="hf-token" name="hf-token" class="form-control"
                                        value="<?php
                                            $crypto = new SecureEncryption(ENCRYPTION_KEY);
                                            echo $crypto->decrypt($user->hf_token);
                                            ?>" maxlength="40" autocomplete="off">
                                    <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                        aria-label="Show/hide Hugging Face API key" tabindex="-1">
                                        <span class="bi bi-eye-slash-fill" aria-hidden="true"></span></button>
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="javascript:;" title="Help" data-bs-toggle="collapse"
                                    data-bs-target="#help-hf-api-key">Help
                                    <span class="bi bi-question-circle"></span>
                                </a>
                            </div>
                            <div id="help-hf-api-key" class="collapse small">
                                <hr>
                                <p>
                                    To use Lingobot, Aprelendo sends your question to Hugging Face and
                                    authenticates the request with the Hugging Face token you choose to save here.
                                    Using your own Hugging Face account keeps that access under your control: you can
                                    replace or revoke the token at any time if you suspect an issue. Hugging Face may
                                    also apply its own usage limits.
                                </p>

                                <p>Follow these steps to create a free Hugging Face account and generate an access
                                    token for Lingobot:</p>

                                <ol>
                                    <li>
                                        <p><strong>Create an account:</strong></p>
                                        <p>
                                            Visit <a href="https://huggingface.co/" target="_blank"
                                                rel="noopener noreferrer">https://huggingface.co/</a> and click on "Sign
                                            Up" to
                                            create an account. If you already have an account, log in.
                                        </p>
                                    </li>
                                    <li>
                                        <p><strong>Create an access token:</strong></p>
                                        <ol>
                                            <li>Click on your user icon in the top-right corner of the page.</li>
                                            <li>From the dropdown menu, select "Access tokens".</li>
                                            <li>Click the "<a
                                                    href="https://huggingface.co/settings/tokens/new?globalPermissions=inference.serverless.write&tokenType=fineGrained"
                                                    target="_blank" rel="noopener noreferrer">Create new token</a>"
                                                button.</li>
                                            <li>Switch to the "Fine-grained" tab.</li>
                                            <li>Under "Permissions", check only the option for "Make calls to the
                                                serverless Inference API".</li>
                                            <li>Give your token a name, such as <strong>"Lingobot"</strong>, and click
                                                the "Create" button.</li>
                                        </ol>
                                    </li>
                                    <li>
                                        <p><strong>Copy the token:</strong></p>
                                        <p>Once the token is created, copy it. Then, paste it into the "Hugging Face
                                            API key" text box above and click the "Save" button below. Your token is
                                            stored encrypted on our servers and used only to authenticate Lingobot
                                            requests made from your account. It is not exposed to other users, and you
                                            can remove or replace it here at any time. If you want to invalidate it
                                            completely, revoke it from your Hugging Face account.
                                        </p>
                                    </li>
                                </ol>

                                <p>You're all set! Now you can start asking Lingobot insightful questions about
                                    vocabulary pieces.</p>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="text-end">
                        <button type="button" id="btn-delete-account" name="deleteaccount"
                            class="btn btn-danger float-start">Delete
                            Account</button>
                        <button type="button" id="cancelbtn" name="cancel" class="btn btn-link"
                            onclick="window.location='/'">Cancel</button>
                        <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>

<!-- Modal window -->
<div id="delete-account-modal" class="modal fade" data-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Are you sure about this?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modal-alert-box" class="alert alert-warning">
                    <div class="alert-flag fs-5">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Careful
                    </div>
                    <div class="alert-msg">
                        Deleting your account is an irreversible action.
                    </div>
                </div>

                <p>Before you delete your account, be aware that all your profile information will be deleted from
                    our servers, thus your user name will be available to anyone else who is willing to subscribe.</p>
                <p>Also, all the files (e.g., epub files) you uploaded to our servers will be deleted, as well as your
                    word list and your private and shared texts libraries. This applies to all the languages you were
                    learning using Aprelendo.</p>
            </div>
            <div class="modal-footer">
                <button id="btn-cancel" type="button" data-bs-dismiss="modal" class="btn btn-link">Cancel</button>
                <button id="btn-confirm-delete-account" type="button" data-bs-dismiss="modal"
                    class="btn btn-danger">Delete Account</button>
            </div>
        </div>
    </div>
</div>

<script defer src="/js/userprofile.js"></script>
<script defer src="/js/password.js"></script>
<script defer src="/js/helpers.js"></script>
<script defer src="/js/tooltips.js"></script>

<?php require_once 'footer.php'; ?>
