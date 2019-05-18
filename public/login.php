<?php
  require_once('../includes/dbinit.php'); // connect to database

  use Aprelendo\Includes\Classes\User;

  $user = new User($con);
  
  // if user is already logged in, go to "My Texts" section
  if ($user->isLoggedIn()) {
    header('Location:/texts.php');
    exit;
  }
?>

<?php require_once 'simpleheader.php'; ?>

<div class="pattern-wallpaper">
    <div class="container mtb">
        <div class="row">
            <div class="col-sm-12 col-md-10 offset-md-1 col-lg-6 offset-lg-3">
                <section>
                    <header>
                        <h3 class="text-center">Sign in</h3>
                    </header>
                    <br />
                    <div id="error-msg" class="d-none"></div>
                    <form action="" id="form_login">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" class="form-control" maxlength="20"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" class="form-control" pattern=".{8,}"
                                required>
                        </div>
                        <p>
                            <a href="forgotpassword.php">Forgot password</a>
                        </p>
                        <button type="submit" id="btn_login" class="btn btn-success">Log in</button>
                    </form>
                    <br />
                    <footer>
                        <p class="text-muted text-center font-italic">You are not registered? <a href="index.php">Create
                                an account</a>.</p>
                    </footer>
                </section>
            </div>
        </div>
    </div>
</div>


<?php require_once 'footer.php'?>

<script defer src="js/login.js"></script>