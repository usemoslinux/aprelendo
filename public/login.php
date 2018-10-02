<?php
  require_once('db/dbinit.php'); // connect to database
  require_once(PUBLIC_PATH . '/classes/users.php');

  $user = new User($con);
  
  // if user is already logged in, go to "My Texts" section
  if ($user->isLoggedIn()) {
    header('Location:/texts.php');
    exit;
  }
?>

<?php require_once 'simpleheader.php'; ?>

<div class="container mtb pattern-wallpaper">
  <div class="row">
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3">
      <section>
        <header>
          <h1 class="text-center">Sign in</h1>
        </header>
        <br/>
        <div id="error-msg" class="hidden"></div>
        <form action="" id="form_login">
          <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" maxlength="20" required>
          </div>
          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" pattern=".{8,}" required>
          </div>
          <p>
            <a href="forgotpassword.php">Forgot password</a>
          </p>
          <button type="submit" id="btn_login" class="btn btn-success">Log in</button>
        </form>
        <br/>
        <footer>
          <p class="text-muted text-center font-italic">You are not registered? <a href="chooselanguage.php">Create an account</a>.</p>
        </footer>
      </section>
    </div>
  </div>
</div>

<?php require_once 'footer.php'?>

<script type="text/javascript" src="js/login.js"></script>