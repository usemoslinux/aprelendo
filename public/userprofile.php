<?php
    require_once('header.php');
?>

    <div class="container mtb">
        <div class="row">
            <div class="col-xs-12">
                <ol class="breadcrumb">
                    <li>
                        <a href="texts.php">Home</a>
                    </li>
                    <li>
                        <a class="active">User profile</a>
                    </li>
                </ol>
                <div id="msgbox"></div>
                <form id="userprofile-form" class="" action="" method="post">
                    <div class="panel panel-default">
                        <div class="panel-heading">User details</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" class="form-control" value="<?php echo $user->name;?>" maxlength="20" required>
                            </div>
                            <div class="form-group">
                                <label for="email">E-mail address:</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo $user->email;?>" maxlength="50" required>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">Password</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="password">Current password:</label>
                                <small>
                                    <i>at least 8 characters long</i>
                                </small>
                                <input type="password" id="password" name="password" class="form-control" pattern=".{8,}" required>
                            </div>
                            <div class="form-group">
                                <label for="password">New password:</label>
                                <small>
                                    <i>at least 8 characters long</i>
                                </small>
                                <input type="password" id="newpassword1" name="newpassword1" class="form-control" pattern=".{8,}">
                            </div>
                            <div class="form-group">
                                <label for="password">Repeat new password:</label>
                                <small>
                                    <i>at least 8 characters long</i>
                                </small>
                                <input type="password" id="newpassword2" name="newpassword2" class="form-control" pattern=".{8,}">
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">Languages</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="src_lang">Your native language:</label>
                                <select name="src_lang" class="form-control" id="src_lang">
                                    <?php $native_lang_index = $user->getLanguageIndex($user->native_lang); ?>
                                    <option value="en" <?php echo $native_lang_index==0 ? ' selected ' : ''; ?>>English</option>
                                    <option value="es" <?php echo $native_lang_index==1 ? ' selected ' : ''; ?>>Spanish</option>
                                    <option value="pt" <?php echo $native_lang_index==2 ? ' selected ' : ''; ?>>Portuguese</option>
                                    <option value="fr" <?php echo $native_lang_index==3 ? ' selected ' : ''; ?>>French</option>
                                    <option value="it" <?php echo $native_lang_index==4 ? ' selected ' : ''; ?>>Italian</option>
                                    <option value="de" <?php echo $native_lang_index==5 ? ' selected ' : ''; ?>>German</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="to_lang">Active learning language:</label>
                                <select name="to_lang" class="form-control" id="to_lang">
                                    <?php $learning_lang_index = $user->getLanguageIndex($user->learning_lang); ?>
                                    <option value="en" <?php echo $learning_lang_index==0 ? ' selected ' : ''; ?>>English</option>
                                    <option value="es" <?php echo $learning_lang_index==1 ? ' selected ' : ''; ?>>Spanish</option>
                                    <option value="pt" <?php echo $learning_lang_index==2 ? ' selected ' : ''; ?>>Portuguese</option>
                                    <option value="fr" <?php echo $learning_lang_index==3 ? ' selected ' : ''; ?>>French</option>
                                    <option value="it" <?php echo $learning_lang_index==4 ? ' selected ' : ''; ?>>Italian</option>
                                    <option value="de" <?php echo $learning_lang_index==5 ? ' selected ' : ''; ?>>German</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <a type="button" id="cancelbtn" name="cancel" class="btn btn-static" onclick="window.location='/'">Cancel</a>
                        <button type="submit" id="savebtn" name="submit" class="btn btn-success">Save</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>

    <?php require_once('footer.php') ?>

    <script type="text/javascript" src="js/userprofile.js"></script>