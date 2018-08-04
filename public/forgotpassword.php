<?php
require_once('header.php');
?>

  <div class="container mtb nice-wallpaper-3">
    <div id="more_content" class="row">
      <div class="col-xs-0 col-sm-1 col-md-3"></div>
      <div class="col-xs-12 col-sm-10 col-md-6">
        <section>
          <header>
            <h1 class="text-center">Restore password</h1>
          </header>
          <br/>
          <div id="alert_msg" class="hidden"></div>
          <p>Enter your email address to receive a link to reset your password.</p>
          <form action="" id="form_forgot_password">
            <div class="form-group">
              <label for="email">E-mail address:</label>
              <input type="email" id="email" name="email" class="form-control" maxlength="50" required>
            </div>
            <button type="submit" id="btn_forgot_password" class="btn btn-success">Request password</button>
          </form>
          <br/>
          <footer>
          </footer>
        </section>
      </div>
      <div class="col-xs-0 col-sm-1 col-md-3"></div>
    </div>
  </div>

  <?php require_once 'footer.php'?>

  <script type="text/javascript" src="js/forgotpassword.js"></script>