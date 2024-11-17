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
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

use Aprelendo\Language;
use Aprelendo\SecureEncryption;

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
                            <div class="mb-3">
                                <label for="email">E-mail address:</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?php echo $user->email; ?>" maxlength="50" required>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card">
                        <div class="card-header">Password</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="password">Current password:</label>
                                <small>
                                    <em>at least 8 characters (including letters, digits &amp; special characters)</em>
                                </small>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control"
                                        autocomplete="off" required>
                                    <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                        aria-label="Show/hide current password" tabindex="-1">
                                        <span class="bi bi-eye-slash-fill" aria-hidden="true"></span></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password">New password:</label>
                                <small>
                                    <em>at least 8 characters (including letters, digits &amp; special characters)</em>
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
                                    $iso_codes = Language::getIsoCodeArray();
                                    $native_lang_iso = $user->native_lang;
                                    $html = '';

                                    foreach ($iso_codes as $iso_code => $iso_name) {
                                        $selected = ($iso_code === $native_lang_iso) ? ' selected' : '';
                                        $html .= '<option value="' . $iso_code . '"'
                                            . $selected . '>'
                                            . ucfirst($iso_name) . "</option>";
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

                                    foreach ($iso_codes as $iso_code => $iso_name) {
                                        $selected = ($iso_code === $learning_lang_iso) ? ' selected' : '';
                                        $html .= '<option value="' . $iso_code . '"'
                                            . $selected . '>'
                                            . ucfirst($iso_name) . "</option>";
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
                                            ?>" maxlength="40" autoco" maxlength="40" autocomplete="off">
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
                                    To use Aprelendo's AI features (i.e. Lingobot), we rely on Hugging Face's
                                    powerful servers. By creating your own Hugging Face account, you ensure that AI
                                    usage remains free while giving you full control over your access. This means you
                                    can add or remove tokens anytime if you suspect an issue. The only drawback is that
                                    Hugging Face may set a limit on your AI usage. This setup provides transparency and
                                    security for all users.
                                </p>

                                <p>Follow these steps to create a Hugging Face account and generate an access token for
                                    Lingobot:</p>

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
                                            API key" text box above and click the "Save" button below. Your token will
                                            be stored in our servers using strong encryption and will not be shared
                                            with anyone.
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
<aside id="delete-account-modal" class="modal fade" data-keyboard="true" role="dialog">
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
</aside>

<script defer src="/js/userprofile.min.js"></script>
<script defer src="/js/password.min.js"></script>
<script defer src="/js/helpers.min.js"></script>
<script defer src="/js/tooltips.min.js"></script>

<?php require_once 'footer.php'; ?>