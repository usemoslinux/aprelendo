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
require_once APP_ROOT . 'includes/checklogin.php'; // check if logged in and set $user
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

use Aprelendo\Includes\Classes\Language;

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
                <div id="msgbox"></div>
                <form id="userprofile-form" class="" method="post">
                    <div class="card">
                        <div class="card-header">User details</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" class="form-control"
                                    value="<?php echo $user->getName();?>" maxlength="20" required>
                            </div>
                            <div class="form-group">
                                <label for="email">E-mail address:</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?php echo $user->getEmail();?>" maxlength="50" required>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card">
                        <div class="card-header">Password</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="password">Current password:</label>
                                <small>
                                    <i>at least 8 characters (including letters, numbers &amp; special characters)</i>
                                </small>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control"
                                        title="Password must contain a letter, a special character and a digit. Password length must be minimum 8 characters"
                                        autocomplete="off" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                            aria-label="Show/hide current password" tabindex="-1"><i
                                                class="fas fa-eye-slash" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password">New password:</label>
                                <small>
                                    <i>at least 8 characters (including letters, numbers &amp; special characters)</i>
                                </small>
                                <div class="input-group">
                                    <input type="password" id="newpassword" name="newpassword" class="form-control"
                                        pattern="(?=.*[0-9a-zA-Z])(?=.*[~`!@#$%^&*()\-_+={};:\[\]\?\.\/,]).{8,}"
                                        title="Password must contain a letter, a special character and a digit. Password length must be minimum 8 characters"
                                        autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                            aria-label="Show/hide new password" tabindex="-1"><i
                                                class="fas fa-eye-slash" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                                <small id="password-strength-text"></small>
                            </div>
                            <div class="form-group">
                                <label for="password">Repeat new password:</label>
                                <div class="input-group">
                                    <input type="password" id="newpassword-confirmation" name="newpassword-confirmation"
                                        class="form-control"
                                        pattern="(?=.*[0-9a-zA-Z])(?=.*[~`!@#$%^&*()\-_+={};:\[\]\?\.\/,]).{8,}"
                                        title="Password must contain a letter, a special character and a digit. Password length must be minimum 8 characters"
                                        autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary show-hide-password-btn" type="button"
                                            aria-label="Show/hide new password confirmation" tabindex="-1"><i
                                                class="fas fa-eye-slash" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                                <small id="passwords-match-text"></small>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card">
                        <div class="card-header">Languages</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="src_lang">Your native language:</label>
                                <select name="src_lang" class="form-control custom-select" id="src_lang">
                                    <?php $native_lang_index = Language::getIndex($user->getNativeLang()); ?>
                                    <option value="en" <?php echo $native_lang_index==0 ? ' selected ' : '' ; ?>>English
                                    </option>
                                    <option value="es" <?php echo $native_lang_index==1 ? ' selected ' : '' ; ?>>Spanish
                                    </option>
                                    <option value="pt" <?php echo $native_lang_index==2 ? ' selected ' : '' ; ?>>
                                        Portuguese</option>
                                    <option value="fr" <?php echo $native_lang_index==3 ? ' selected ' : '' ; ?>>French
                                    </option>
                                    <option value="it" <?php echo $native_lang_index==4 ? ' selected ' : '' ; ?>>Italian
                                    </option>
                                    <option value="de" <?php echo $native_lang_index==5 ? ' selected ' : '' ; ?>>German
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="to_lang">Active learning language:</label>
                                <select name="to_lang" class="form-control custom-select" id="to_lang">
                                    <?php $lang_index = Language::getIndex($user->getLang()); ?>
                                    <option value="en" <?php echo $lang_index==0 ? ' selected ' : '' ; ?>>English
                                    </option>
                                    <option value="es" <?php echo $lang_index==1 ? ' selected ' : '' ; ?>>Spanish
                                    </option>
                                    <option value="pt" <?php echo $lang_index==2 ? ' selected ' : '' ; ?>>Portuguese
                                    </option>
                                    <option value="fr" <?php echo $lang_index==3 ? ' selected ' : '' ; ?>>French
                                    </option>
                                    <option value="it" <?php echo $lang_index==4 ? ' selected ' : '' ; ?>>Italian
                                    </option>
                                    <option value="de" <?php echo $lang_index==5 ? ' selected ' : '' ; ?>>German
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="text-right">
                        <button type="button" id="btn-delete-account" name="deleteaccount"
                            class="btn btn-danger float-left">Delete
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
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Are you sure about this?</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="alert alert-danger">Deleting you account is an irreversible action.</p>
                <p>Before you delete your account, be aware that all your profile information will be deleted from our servers, thus your user name will be available to anyone else who is willing to subscribe.</p>
                <p>Also, all the files (e.g., epub files) you uploaded to our servers will be deleted, as well as your word list and your private and shared texts libraries. This applies to all the languages you were learning using Aprelendo.</p>
            </div>
            <div class="modal-footer">
            <button id="btn-cancel" type="button" data-dismiss="modal" class="btn btn-link">Cancel</button>
                <button id="btn-confirm-delete-account" type="button" data-dismiss="modal" class="btn btn-danger">Delete Account</button>
            </div>
        </div>
    </div>
</aside>

<script defer src="js/userprofile-min.js"></script>
<script defer src="js/password-min.js"></script>

<?php require_once 'footer.php'; ?>