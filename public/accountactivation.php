<?php
require_once('db/dbinit.php'); // connect to database
require_once(PUBLIC_PATH . 'classes/users.php'); // loads User class
require_once(PUBLIC_PATH . 'simpleheader.php');
?>

<div class="container mtb pattern-wallpaper">
    <div class="row">
        <div class="col-xs-0 col-sm-1 col-md-3"></div>
        <div class="col-xs-12 col-sm-10 col-md-6">
            <section>
                <header>
                    <h1 class="text-center">Account activation</h1>
                </header>
                <br />
                <div id="alert_msg" class="hidden"></div>

                <?php 
                // 1. check if username & hash values passed by the reset link are set
                if (isset($_GET['username']) && !empty($_GET['username']) && isset($_GET['hash']) && !empty($_GET['hash'])) :
                ?>

                <?php 
                $user = new User($con);
                if ($user->activate($_GET['username'], $_GET['hash'])) : ?>
                
                <div id="alert_msg_2" class="alert alert-success">Congratulations! Your account is now active.</div>

                <p>You can now login with the username and password you provided when you signed up.</p>

                <div class="text-center">
                    <a href="login.php" class="btn btn-lg btn-success">Login now</a>
                </div>

                <br/>
                <br/>
                
                <?php
                else : // activation failed
                ?>

                <div id="alert_msg_2" class="alert alert-danger">Your account activation failed.</div>
               
                <?php
                endif; 
                else : // $_GET parameters not set or empty
                ?>

                <div id="alert_msg_2" class="alert alert-danger">The activation link seems to be malformed. Please try again using the one provided in the email we've sent you.</div>
                
                <?php 
                endif;
                ?>

                <br />
                <footer>
                </footer>
            </section>
        </div>
        <div class="col-xs-0 col-sm-1 col-md-3"></div>
    </div>
</div>

<?php require_once 'footer.php'?>