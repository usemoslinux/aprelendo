<?php
require_once('../includes/dbinit.php'); // connect to database
require_once('simpleheader.php');
?>

<div class="container mtb pattern-wallpaper">
    <div class="row">
        <div class="col-sm-0 col-sm-1 col-lg-3"></div>
        <div class="col-sm-12 col-sm-10 col-lg-6">
            <section>
                <header>
                    <h1 class="text-center">Restore password</h1>
                </header>
                <br/>
                <div id="alert_msg" class="d-none"></div>

                <?php 
                // 1. check if username & password values passed by the reset link are set
                if(isset($_GET['username']) && isset($_GET['reset'])) {
                    $username = $con->escape_string($_GET['username']);
                    $password_hash = $con->escape_string($_GET['reset']);
                    
                    // check if username & password exist in db
                    $result = $con->query("SELECT userName, userPasswordHash FROM users WHERE userName='$username' AND userPasswordHash='$password_hash'");
                    
                    // 1.1. if username & password values passed by the reset link are found in db, then...
                    if ($result->num_rows > 0) { // 
                ?>

                <p>Enter your new password twice.</p>
                <form action="" id="form_create_new_password">
                    <input type="hidden" id="username" name="username" value="<?php echo $_GET['username']; ?>"> 
                    <div class="form-group">
                        <label for="pass1">New password:</label>
                        <small>
                            <i>at least 8 characters long</i>
                        </small>
                        <input type="password" id="pass1" name="pass1" class="form-control" pattern=".{8,}" required>
                    </div>
                    <div class="form-group">
                        <label for="pass2">Confirm password:</label>
                        <small>
                            <i>at least 8 characters long</i>
                        </small>
                        <input type="password" id="pass2" name="pass2" class="form-control" pattern=".{8,}" required>
                    </div>
                    <button type="submit" id="create_new_password" class="btn btn-success">Save new password</button>
                </form>

                <?php
                    } else { // 1.2. if username & password are set but not found in db, then ...
                ?>

                <div id="alert_msg" class="alert alert-danger">Can't reset user password. Possibly the recovery link has expired.</div>

                <?php 
                    }
                } else { // 2. if username & password are NOT set, show form to send the reset password link
                ?>

                <p>Enter your email address to receive a link to reset your password.</p>
                <form action="" id="form_forgot_password">
                    <div class="form-group">
                        <label for="email">E-mail address:</label>
                        <input type="email" id="email" name="email" class="form-control" maxlength="50" required>
                    </div>
                    <button type="submit" id="btn_forgot_password" class="btn btn-success">Request password</button>
                </form>

                <?php 
                }
                ?>

                <br/>
                <footer>
                </footer>
            </section>
        </div>
        <div class="col-sm-0 col-sm-1 col-lg-3"></div>
    </div>
</div>

<?php require_once 'footer.php'?>

<script src="js/forgotpassword.js"></script>